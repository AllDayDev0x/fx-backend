<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

class SupportMemberController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request) {

        $this->middleware('auth:support_member');

        $this->skip = $request->skip ?: 0;
       
        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

    }

    /**
     * @method dashboard()
     *
     * @uses  Display the analytics for the support member
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */
    public function dashboard() {

        return view('support_member.dashboard');
    }

     /**
     * @method support_tickets_index()
     *
     * @uses Display the lists of support tickets
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */
    public function support_tickets_index(Request $request) {

        $support_tickets = \App\Models\SupportTicket::orderBy('created_at','DESC')->paginate($this->take);

        return view('support_member.support_tickets.index')
                    ->with('page', 'support_tickets')
                    ->with('sub_page', 'support_tickets-view')
                    ->with('support_tickets', $support_tickets);
    }

    /**
     * @method support_tickets_view()
     *
     * @uses displays the specified support tickets details based on support ticket id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request -  Support Ticket Id
     * 
     * @return View page
     *
     */
    public function support_tickets_view(Request $request) {
       
        try {
      
            $support_ticket = \App\Models\SupportTicket::find($request->support_ticket_id);

            if(!$support_ticket) { 

                throw new Exception(tr('support_ticket_not_found'), 101);                
            }
        
            return view('support_member.support_tickets.view')
                        ->with('page', 'support_tickets') 
                        ->with('sub_page','support_tickets-view') 
                        ->with('support_ticket', $support_ticket);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method profile()
     *
     * @uses  Used to display the account details of support member
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */

    public function profile() {

        $support_member = Auth::guard('support_member')->user();

        return view('support_member.account.profile')
                ->with('page', 'profile')
                ->with('support_member',$support_member);
    }


    /**
     * @method profile_save()
     *
     * @uses To update the support member details
     *
     * @created Akshata
     *
     * @updated
     *
     * @param -
     *
     * @return view page 
     */

    public function profile_save(Request $request) {

        try {
        	
            DB::beginTransaction();

            $rules = 
                [
                    'name' => 'max:191',
                    'email' => $request->support_member_id ? 'email|max:191|unique:support_members,email,'.$request->support_member_id : 'email|max:191|unique:support_members,email,NULL',
                    'support_member_id' => 'required|exists:support_members,id',
                    'picture' => 'mimes:jpeg,jpg,png'
                ];
            
            Helper::custom_validator($request->all(),$rules);
            
            $support_member = \App\Models\SupportMember::find($request->support_member_id);

            if(!$support_member) {

                Auth::guard('support_member')->logout();

                throw new Exception(tr('support_member_not_found'), 101);
            }
        
            $support_member->name = $request->name ?: $support_member->name;

            $support_member->email = $request->email ?: $support_member->email;

            $support_member->mobile = $request->mobile ?: $support_member->mobile;

            if($request->hasFile('picture') ) {
                
                Helper::storage_delete_file($support_member->picture, SUPPORT_MEMBER_FILE_PATH); 
                
                $support_member->picture = Helper::storage_upload_file($request->file('picture'), SUPPORT_MEMBER_FILE_PATH);
            }

            $support_member->save();

            DB::commit();

            return redirect()->route('support_member.profile')->with('flash_success', tr('support_member_profile_success'));


        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error' , $e->getMessage());

        }    
    
    }

    /**
     * @method change_password()
     *
     * @uses To change the support member password
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */

    public function change_password(Request $request) {

        try {

            DB::begintransaction();

            $rules = 
            [              
                'password' => 'required|confirmed|min:6',
                'old_password' => 'required',
            ];
            
            Helper::custom_validator($request->all(),$rules);

            $support_member = \App\Models\SupportMember::find(Auth::guard('support_member')->user()->id);

            if(!$support_member) {

                Auth::guard('support_member')->logout();
                              
                throw new Exception(tr('support_member_not_found'), 101);

            }

            if(Hash::check($request->old_password,$support_member->password)) {

                $support_member->password = Hash::make($request->password);

                $support_member->save();

                DB::commit();

                Auth::guard('support_member')->logout();

                return redirect()->route('support_member.login')->with('flash_success', tr('password_change_success'));
                
            } else {

                throw new Exception(tr('password_mismatch'));
            }

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error' , $e->getMessage());

        }    
    
    }

     /**
     * @method support_tickets_chat()
     *
     * @uses This page is to display the chat for the support member
     *
     * @created Akshata
     *
     * @updated
     *
     * @param 
     *
     * @return view page 
     */

    public function support_tickets_chat(){

        $support_chats = \App\Models\SupportChat::where('status',APPROVED)->paginate($this->take);

        return view('support_member.support_tickets.chat')
                ->with('page','support_tickets-view')
                ->with('support_chats',$support_chats);
    }

}
