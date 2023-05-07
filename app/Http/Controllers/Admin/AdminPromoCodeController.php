<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor, File;

use App\Models\PromoCode, App\Models\User, App\Models\UserPromoCode;

use App\Helpers\Helper;

class AdminPromoCodeController extends Controller
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:admin');

        $this->skip = $request->skip ?: 0;
       
        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method promo_codes_index()
     *
     * @uses To list out Promo codes details.
     *
     * @created Arun
     *
     * @updated 
     *
     * @param -
     *
     * @return view page
     */    

    public function promo_codes_index(Request $request) {

        $base_query = PromoCode::orderBy('created_at','desc');

        if($request->search_key) {

            $base_query = $base_query
                    ->orWhere('title','LIKE','%'.$request->search_key.'%')
                    ->orWhere('promo_code','LIKE','%'.$request->search_key.'%');
        }

        $promo_codes = $base_query->paginate($this->take);

        return view('admin.promo_codes.index')
                    ->with('page','promo_codes')
                    ->with('sub_page','promo_codes-view')
                    ->with('promo_codes' , $promo_codes);
    }

    /**
     * @method promo_codes_create()
     *
     * @uses To create service location object
     *
     * @created Arun
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     */

    public function promo_codes_create() {
        
        $promo_code = new PromoCode;

        $users = User::Approved()->get();
       
        return view('admin.promo_codes.create')
                    ->with('page' , 'promo_codes')
                    ->with('sub_page','promo_codes-create')
                    ->with('users', $users)
                    ->with('promo_code', $promo_code);
    }

    /**
     * @method promo_codes_save()
     *
     * @uses To save/update the new/existing promo_codes object details
     *
     * @created Arun
     *
     * @updated
     *
     * @param integer (request) $promo_code_id, promo_code (request) details
     * 
     * @return success/failure message
     *
     */
    
    public function promo_codes_save(Request $request) {
       
        try {
           
            DB::beginTransaction();

            $validator = [
                'title' => 'required|max:191',
                'promo_code' => $request->promo_code_id ? 'required|max:10|min:1|unique:promo_codes,promo_code,'.$request->promo_code_id : 'required|unique:promo_codes,promo_code|min:1|max:10',
                'amount_type' => 'required',
                'description' => 'max:350',
                'amount' => 'required|numeric',
                'start_date' => 'required|after:now',
                'expiry_date' => 'required|after:start_date',
                'no_of_users_limit' => 'required',
                'per_users_limit' => 'required',
            ];
            
            Helper::custom_validator($request->all(),$validator);
            
            $promo_code = new PromoCode;

            $message = tr('promo_code_created_success');

            if( $request->promo_code_id != '') {

                $promo_code = PromoCode::find($request->promo_code_id);

                $user = User::find($promo_code->user_id);

                $message = tr('promo_code_updated_success', $user->name ?? '');

            } else {
               
                $promo_code->status = APPROVED;
            }

            $promo_code->title = $request->title;

            $promo_code->description = $request->description ?: '';

            $promo_code->promo_code = $request->promo_code;

            $promo_code->amount_type = $request->amount_type;

            $promo_code->amount = $request->amount;

            $promo_code->start_date = $request->start_date;

            $promo_code->expiry_date = $request->expiry_date;

            $promo_code->user_id = $request->user_id ?? 0;

            $promo_code->no_of_users_limit = $request->no_of_users_limit ?? 0;

            $promo_code->per_users_limit = $request->per_users_limit ?? 0;

            if( $promo_code->save()) {
                
                DB::commit();

                return redirect()->route('admin.promo_codes.view',['promo_code_id' => $promo_code->id])->with('flash_success',$message);
            }
            
        } catch (Exception $e) {
            
            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());
        }

    }

    /**
     * @method promo_codes_view()
     *
     * @uses display promo_code details based on promo_code id
     *
     * @created Arun
     *
     * @updated 
     *
     * @param integer (request) $promo_code_id
     * 
     * @return view page
     *
     */
    public function promo_codes_view(Request $request) {

        $promo_code = PromoCode::find($request->promo_code_id);

        $promo_code_used = UserPromoCode::where('promo_code',$promo_code->promo_code)->sum('no_of_times_used');

        if(!$promo_code) {

            throw new Exception(tr('promo_code_not_found'), 101);
            
        }

        return view('admin.promo_codes.view')
                    ->with('page', 'promo_codes')
                    ->with('sub_page','promo_codes-view')
                    ->with('promo_code' , $promo_code)
                    ->with('promo_code_used' , $promo_code_used);
    
    }

    /**
     * @method promo_codes_edit
     *
     * @uses To update promo_code based on id
     *
     * @created Arun
     *
     * @updated
     *
     * @param integer (request) $promo_code_id
     * 
     * @return view page
     *
     */    
    public function promo_codes_edit(Request $request) {

        try {

            $promo_code = PromoCode::find($request->promo_code_id);

            $users = User::Approved()->get();

            if(!$promo_code) {

                throw new Exception(tr('promo_code_not_found'), 101);
                
            }

            return view('admin.promo_codes.edit')
                        ->with('page','promo_codes')
                        ->with('sub_page','promo_codes-view')
                        ->with('users',$users)
                        ->with('promo_code',$promo_code);

       } catch (Exception $e) {

            return redirect()->route('admin.promo_codes.index')->with('flash_error', $e->getMessage());
       }
    
    }

    /**
     * @method promo_codes_status
     *
     * @uses To update promo_code status as DECLINED/APPROVED based on promo_code id
     *
     * @created Arun
     *
     * @updated 
     *
     * @param integer (request) $promo_code_id
     * 
     * @return success/failure message
     *
     */
    public function promo_codes_status(Request $request) {

        try {

            DB::beginTransaction();

            $promo_code = PromoCode::find($request->promo_code_id);

            if(!$promo_code) {

                throw new Exception(tr('promo_code_not_found'), 101);                
            }

            $promo_code->status = $promo_code->status ? DECLINED : APPROVED;

            if($promo_code->save()) {

                DB::commit();

                $message = $promo_code->status ? tr('promo_code_approve_success') : tr('promo_code_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }

            throw new Exception(tr('promo_code_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.promo_codes.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method promo_codes_delete
     *
     * @uses To delete the promo_code details based on promo_code id
     *
     * @created Arun
     *
     * @updated 
     *
     * @param integer (request) $promo_code_id
     * 
     * @return success/failure message
     *
     */
    public function promo_codes_delete(Request $request) {

        try {

            DB::beginTransaction();

            $promo_code = PromoCode::find($request->promo_code_id);

            if(!$promo_code) {

                throw new Exception(tr('promo_code_not_found'), 101);                
            }

            if($promo_code->delete() ) {

                DB::commit();

                return redirect()->route('admin.promo_codes.index')->with('flash_success',tr('promo_code_deleted_success'));

            }

            throw new Exception(tr('promo_code_delete_error'));
            
        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.promo_codes.index')->with('flash_error', $e->getMessage());

        }
   
    }
}
