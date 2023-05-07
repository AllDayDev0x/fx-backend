<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

class AdminSupportMemberController extends Controller
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
     * @method support_members_index()
     *
     * @uses To list out support_members details 
     *
     * @created 
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function support_members_index(Request $request) {
        
        $base_query = \App\Models\SupportMember::orderBy('created_at','desc');

        if($request->search_key) {

            $base_query = $base_query
                    ->where('support_members.name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('support_members.email','LIKE','%'.$request->search_key.'%')
                    ->orWhere('support_members.mobile','LIKE','%'.$request->search_key.'%');
        }

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_APPROVED:
                    $base_query = $base_query->where('support_members.status', SUPPORT_MEMBER_APPROVED);
                    break;

                case SORT_BY_DECLINED:
                    $base_query = $base_query->where('support_members.status', SUPPORT_MEMBER_DECLINED);
                    break;

                case SORT_BY_EMAIL_VERIFIED:
                    $base_query = $base_query->where('support_members.is_email_verified',SUPPORT_MEMBER_EMAIL_VERIFIED);
                    break;
                
                default:
                    $base_query = $base_query->where('support_members.is_email_verified',SUPPORT_MEMBER_EMAIL_NOT_VERIFIED);
                    break;
            }
        }
    
        $support_members = $base_query->paginate(10);

        return view('admin.support_members.index')
                    ->with('page', 'support_members')
                    ->with('sub_page', 'support_members-view')
                    ->with('support_members', $support_members);
    
    }

    /**
     * @method support_members_create()
     *
     * @uses To create support_member details
     *
     * @created  
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function support_members_create() {

        $support_member = new \App\Models\SupportMember;

        return view('admin.support_members.create')
                    ->with('page', 'support_members')
                    ->with('sub_page','support_members-create')
                    ->with('support_member', $support_member);           
   
    }

    /**
     * @method support_members_edit()
     *
     * @uses To display and update support_member details based on the support_member id
     *
     * @created 
     *
     * @updated 
     *
     * @param object $request - support_member Id
     * 
     * @return redirect view page 
     *
     */
    public function support_members_edit(Request $request) {

        try {

            $support_member = \App\Models\SupportMember::find($request->support_member_id);

            if(!$support_member) { 

                throw new Exception(tr('support_member_not_found'), 101);
            }

            return view('admin.support_members.edit')
                    ->with('page', 'support_members')
                    ->with('sub_page', 'support_members-view')
                    ->with('support_member', $support_member); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.support_members.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method support_members_save()
     *
     * @uses To save the support_members details of new/existing support_member object based on details
     *
     * @created 
     *
     * @updated 
     *
     * @param object request - support_member Form Data
     *
     * @return success message
     *
     */
    public function support_members_save(Request $request) {

        
        try {

            DB::begintransaction();

            $rules = [
                
                'first_name' => 'required|max:191',
                'last_name' => 'required|max:191',
                'email' => 'required|email|max:191|unique:support_members',
                'password' => $request->support_member_id ? '' : 'required|min:6|confirmed',
                
                'mobile' =>'digits_between:6,13',
                'picture' => 'mimes:jpg,png,jpeg',
                'support_members_id' => 'exists:support_members,id|nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $support_member = $request->support_members_id ? \App\Models\SupportMember::find($request->support_member_id) : new \App\Models\SupportMember;

            $is_new_support_member = NO;

            if($support_member->id) {

                $message = tr('support_member_updated_success'); 

            } else {

                $is_new_support_member = YES;

                $support_member->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('support_member_created_success');

                //$support_member->email_verified_at = date('Y-m-d H:i:s');

                $support_member->picture = asset('placeholder.jpeg');

                //$support_member->is_email_verified = EMAIL_VERIFIED;

                $support_member->token = Helper::generate_token();

                $support_member->token_expiry = Helper::generate_token_expiry();

            }

            $support_member->first_name = $request->first_name ?: $support_member->first_name;

            $support_member->last_name = $request->last_name ?: $support_member->last_name;

            $support_member->email = $request->email ?: $support_member->email;

            $support_member->mobile = $request->mobile ?: '';

            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->support_member_id) {

                    Helper::storage_delete_file($support_member->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $support_member->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
            }

            if($support_member->save()) {

                if($is_new_support_member == YES) {

                    /**
                     * @todo Welcome mail notification
                     */

                    $email_data['subject'] = tr('support_member_welcome_email' , Setting::get('site_name'));

                    $email_data['email']  = $support_member->email;

                    $email_data['name'] = $support_member->first_name;

                    $email_data['page'] = "emails.support_members.welcome";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    //$support_member->is_email_verified = SUPPORT_MEMBER_EMAIL_VERIFIED;

                    $support_member->save();

                }

                DB::commit(); 

                return redirect(route('admin.support_members.view', ['support_member_id' => $support_member->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('support_member_save_failed'));
            
        } 
        catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method support_members_view()
     *
     * @uses Display the specified support_member details based on support_member_id
     *
     * @created  
     *
     * @updated 
     *
     * @param object $request - support_member Id
     * 
     * @return View page
     *
     */
    public function support_members_view(Request $request) {
       
        try {
      
            $support_member = \App\Models\SupportMember::find($request->support_member_id);

            if(!$support_member) { 

                throw new Exception(tr('support_member_not_found'), 101);                
            }

            return view('admin.support_members.view')
                        ->with('page', 'support_members') 
                        ->with('sub_page','support_members-view') 
                        ->with('support_member' , $support_member);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method support_members_delete()
     *
     * @uses delete the support_member details based on support_member id
     *
     * @created  
     *
     * @updated  
     *
     * @param object $request - support_member Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function support_members_delete(Request $request) {

        try {

            DB::begintransaction();

            $support_member = \App\Models\SupportMember::find($request->support_member_id);
            
            if(!$support_member) {

                throw new Exception(tr('support_member_not_found'), 101);                
            }

            if($support_member->delete()) {

                DB::commit();

                return redirect()->route('admin.support_members.index')->with('flash_success',tr('support_member_deleted_success'));   

            } 
            
            throw new Exception(tr('support_member_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method support_members_status
     *
     * @uses To update support_member status as DECLINED/APPROVED based on support_members id
     *
     * @created 
     *
     * @updated 
     *
     * @param object $request - support_member Id
     * 
     * @return response success/failure message
     *
     **/
    public function support_members_status(Request $request) {

        try {

            DB::beginTransaction();

            $support_member = \App\Models\SupportMember::find($request->support_member_id);

            if(!$support_member) {

                throw new Exception(tr('support_member_not_found'), 101);
                
            }

            $support_member->status = $support_member->status ? DECLINED : APPROVED ;

            if($support_member->save()) {

                if($support_member->status == DECLINED) {

                    $email_data['subject'] = tr('support_member_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('support_member_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');

                }

                $email_data['email']  = $support_member->email;

                $email_data['name']  = $support_member->name;

                $email_data['page'] = "emails.support_members.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $support_member->status ? tr('support_member_approve_success') : tr('support_member_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('support_member_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.support_members.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method support_members_verify_status()
     *
     * @uses verify the support_member
     *
     * @created 
     *
     * @updated
     *
     * @param object $request - support_member Id
     *
     * @return redirect back page with status of the support_member verification
     */
    public function support_members_verify_status(Request $request) {

        try {

            DB::beginTransaction();

            $support_member = \App\Models\SupportMember::find($request->support_member_id);

            if(!$support_member) {

                throw new Exception(tr('support_member_not_found'), 101);
                
            }

            $support_member->is_email_verified = $support_member->is_email_verified ? SUPPORT_MEMBER_EMAIL_NOT_VERIFIED : SUPPORT_MEMBER_EMAIL_VERIFIED;

            if($support_member->save()) {

                DB::commit();

                $message = $support_member->is_email_verified ? tr('support_member_verify_success') : tr('support_member_unverify_success');

                return redirect()->route('admin.support_members.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('support_member_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.support_members.index')->with('flash_error', $e->getMessage());

        }
    
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


        return view('admin.support_tickets.index')
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
        
            return view('admin.support_tickets.view')
                        ->with('page', 'support_tickets') 
                        ->with('sub_page','support_tickets-view') 
                        ->with('support_ticket' , $support_ticket);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method support_tickets_create()
     *
     * @uses To create subscriptions details
     *
     * @created  
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function support_tickets_create() {

        $support_ticket = new \App\Models\SupportTicket;

        $users = \App\Models\User::orderby('name', 'desc')->Approved()->get();

        foreach ($users as $key => $user_details) {

            $user_details->is_selected = NO;
        }

        $support_members = \App\Models\SupportMember::Approved()->orderBy('name', 'desc')->get();

        foreach ($support_members as $key => $support_member) {
            $support_member->is_selected = NO;
        }

        return view('admin.support_tickets.create')
                    ->with('page', 'support_ticket')
                    ->with('sub_page','support_ticket-create')
                    ->with('support_ticket', $support_ticket)
                    ->with('users', $users)
                    ->with('support_members', $support_members);                    

    }

    /**
     * @method support_tickets_save()
     *
     * @uses To save the support_tickets details of new/existing subscription object based on details
     *
     * @created 
     *
     * @updated 
     *
     * @param object request - Subscrition Form Data
     *
     * @return success message
     *
     */
    public function support_tickets_save(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'user_id' => 'required',
                'subject'  => 'required|max:255',
                'message' => 'max:255',
                

            ];

            Helper::custom_validator($request->all(),$rules);


            $support_ticket = \App\Models\SupportTicket::find($request->support_ticket_id) ?? new \App\Models\SupportTicket;

            $support_ticket->status = APPROVED;
            
            $support_ticket->user_id = $request->user_id;

            $support_ticket->support_member_id = $request->support_member_id;

            $support_ticket->subject = $request->subject ?: "";

            $support_ticket->message = $request->message ?: "";

            

            if( $support_ticket->save() ) {

                DB::commit();

                $message = $request->support_ticket_id ? tr('support_ticket_update_success')  : tr('support_ticket_create_success');

                return redirect()->route('admin.support_tickets.view', ['support_ticket_id' => $support_ticket->id])->with('flash_success', $message);
            } 

            throw new Exception(tr('support_ticket_saved_error') , 101);

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());
        } 

    }

    /**
     * @method support_tickets_edit()
     *
     * @support_ticket To display and update support_tickets details based on the support_ticket id
     *
     * @created 
     *
     * @updated 
     *
     * @param object $request - Support_ticket Id
     * 
     * @return redirect view page 
     *
     */
    public function support_tickets_edit(Request $request) {

        try {

            $support_ticket = \App\Models\SupportTicket::find($request->support_ticket_id);

            if(!$support_ticket) {

                throw new Exception(tr('support_ticket_not_found'), 101);
            }

            $users = \App\Models\User::orderby('name', 'desc')->Approved()->get();

            foreach ($users as $key => $user_details) {

                $user_details->is_selected = $user_details->id == $support_ticket->user_id ? YES : NO;
            }

            $support_members = \App\Models\SupportMember::Approved()->orderBy('name', 'desc')->get();

            foreach ($support_members as $key => $support_member) {
                $support_member->is_selected = $support_member->id == $support_ticket->support_member_id ? YES : NO;

            }

            return view('admin.support_tickets.edit')
                    ->with('page', 'support_tickets')
                    ->with('sub_page', 'support_ticket-view')
                    ->with('support_ticket', $support_ticket)
                    ->with('users', $users)
                    ->with('support_members', $support_members);
            
        } catch(Exception $e) {

            return redirect()->route('admin.support_tickets.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method support_tickets_delete()
     *
     * @uses delete the support_tickets details based on support_ticket id
     *
     * @created  
     *
     * @updated  
     *
     * @param object $request - Support_ticket Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function support_tickets_delete(Request $request) {

        try {

            DB::begintransaction();

            $support_ticket = \App\Models\SupportTicket::find($request->support_ticket_id);
            
            if(!$support_ticket) {

                throw new Exception(tr('support_tickets_not_found'), 101);                
            }

            if($support_ticket->delete()) {

                DB::commit();

                return redirect()->route('admin.support_tickets.index')->with('flash_success',tr('support_ticket_deleted_success'));   

            } 
            
            throw new Exception(tr('support_ticket_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }
}
