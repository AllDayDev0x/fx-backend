<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\PromoCode, App\Models\User, App\Models\UserPromoCode;

use Carbon\Carbon;

use App\Helpers\Helper;

class PromocodeApiController extends Controller
{
    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method promo_code_index()
     *
     * @uses To display all the promocode
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function promo_code_index(Request $request) {

        try {

            $current_date = Carbon::now()->format('Y-m-d');

            $base_query = $total_query = PromoCode::whereDate('promo_codes.expiry_date','>=', $current_date)->where('user_id',$request->id)->orderBy('promo_codes.created_at', 'desc');

            $promocode = $base_query->skip($this->skip)->take($this->take)->get();

            $data['total'] = $total_query->count() ?? 0;

            $data['promocode'] = $promocode ?? [];

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method promo_code_save()
     *
     * @uses To save the promocode
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id, promo code
     *
     * @return JSON Response
     */
    public function promo_code_save(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'title' => 'required|max:191',
                'promo_code' => $request->promo_code_id ? 'required|max:10|min:1|unique:promo_codes,promo_code,'.$request->promo_code_id : 'required|unique:promo_codes,promo_code|min:1|max:10',
                'amount_type' => 'required',
                'description' => 'max:350',
                'amount' => 'required|numeric|gt:0',
                'start_date' => 'required|after:now',
                'expiry_date' => 'required|after:start_date',
                'no_of_users_limit' => 'required',
                'per_users_limit' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('id',$request->id)->where('is_content_creator',CONTENT_CREATOR)->first();

            if(!$user){

                throw new Exception(api_error(135), 135);

            }

            $promo_code = new PromoCode;

            $success_code = 233;

            if( $request->promo_code_id != '') {

                $promo_code = PromoCode::where('id',$request->promo_code_id)->first();

                if($promo_code){

                    $success_code = 232;

                }else{

                    throw new Exception(api_error(237), 237);

                }

            } else {
               
                $promo_code->status = APPROVED;
            }

            $promo_code->title = $request->title ?: $promo_code->title;

            $promo_code->description = $request->description ?: '';

            $promo_code->promo_code = $request->promo_code ?: $promo_code->promo_code;

            $promo_code->amount_type = $request->amount_type ?: $promo_code->amount_type;

            $promo_code->amount = $request->amount?: $promo_code->amount;

            $promo_code->start_date = $request->start_date ? common_server_date($request->start_date, $this->timezone, 'Y-m-d H:i:s') : $promo_code->start_date;

            $promo_code->expiry_date = $request->expiry_date ? common_server_date($request->expiry_date, $this->timezone, 'Y-m-d H:i:s') : $promo_code->expiry_date;

            $promo_code->user_id = $request->id ?? $promo_code->user_id;

            $promo_code->no_of_users_limit = $request->no_of_users_limit ?? $promo_code->no_of_users_limit;

            $promo_code->per_users_limit = $request->per_users_limit ?? $promo_code->per_users_limit;

            if( $promo_code->save()) {

                DB::commit();

                $data['user'] = $this->loginUser;

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            }else{

                throw new Exception(api_error(128), 128);

            }

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method promo_code_delete()
     *
     * @uses To display all the promocode
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function promo_code_delete(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'promo_code_id' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $promo_code = PromoCode::where('id',$request->promo_code_id)->where('user_id',$request->id);

            if(!$promo_code) {

                throw new Exception(api_error(233), 233);                
            }

            if($promo_code->delete() ) {

                DB::commit();

                return $this->sendResponse(api_success(234), 234);

            }else{

                throw new Exception(api_error(128), 128);

            }


        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

}
