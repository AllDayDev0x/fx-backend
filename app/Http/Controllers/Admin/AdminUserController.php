<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper, App\Helpers\EnvEditorHelper;

use DB, Hash, Setting, Auth, Validator, Exception, Enveditor;

use App\Jobs\SendEmailJob;

use Excel;

use App\Exports\UsersExport, App\Exports\WeeklyReport, App\Exports\MonthlyReport, App\Exports\CustomReport;

use App\Models\User, App\Models\Post, App\Models\UserSubscriptionPayment, App\Models\UserWallet, App\Models\PostLike, App\Models\UserTip, App\Models\PostPayment, App\Models\UserCategory, App\Models\Follower, App\Models\UserSubscription;

use App\Models\Cart;

use Image;

use App\Models\ReferralCode;

use App\Models\Category, App\Models\CategoryDetail;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

use App\Exports\SubscriptionPaymentExport, App\Exports\ChatAssetPaymentExport;

class AdminUserController extends Controller
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
     * @method report_dashboard()
     *
     * @uses Show the User Dashboard.
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */

    public function report_dashboard(Request $request) {
        
        $data = new \stdClass;

        $data->total_posts = Post::where('user_id',$request->user_id)->count();

        $data->post_ids = Post::where('user_id',$request->user_id)->pluck('id');

        $data->subscription_payments = UserSubscriptionPayment::where('to_user_id',$request->user_id)->where('status', PAID)->sum((Setting::get('is_only_wallet_payment') ? 'token' : 'amount'));

        $data->liked_post = PostLike::where('user_id',$request->user_id)->count();

        $data->wallet = UserWallet::where('user_id',$request->user_id)->get();

        $data->user_tips = UserTip::where('to_user_id',$request->user_id)->where('status',PAID)->sum((Setting::get('is_only_wallet_payment') ? 'token' : 'amount'));

        $data->post_payments = PostPayment::where('status',PAID)->whereIn('post_id',$data->post_ids)->sum((Setting::get('is_only_wallet_payment') ? 'token' : 'paid_amount'));

        $data->total_payments = $data->user_tips + $data->post_payments + $data->subscription_payments;

        $data->analytics = last_x_months_data(12);

        $data->posts_data = last_x_months_posts(12,$request->user_id) ?? [];

        $data->user_id = $request->user_id;

        $user = $user = User::find($request->user_id);

        return view('admin.users.report_dashboard')
                    ->with('page' , 'users')
                    ->with('data', $data)
                    ->with('user', $user);
    
    }

    /**
     * @method weekly_report excel download
     *
     * @uses to send user report request details based on request id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/

    public function weekly_report(Request $request) {

        try{

            $file_format = '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new WeeklyReport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error' , $e->getMessage());

        }

    }

    /**
     * @method monthly_report excel download
     *
     * @uses to send user report request details based on request id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/

    public function monthly_report(Request $request) {

        try{

            $file_format = '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new MonthlyReport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error' , $e->getMessage());

        }

    }

    /**
     * @method monthly_report excel download
     *
     * @uses to send user report request details based on request id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/

    public function custom_report(Request $request) {

        try{

            $file_format = '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new CustomReport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error' , $e->getMessage());

        }

    }

    /**
     * @method send_week_report
     *
     * @uses to send user report request details based on request id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/
    
    public function send_week_report(Request $request) {

        try {

            $now = Carbon::now();

            $start_date = $now->startOfWeek()->format('Y-m-d H:i:s');

            $end_date = $now->endOfWeek()->format('Y-m-d H:i:s');

            $response = CommonRepo::send_report($start_date,$end_date,$request->user_id,WEELKY_REPORT);

            return redirect()->back()->with('flash_success',tr('report_mail_sent_success'));

        } catch(Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method send_monthly_report
     *
     * @uses to send user report request details based on request id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/
    
    public function send_monthly_report(Request $request) {

        try {

            $now = Carbon::now();

            $start_date = $now->startOfMonth()->format('Y-m-d H:i:s');

            $end_date = $now->endOfMonth()->format('Y-m-d H:i:s');

            $response = CommonRepo::send_report($start_date,$end_date,$request->user_id,MONTHLY_REPORT);

            return redirect()->back()->with('flash_success',tr('report_mail_sent_success'));

        } catch(Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method send_custom_report
     *
     * @uses to send user report request details based on request id
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return view page
     *
     **/
    
    public function send_custom_report(Request $request) {

        try {

            $rules = [
                'from_date' => 'required',
                'to_date' => 'required|after:from_date',
                ];

            Helper::custom_validator($request->all(),$rules);

            if($request->type == 'export'){

                $file_format = '.xlsx';

                $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

                return Excel::download(new CustomReport($request), $filename);

            }else{

                $start_date = $request->from_date;

                $end_date = $request->to_date;

                $response = CommonRepo::send_report($start_date,$end_date,$request->user_id,CUSTOM_REPORT);

                return redirect()->back()->with('flash_success',tr('report_mail_sent_success'));
            }

        } catch(Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method users_index()
     *
     * @uses To list out users details 
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function users_index(Request $request) {

        $base_query = \App\Models\User::orderBy('created_at','desc');

        $page = 'users'; $sub_page = 'users-view';

        $title = tr('view_users');

        if($request->search_key) {

            $search_user_ids = \App\Models\User::where('users.name','LIKE','%'.$request->search_key.'%')
                    ->orWhere('users.email','LIKE','%'.$request->search_key.'%')
                    ->orWhere('users.mobile','LIKE','%'.$request->search_key.'%')
                    ->pluck('id');

            $base_query = $base_query->whereIn('users.id',$search_user_ids);
        }

        if($request->account_type != '') {

            $page = $request->account_type == USER_FREE_ACCOUNT ? 'users-free' : 'users-premium'; $sub_page = '';

            $title = $request->account_type == USER_FREE_ACCOUNT ? tr('free_users') : tr('premium_users');

            $sub_page = $request->account_type == USER_FREE_ACCOUNT ?'free-users' : 'premium-users';

            $base_query = $base_query->where('users.user_account_type', $request->account_type);

        } 

        if($request->document_status != '') {

            if($request->document_status == USER_DOCUMENT_PENDING){

                $base_query = $base_query->whereIn('users.is_document_verified',[USER_DOCUMENT_NONE,USER_DOCUMENT_PENDING]);

                
            }else{

                $base_query = $base_query->where('users.is_document_verified',$request->document_status);

            }

        }

        if($request->status != '') {

            $base_query = $base_query->where('users.status',$request->status);

        }

        if(Setting::get('is_referral_enabled') && $request->referral_code_id) {

            $user_ids = \App\Models\UserReferral::where('referral_code_id',$request->referral_code_id)->get()->pluck('user_id')->toArray();

            $base_query = $base_query->whereIn('users.id',$user_ids);

        }

        if($request->category_id) {

            $category_details = CategoryDetail::select('user_id')
                           ->where('category_id', $request->category_id)
                           ->where('type', CATEGORY_TYPE_PROFILE)
                           ->pluck('user_id')->toArray();

            
            $base_query = $base_query->whereIn('users.id',  $category_details);
           
        }

        $users = $base_query->paginate($this->take);

        $category = Category::find($request->category_id)??'';

        return view('admin.users.index')
                    ->with('page', $page)
                    ->with('sub_page', $sub_page)
                    ->with('title', $title)
                    ->with('category', $category)
                    ->with('users', $users);
    
    }

    /**
     * @method users_create()
     *
     * @uses To create user details
     *
     * @created  Akshata
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function users_create() {

        $user = new \App\Models\User;

        $categories = Category::where('status',APPROVED)->get();

        $user_category = CategoryDetail::where('user_id',$user->id)->where('type', CATEGORY_TYPE_PROFILE)->pluck('id')->toArray();

        return view('admin.users.create')
                    ->with('page', 'users')
                    ->with('sub_page','users-create')
                    ->with('user', $user)  
                    ->with('categories',$categories)
                    ->with('user_category',$user_category);        
   
    }

    public function users_excel(Request $request) {

        try{
            $file_format = $request->file_format ?? '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new UsersExport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error' , $e->getMessage());

        }

    }

    /**
     * @method users_edit()
     *
     * @uses To display and update user details based on the user id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return redirect view page 
     *
     */
    public function users_edit(Request $request) {

        try {

            $user = \App\Models\User::find($request->user_id);

            if(!$user) { 

                throw new Exception(tr('user_not_found'), 101);
            }

            $user_category = CategoryDetail::where('user_id',$user->id)->where('type', CATEGORY_TYPE_PROFILE)->pluck('category_id')->toArray();

            $categories = Category::where('status',APPROVED)->get();

            return view('admin.users.edit')
                    ->with('page', 'users')
                    ->with('sub_page', 'users-view')
                    ->with('user', $user)
                    ->with('categories',$categories)
                    ->with('user_category',$user_category); 
            
        } catch(Exception $e) {

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method users_save()
     *
     * @uses To save the users details of new/existing user object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - User Form Data
     *
     * @return success message
     *
     */
    public function users_save(Request $request) {

        try {

            DB::begintransaction();

            $rules = [   
                'name' => 'required|max:191',
                //'last_name' => 'required|max:191',             
                //'first_name' => 'required|max:191',
                //'last_name' => 'required|max:191',
                // 'email' => 'email|unique:users,email,'.$request->id.'|max:255',
                'username' => 'nullable|unique:users,username,'.$request->user_id.'|max:255|regex:/^[a-zA-Z0-9-._]+$/u',
                'email' => $request->user_id ? 'required|email|max:191|unique:users,email,'.$request->user_id.',id' : 'required|email|max:191|unique:users,email,NULL,id',
                'password' => $request->user_id ? "" : 'required|min:6|confirmed',
                'mobile' => $request->mobile ? 'digits_between:6,13' : '',
                'picture' => 'mimes:jpg,png,jpeg',
                'user_id' => 'exists:users,id|nullable',
                'cover' => 'nullable|mimes:jpeg,png,jpg',
                'gender' => 'nullable|in:male,female,others,rather-not-select',
                'category_ids' => 'required',
            ];

            $custom_errors = [ 'regex' => api_error(265) ];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $user = $request->user_id ? \App\Models\User::find($request->user_id) : new \App\Models\User;

            $is_new_user = NO;

            if($user->id) {

                $message = tr('user_updated_success'); 

            } else {

                $is_new_user = YES;

                $user->password = ($request->password) ? \Hash::make($request->password) : null;

                $message = tr('user_created_success');

                $user->email_verified_at = date('Y-m-d H:i:s');

                $user->is_email_verified = USER_EMAIL_VERIFIED;

                $user->token = Helper::generate_token();

                $user->token_expiry = Helper::generate_token_expiry();
                
                $user->login_by = $request->login_by ?: 'manual';

            }

           $user->name = $request->name ?? "";

            $user->first_name = $request->first_name ?? "";

            $user->last_name = $request->last_name ?? "";

            $user->email = $request->email ?? "";

            $user->mobile = $request->mobile ?? "";

            $user->username = $request->username ?? "";

            $user->mobile = $request->mobile ?: "";

            $user->gender = $request->gender ?: "rather-not-select";

            $user->website = $request->website ?: "";

            $user->amazon_wishlist = $request->amazon_wishlist ?: "";

            $user->instagram_link = $request->filled('instagram_link') ? $request->instagram_link : "";
            
            $user->facebook_link = $request->filled('facebook_link') ? $request->facebook_link : "";
            
            $user->twitter_link = $request->filled('twitter_link') ? $request->twitter_link : "";

            $user->linkedin_link = $request->filled('linkedin_link') ? $request->linkedin_link : "";

            $user->pinterest_link = $request->filled('pinterest_link') ? $request->pinterest_link : "";

            $user->youtube_link = $request->filled('youtube_link') ? $request->youtube_link : "";

            $user->twitch_link = $request->filled('twitch_link') ? $request->twitch_link : "";

            $user->snapchat_link = $request->filled('snapchat_link') ? $request->snapchat_link : "";

            $username = $request->username ?: $user->username;

            $user->unique_id = $user->username = strtolower($username);
            
            // Upload picture
            
            if($request->hasFile('picture')) {

                if($request->user_id) {

                    Helper::storage_delete_file($user->picture, COMMON_FILE_PATH); 
                    // Delete the old pic
                }

                $user->picture = Helper::storage_upload_file($request->file('picture'), COMMON_FILE_PATH);
          
            }

            if($request->hasFile('cover') != "") {

                Helper::storage_delete_file($user->cover, COMMON_FILE_PATH); // Delete the old pic

                $user->cover = Helper::storage_upload_file($request->file('cover'), COMMON_FILE_PATH);
            
            }

            if($user->save()) {

                if($request->monthly_amount || $request->yearly_amount) {

                    $user_subscription = \App\Models\UserSubscription::where('user_id', $user->id)->first() ?? new \App\Models\UserSubscription;

                    $user_subscription->user_id = $user->id;

                    $user_subscription->monthly_amount = $request->monthly_amount ?: ($user_subscription->monthly_amount ?: 0.00);

                    $user_subscription->yearly_amount = $request->yearly_amount ?: ($user_subscription->yearly_amount ?: 0.00);

                    $user_subscription->save();

                    DB::commit();

                }


                if($is_new_user == YES) {

                    /**
                     * @todo Welcome mail notification
                     */

                  
                    $email_data['subject'] = tr('user_welcome_email' , Setting::get('site_name'));

                    $email_data['email']  = $user->email;

                    $email_data['name'] = $user->first_name;

                    $email_data['page'] = "emails.users.welcome";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    $user->is_email_verified = USER_EMAIL_VERIFIED;

                    $user->user_account_type = $request->user_account_type;

                    $user->save();

                }

                /*if($request->category_id!=''){

                    $category = \App\Models\CategoryDetail::where('user_id', $user->id)->first() ?? new \App\Models\CategoryDetail;
    
                    $category->category_id = $request->category_id;
    
                    $category->user_id = $user->id;
    
                    $category->save();

                }*/

                if($request->category_ids) {
                    
                    $category_ids = $request->category_ids;
                    
                    if(!is_array($category_ids)) {

                        $category_ids = explode(',', $category_ids);
                        
                    }

                    if($request->user_id) {
                    
                        CategoryDetail::where('user_id', $request->user_id)->whereNotIn('category_id', $category_ids)->delete();
                    }                    


                    foreach ($category_ids as $key => $value) {

                        $user_category = new CategoryDetail;

                        $user_category->user_id = $user->id;
                        
                        $user_category->category_id = $value;

                        $user_category->type = CATEGORY_TYPE_PROFILE;

                        $user_category->status = APPROVED;
                        
                        $user_category->save();

                    } 
                }

                DB::commit(); 

                return redirect(route('admin.users.view', ['user_id' => $user->id]))->with('flash_success', $message);

            } 

            throw new Exception(tr('user_save_failed'));
            
        } 
        catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }


    /**
     * @method user_upgrade_account()
     *
     * @uses To save the users details of new/existing user object based on details
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object request - User Form Data
     *
     * @return success message
     *
     */
    public function user_upgrade_account(Request $request) {

        try {

            DB::beginTransaction();

              $rules = [
                'user_id' => 'required',
                'monthly_amount' => 'numeric|min:1',
                'yearly_amount' => 'numeric|gt:monthly_amount',
            ];


            Helper::custom_validator($request->all(),$rules);

            $user = User::find($request->user_id);


            if(!$user) { 

                throw new Exception(tr('user_not_found'), 101);                
            }

            $user->user_account_type = ($request->monthly_amount > 0 || $request->yearly_amount) > 0 ? USER_PREMIUM_ACCOUNT : USER_FREE_ACCOUNT;

            $user->is_document_verified = USER_DOCUMENT_APPROVED;

            $user->is_content_creator = CONTENT_CREATOR;

            $user->content_creator_step = CONTENT_CREATOR_APPROVED;
           
            if($user->save()) {

                if($request->is_billing_account) {

                    $user_billing_account = new \App\Models\UserBillingAccount;

                    $user_billing_account->user_id = $request->user_id ?: 0;

                    $user_billing_account->nickname = $request->nickname ?: '';

                    $user_billing_account->first_name = $request->first_name ?: '';

                    $user_billing_account->last_name = $request->last_name ?: '';

                    $user_billing_account->route_number = $request->route_number ?: '';

                    $user_billing_account->bank_type = $request->bank_type ?: '';

                    $user_billing_account->account_holder_name = $request->account_holder_name ?: 0;

                    $user_billing_account->account_number = $request->account_number ?: 0;

                    $user_billing_account->ifsc_code = $request->ifsc_code ?: '';

                    $user_billing_account->swift_code = $request->swift_code ?: '';

                    $user_billing_account->bank_name = $request->bank_name ?: '';

                    $user_billing_account->save();
                }

                if(isset($request->monthly_amount) || isset($request->yearly_amount) || isset($request->vod_amount)) {

                   $user_subscription =  UserSubscription::find($request->subscription_id) ?? new UserSubscription ;

                    $user_subscription->user_id = $user->id;

                    if(Setting::get('is_only_wallet_payment')) {

                        $user_subscription->monthly_token = $request->monthly_amount ??  0.00;

                        $user_subscription->monthly_amount = $user_subscription->monthly_token * Setting::get('token_amount');

                        $user_subscription->yearly_token = $request->yearly_amount ?? 0.00;

                        $user_subscription->yearly_amount = $user_subscription->yearly_token * Setting::get('token_amount');

                    } else {

                        $user_subscription->monthly_amount = $request->monthly_amount ?? 0.00;

                        $user_subscription->yearly_amount = $request->yearly_amount ?? 0.00;

                    }

                    $user_subscription->vod_amount = $request->vod_amount ?? 0.00;

                    $user_subscription->save();

                    DB::commit();

                }

                $email_data['subject'] = tr('user_account_upgrade').' '.Setting::get('site_name');

                $email_data['email']  = $user->email ?? "-";

                $email_data['name']  = $user->name ?? "-";

                $email_data['page'] = "emails.users.account-upgrade";

                $email_data['message'] = tr('account_upgrade_message', $user->name ?? ''); 

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));


                DB::commit(); 

                return redirect()->back()->with('flash_success',tr('user_upgrade_account',$user->name));

            } 

            throw new Exception(tr('user_upgrade_account_failed'));
            
        } 
        catch(Exception $e){ 

            DB::rollback();

            return redirect()->back()->withInput()->with('flash_error', $e->getMessage());

        } 

    }

    /**
     * @method users_view()
     *
     * @uses Display the specified user details based on user_id
     *
     * @created Akshata 
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return View page
     *
     */
    public function users_view(Request $request) {
       
        try {
      
            $user = \App\Models\User::find($request->user_id);

            if(!$user) { 

                throw new Exception(tr('user_not_found'), 101);                
            }

            if($request->search_key) {

                $search_key = $request->search_key;

                $base_query = $base_query->whereHas('user',function($query) use($search_key) {

                    return $query->where('users.username','LIKE','%'.$search_key.'%');

                })->orWhere('referral_codes.referral_code','LIKE','%'.$search_key.'%');
            }

            $referral_codes = ReferralCode::where('referral_codes.user_id', $user->id)->first();

            if(!$referral_codes) {

                $referral_codes = \App\Repositories\CommonRepository::user_referral_code($user->id);

            }

            $category_details = $user->categoryDetails()->pluck('category_id');
           
            $categories = Category::find($category_details);

            return view('admin.users.view')
                        ->with('page', 'users') 
                        ->with('sub_page','users-view') 
                        ->with('user' , $user)
                        ->with('referral_code', $referral_codes)
                        ->with('categories', $categories);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method users_delete()
     *
     * @uses delete the user details based on user id
     *
     * @created Akshata 
     *
     * @updated  
     *
     * @param object $request - User Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function users_delete(Request $request) {

        try {

            DB::begintransaction();

            $user = \App\Models\User::find($request->user_id);
            
            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);                
            }

            if($user->delete()) {

                DB::commit();


                return redirect()->route('admin.users.index',['page'=>$request->page])->with('flash_success',tr('user_deleted_success'));   

            } 
            
            throw new Exception(tr('user_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method users_status
     *
     * @uses To update user status as DECLINED/APPROVED based on users id
     *
     * @created Akshata
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return response success/failure message
     *
     **/
    public function users_status(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\Models\User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->status = $user->status ? DECLINED : APPROVED ;

            if($user->save()) {

                if($user->status == DECLINED) {

                    $email_data['subject'] = tr('user_decline_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('declined');

                } else {

                    $email_data['subject'] = tr('user_approve_email' , Setting::get('site_name'));

                    $email_data['status'] = tr('approved');

                }

                $email_data['email']  = $user->email;

                $email_data['name']  = $user->name;

                $email_data['page'] = "emails.users.status";

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $user->status ? tr('user_approve_success') : tr('user_decline_success');

                return redirect()->back()->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_status_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

    /**
     * @method users_verify_status()
     *
     * @uses verify the user
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - User Id
     *
     * @return redirect back page with status of the user verification
     */
    public function users_verify_status(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\Models\User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->is_email_verified = $user->is_email_verified ? USER_EMAIL_NOT_VERIFIED : USER_EMAIL_VERIFIED;

            if($user->save()) {

                $status_message = $user->is_email_verified ? tr('approved'):tr('declined');

                $email_data['subject'] = tr('user_email_verification').' '.Setting::get('site_name');

                $email_data['email']  = $user->email ?? "-";

                $email_data['name']  = $user->name ?? "-";

                $email_data['page'] = "emails.users.email-verify";

                $email_data['message'] = tr('email_verify_message', $status_message); 

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                $message = $user->is_email_verified ? tr('user_verify_success') : tr('user_unverify_success');

                return redirect()->route('admin.users.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }
    
    }


     /**
     * @method users_bulk_action()
     * 
     * @uses To delete,approve,decline multiple users
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     *
     * @return success/failure message
     */
    public function users_bulk_action(Request $request) {

        try {
            
            $action_name = $request->action_name ;

            $user_ids = explode(',', $request->selected_users);

            if (!$user_ids && !$action_name) {

                throw new Exception(tr('user_action_is_empty'));

            }

            DB::beginTransaction();

            if($action_name == 'bulk_delete'){

                $user = \App\Models\User::whereIn('id', $user_ids)->delete();

                if ($user) {

                    DB::commit();

                    return redirect()->back()->with('flash_success',tr('admin_users_delete_success'));

                }

                throw new Exception(tr('user_delete_failed'));

            }elseif($action_name == 'bulk_approve'){

                $user =  \App\Models\User::whereIn('id', $user_ids)->update(['status' => USER_APPROVED]);

                if ($user) {

                    DB::commit();

                    return back()->with('flash_success',tr('admin_users_approve_success'))->with('bulk_action','true');
                }

                throw new Exception(tr('users_approve_failed'));  

            }elseif($action_name == 'bulk_decline'){
                
                $user =  \App\Models\User::whereIn('id', $user_ids)->update(['status' => USER_DECLINED]);

                if ($user) {
                    
                    DB::commit();

                    return back()->with('flash_success',tr('admin_users_decline_success'))->with('bulk_action','true');
                }

                throw new Exception(tr('users_decline_failed')); 
            }

        }catch( Exception $e) {

            DB::rollback();

            return redirect()->back()->with('flash_error',$e->getMessage());
        }

    }

     /**
     * @method user_followers()
     *
     * @uses This is to display the all followers of specified content creator
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
     public function user_followers(Request $request) {

      try {

        $user = \App\Models\User::find($request->user_id);

        if(!$user) {

            throw new Exception(tr('user_not_found'));

        }

        $search_key = $request->search_key;

        $blocked_users = blocked_users($request->user_id);

        $base_query =  \App\Models\Follower::whereNotIn('follower_id',$blocked_users)->where('user_id', $request->user_id)->where('status', YES);

        if($search_key) {

            $base_query = $base_query
                        ->whereHas('followerDetails',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        });
        }


        $user_followers = $base_query->paginate($this->take);

        return view('admin.users.followers')
                ->with('page', 'users')
                ->with('sub_page', 'users-view')
                ->with('user_followers', $user_followers)
                ->with('user', $user);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());

        }
     }

    /**
     * @method user_followings()
     *
     * @uses This is to display the all followers of specified 
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - follower Id
     *
     * @return view page
     */
     public function user_followings(Request $request) {
      
      try{

        $user = \App\Models\User::find($request->following_id);

        if(!$user) {

            throw new Exception(tr('user_not_found'));
        }

        $blocked_users = blocked_users($request->following_id);

        $base_query = \App\Models\Follower::whereNotIn('user_id',$blocked_users)->where('follower_id',$request->following_id)->where('status', YES);

        $search_key = $request->search_key;

        if($search_key) {

            $base_query = $base_query
                        ->whereHas('user',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        });
        }

        $followings =  $base_query->paginate($this->take);

        return view('admin.users.followings')
                ->with('page','users')
                ->with('sub_page','users-view')
                ->with('followings', $followings)
                ->with('user', $user);

        } catch(Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());

        }
       
    }

    /**
     * @method user_documents_index()
     *
     * @uses Lists all users documents 
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Stardom document Id
     *
     * @return view page
     */
    public function user_documents_index(Request $request) {

        $base_query = \App\Models\User::whereHas('userDocuments')->orderBy('updated_at','desc');
        
        if($request->search_key) {

            $base_query->where(function ($query) use ($request) {
                $query->where('name', "like", "%" . $request->search_key . "%");
                $query->orWhere('email', "like", "%" . $request->search_key . "%");
                $query->orWhere('mobile', "like", "%" . $request->search_key . "%");
            });
        }

        if($request->status!='') {

            $base_query->where('status', $request->status);
        }

        $users = $base_query->where('is_document_verified', '!=', USER_DOCUMENT_APPROVED)->paginate($this->take);
        
        foreach($users as $user){

            $user->documents_count = \App\Models\UserDocument::where('user_id',$user->id)->count();
            
        }
        
        return view('admin.users.documents.index')
                    ->with('page','users-documents')
                    ->with('sub_page', '')
                    ->with('users', $users);    
    
    }

    /**
     * @method user_document_view()
     *
     * @uses Display the specified document
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Stardom document Id
     *
     * @return view page
     */
    public function user_documents_view(Request $request) {

        try {

            $user = User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'));

            }

            $user_documents = \App\Models\UserDocument::where('user_id', $request->user_id)->orderBy('updated_at','desc')->paginate($this->take);

            return view('admin.users.documents.view')
                    ->with('page', 'users-documents')
                    ->with('sub_page', '')
                    ->with('user', $user)
                    ->with('user_documents', $user_documents);
            
        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
        }
    
    }

    /**
     * @method user_documents_verify()
     *
     * @uses verify the stardom documents
     *
     * @created Akshata
     *
     * @updated
     *
     * @param object $request - Stardom Document Id
     *
     * @return redirect back page with status of the stardom verification
     */
    public function user_documents_verify(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\Models\User::find($request->user_id);   
            
            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->is_document_verified = $request->status;

            $user->content_creator_step = $request->status == USER_DOCUMENT_APPROVED ? CONTENT_CREATOR_DOC_VERIFIED : $user->content_creator_step;

            if($user->save()) {

                DB::commit();

                $status_message = $user->is_document_verified == USER_DOCUMENT_APPROVED ? tr('approved'):tr('declined');

                $email_data['subject'] = tr('user_document_verification').' '.Setting::get('site_name');

                $email_data['email']  = $user->email ?? "-";

                $email_data['name']  = $user->name ?? "-";

                $email_data['page'] = "emails.users.document-verify";

                $email_data['message'] = tr('document_verify_message', $status_message); 

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                $message = $user->is_document_verified == USER_DOCUMENT_APPROVED ? tr('user_document_verify_success') : tr('user_document_unverify_success');

                return redirect()->route('admin.user_documents.index')->with('flash_success', $message);
            }
            
            throw new Exception(tr('user_document_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.user_documents.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method user_subscriptions_payments()
     *
     * @uses To list out users subscription payment details 
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function user_subscription_payments(Request $request) {
       
        $base_query = \App\Models\UserSubscriptionPayment::has('fromUser')->has('toUser');

        if($request->from_user_id){

            $base_query->where('from_user_id',$request->from_user_id);
            
        }

        $search_key = $request->search_key;

        if($search_key) {

            $user_subscription_payment_ids = \App\Models\UserSubscriptionPayment::whereHas('fromUser',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        })->orwhereHas('toUser',function($query) use($search_key) {
                            
                            return $query->where('users.name','LIKE','%'.$search_key.'%');
                        })->orWhere('user_subscription_payments.payment_id','LIKE','%'.$search_key.'%')->pluck('id');

            $base_query = $base_query->whereIn('id',$user_subscription_payment_ids);
        }

        if($request->status) {

            switch ($request->status) {

                case SORT_BY_HIGH:
                    $base_query = $base_query->orderBy('amount','desc');
                    break;

                case SORT_BY_LOW:
                    $base_query = $base_query->orderBy('amount','asc');
                    break;

                case SORT_BY_FREE:
                    $base_query = $base_query->where('amount',0.00);
                    break;

                case SORT_BY_PAID:

                    $base_query = $base_query->where('amount','!=',0.00);

                    break;
               
                default:
                    $base_query = $base_query->orderBy('created_at','desc');
                    
                    break;
            }
        }
        else{

            $base_query = $base_query->orderBy('created_at','desc');
        }

        $user = User::find($request->from_user_id)??'';

        $user_subscriptions = $base_query->paginate(10);

        return view('admin.users.subscriptions.index')
                    ->with('page', 'payments')
                    ->with('sub_page', 'user-subscription-payments')
                    ->with('user', $user)
                    ->with('user_subscriptions', $user_subscriptions);
    }



    /**
     * @method user_subscriptions_payment_view()
     *
     * @uses To list out users subscription payment details 
     *
     * @created Sakthi
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function user_subscriptions_payment_view(Request $request) {

        try {
       
            $user_subscription_payment = \App\Models\UserSubscriptionPayment::find($request->subscription_id);
             
             if(!$user_subscription_payment) { 

                throw new Exception(tr('user_subscription_payment_not_found'), 101);                
            }


            return view('admin.users.subscriptions.view')
                        ->with('page', 'user_subscription_payment')
                        ->with('sub_page', 'user-subscription-payments')
                        ->with('user_subscription_payment', $user_subscription_payment);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
       }
    }



    /**
     * @method block_users_index()
     *
     * @uses To list out users blocked
     *
     * @created Ganesh
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function block_users_index(Request $request) {

        $base_query = \App\Models\BlockUser::select('block_users.*', DB::raw('count(`blocked_to`) as blocked_count'))
                      ->has('blockeduser')->groupBy('blocked_to')->orderBy('created_at','desc');

        
        $search_key = $request->search_key;

        if($search_key) {

            $base_query = $base_query
                        ->whereHas('blockeduser',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%')
                                     ->orWhere('users.email','LIKE','%'.$search_key.'%');

                        });
        }

        if($request->user_id) {

            $base_query = $base_query->where('block_by',$request->user_id);
        }

        $user = User::find($request->user_id)??'';

        $block_users = $base_query->paginate($this->take);

        $title = tr('blocked_users');

        return view('admin.users.blocked_users.index')
                    ->with('page','users')
                    ->with('sub_page', 'users-blocked')
                    ->with('title', $title)
                    ->with('block_users', $block_users)
                    ->with('user',$user);
    
    }


    /**
     * @method block_users_view()
     *
     * @uses Display the blocked user details based on user_id
     *
     * @created Ganesh 
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return View page
     *
     */
    public function block_users_view(Request $request) {
       
        try {

            if(!$request->user_id) {
    
                throw new Exception(tr('user_not_found'), 101);
                
            }
            
            $user = \App\Models\User::find($request->user_id);    

            $blocked_users = \App\Models\BlockUser::where('blocked_to',$request->user_id)->orderBy('created_at','desc')->paginate($this->take);
            
            $title = tr('blocked_list');
    
            return view('admin.users.blocked_users.view')
                        ->with('page','users')
                        ->with('sub_page', 'users-blocked')
                        ->with('title', $title)
                        ->with('user', $user)
                        ->with('blocked_users', $blocked_users);
    
    
            } catch(Exception $e) {
    
                return redirect()->route('admin.block_users.index')->with('flash_error', $e->getMessage());
    
            }
    
    }


    


    /**
     * @method block_users_delete()
     *
     * @uses delete the block user details based on  id
     *
     * @created Ganesh 
     *
     * @updated  
     *
     * @param object $request - User Id
     * 
     * @return response of success/failure details with view page
     *
     */
    public function block_users_delete(Request $request) {

        try {

            DB::begintransaction();


            if($request->user_id){

                \App\Models\BlockUser::where('blocked_to',$request->user_id)->delete();

                DB::commit();

                return redirect()->route('admin.block_users.index')->with('flash_success',tr('block_user_deleted_success'));   


            }


            $block_user = \App\Models\BlockUser::find($request->block_user_id);
            
            if(!$block_user) {

                throw new Exception(tr('block_user_not_found'), 101);                
            }

            if($block_user->delete()) {

                DB::commit();

                return redirect()->route('admin.block_users.view',['user_id'=>$block_user->blocked_to,'page'=>$request->page])->with('flash_success',tr('block_user_deleted_success'));   

            } 
            
            throw new Exception(tr('user_delete_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
         
    }

    /**
     * @method chat_asset_payments()
     *
     * @uses To list out chat_asset_payments details 
     *
     * @created Arun
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function chat_asset_payments(Request $request) {
       
        $base_query = \App\Models\ChatAssetPayment::orderBy('created_at','desc');

        $search_key = $request->search_key;

        if($search_key) {

            $base_query = $base_query
                        ->whereHas('fromUser',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        })->orwhereHas('toUser',function($query) use($search_key) {
                            
                            return $query->where('users.name','LIKE','%'.$search_key.'%');
                        })->orWhere('chat_asset_payments.payment_id','LIKE','%'.$search_key.'%');
        }

        $chat_asset_payments = $base_query->paginate(10);


        return view('admin.users.chat.index')
                    ->with('page', 'payments')
                    ->with('sub_page', 'chat-asset-payments')
                    ->with('chat_asset_payments', $chat_asset_payments);
    }

    /**
     * @method chat_asset_payment_view()
     *
     * @uses To list out chat_asset_payment details 
     *
     * @created Arun
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function chat_asset_payment_view(Request $request) {

        try {
       
            $chat_asset_payment = \App\Models\ChatAssetPayment::find($request->chat_asset_payment_id);
             
             if(!$chat_asset_payment) { 

                throw new Exception(tr('chat_asset_payment_not_found'), 101);                
            }


            return view('admin.users.chat.view')
                        ->with('page', 'user_subscription_payment')
                        ->with('sub_page', 'chat-asset-payments')
                        ->with('chat_asset_payment', $chat_asset_payment);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
       }
    }

    /**
     * @method users_verify_badge_status()
     *
     * @uses verify the user
     *
     * @created vithya
     *
     * @updated
     *
     * @param object $request - User Id
     *
     * @return redirect back page with status of the user verification
     */
    public function users_verify_badge_status(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\Models\User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->is_verified_badge = $user->is_verified_badge ? NO : YES;

            if($user->save()) {

                DB::commit();

                $message = $user->is_verified_badge ? tr('user_verify_badge_added',$user->name) : tr('user_verify_badge_removed',$user->name);

                return redirect()->route('admin.users.index')->with('flash_success', $message);

            }
            
            throw new Exception(tr('user_verify_change_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }
    
    }

    /**
     * @method billing_accounts_index()
     *
     * @uses To list out user banking details
     *
     * @created Arun
     *
     * @updated Subham Kant
     *
     * @param 
     * 
     * @return return view page
     *
     */

    public function billing_accounts_index(Request $request) {
       
        $base_query = \App\Models\UserBillingAccount::orderBy('created_at','desc');

        $user = User::find($request->user_id)??'';

        if($request->user_id){

         $base_query->where('user_id',$request->user_id);

        }

        if($request->search_key) {

            $base_query->where(function ($query) use ($request) {
                $query->where('nickname', "like", "%" . $request->search_key . "%");
                $query->orWhere('first_name', "like", "%" . $request->search_key . "%");
                $query->orWhere('route_number', "like", "%" . $request->search_key . "%");
                $query->orWhere('business_name', "like", "%" . $request->search_key . "%");
                $query->orWhere('account_number', "like", "%" . $request->search_key . "%");
            });
        }
                      
        $bank_details = $base_query->paginate($this->take);

        return view('admin.users.bank_details')
                    ->with('page', 'users')
                    ->with('sub_page', 'users-view')
                    ->with('user', $user)
                    ->with('bank_details', $bank_details);
    }

    /**
     * @method user_dashboard()
     *
     * @uses Show the User Dashboard.
     *
     * @created Subham
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */

    public function user_dashboard(Request $request) {
        
        $data = new \stdClass;

        $data->total_posts = Post::where('user_id',$request->user_id)->count();

        $data->post_ids = Post::where('user_id',$request->user_id)->pluck('id');

        $data->subscription_payments = UserSubscriptionPayment::where('from_user_id',$request->user_id)->where('status', PAID)->sum('user_subscription_payments.amount');

        $data->liked_post = PostLike::where('user_id',$request->user_id)->count();

        $data->wallet = UserWallet::where('user_id',$request->user_id);

        $data->user_tips = UserTip::where('user_id',$request->user_id)->where('status',PAID)->sum('amount');

        $data->post_payments = PostPayment::where('status',PAID)->whereIn('post_id',$data->post_ids)->sum('paid_amount');

        $data->total_payments = $data->user_tips + $data->post_payments + $data->subscription_payments;

        $data->analytics = last_x_months_data(12);

        $data->posts_data = last_x_months_posts(12,$request->user_id) ?? [];

        $data->user_id = $request->user_id;

        $data->user = User::find($request->user_id);

        return view('admin.users.dashboard')
                    ->with('page' , 'dashboard')
                    ->with('data', $data);
    
    }

    /**
     * @method referral_codes()
     *
     * @uses To list out referrals 
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */

    public function referral_codes(Request $request) {

        $base_query = ReferralCode::orderBy('created_at','desc');

        $search_key = $request->search_key;

        if($request->search_key) {

            $base_query = $base_query
                        ->whereHas('user',function($query) use($search_key) {

                            return $query->where('users.name','LIKE','%'.$search_key.'%');

                        })->orWhere('referral_codes.referral_code','LIKE','%'.$search_key.'%');
        }

        $referrals = $base_query->paginate(10);

        return view('admin.referrals.index')
                    ->with('page', 'user_referrals')
                    ->with('sub_page', 'referrals')
                    ->with('referrals', $referrals);
    }
    /**
     * @method referrals_view()
     *
     * @uses To list out referral details 
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function referrals_view(Request $request) {

        try {
            
            $referrals_code_details = \App\Models\ReferralCode::where("referral_codes.id", "=", $request->referral_id)->first();

            $referrals_user_details = \App\Models\UserReferral::where("referral_code_id", "=", $request->referral_id)->get();
           
             if(!$referrals_code_details) { 

                throw new Exception(tr('referral_details_not_found'), 101);                
            }

            return view('admin.referrals.view')
                        ->with('page', 'referrals')
                        ->with('sub_page', 'referral-details')
                        ->with('referrals_code_details', $referrals_code_details)
                        ->with('referrals_user_details', $referrals_user_details);

        } catch (Exception $e) {

            return redirect()->back()->with('flash_error', $e->getMessage());
       }
   }


     /**
     * @method carts_list()
     *
     * @uses To list out carts details 
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function carts_list(Request $request) {

        $base_query = Cart::where('user_id',$request->user_id)->orderBy('created_at','desc');

        $user = \App\Models\User::find($request->user_id)??'';
      
        $carts = $base_query->paginate($this->take);

        return view('admin.users.carts.index')
                    ->with('page', 'users')
                    ->with('sub_page', 'users_view')
                    ->with('user', $user)
                    ->with('carts', $carts);
    
    }

    
     /**
     * @method carts_remove()
     *
     * @uses remove products from cart 
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */
    public function carts_remove(Request $request) {

         try {

            DB::begintransaction();

            $cart = Cart::find($request->cart_id);
            
            if(!$cart) { 

                throw new Exception(tr('cart_details_not_found'), 101);                
            }

            if($cart->delete()) {

                DB::commit();

                return redirect()->route('admin.users.carts', ['user_id' => $cart->user_id] )->with('flash_success',tr('cart_removed_success'));   

            } 
            
            throw new Exception(tr('cart_remove_failed'));
            
        } catch(Exception $e){

            DB::rollback();

            return redirect()->back()->with('flash_error', $e->getMessage());

        }       
    }

    /**
     * @method content_creator_upgrade
     *
     * @uses To update user to Content Creator based on users id
     *
     * @created Arun
     *
     * @updated 
     *
     * @param object $request - User Id
     * 
     * @return response success/failure message
     *
     **/
    public function content_creator_upgrade(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\Models\User::find($request->user_id);

            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            $user->is_document_verified = USER_DOCUMENT_APPROVED;

            $user->is_content_creator = CONTENT_CREATOR;

            $user->content_creator_step = CONTENT_CREATOR_APPROVED;

            if($user->save()) {

                $email_data['subject'] = tr('upgrade_to_content_creator').' '.Setting::get('site_name');

                $email_data['email']  = $user->email ?? "-";

                $email_data['name']  = $user->name ?? "-";

                $email_data['page'] = "emails.users.account-upgrade";

                $email_data['message'] = tr('upgrade_to_content_creator_success'); 

                $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                DB::commit();

                return redirect()->back()->with('flash_success', tr('upgrade_to_content_creator_success'));
            }
            
            throw new Exception(tr('upgrade_to_content_creator_failed'));

        } catch(Exception $e) {

            DB::rollback();

            return redirect()->route('admin.users.index')->with('flash_error', $e->getMessage());

        }

    }

    public function subscription_payment_excel(Request $request) {

        try{
            $file_format = $request->file_format ?? '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new SubscriptionPaymentExport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.users_subscriptions.index')->with('flash_error' , $e->getMessage());

        }

    }

    public function chat_asset_payment_excel(Request $request) {

        try{
            $file_format = $request->file_format ?? '.xlsx';

            $filename = routefreestring(Setting::get('site_name'))."-".date('Y-m-d-h-i-s')."-".uniqid().$file_format;

            return Excel::download(new ChatAssetPaymentExport($request), $filename);

        } catch(\Exception $e) {

            return redirect()->route('admin.chat_asset_payments.index')->with('flash_error' , $e->getMessage());

        }

    }

}
