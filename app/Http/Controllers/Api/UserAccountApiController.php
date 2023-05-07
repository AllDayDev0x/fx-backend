<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User, App\Models\PostPayment, App\Models\Follower, App\Models\UserSubscription, App\Models\VerificationCode;

use App\Models\Post, App\Models\PostFile, App\Models\PromoCode, App\Models\VideoCallPayment, App\Models\AudioCallPayment;

use App\Models\CategoryDetail, App\Models\UserSubscriptionPayment, App\Models\PostLike, App\Models\UserWallet, App\Models\UserTip, App\Models\UserLoginSession;

use App\Models\ChatAssetPayment, App\Models\Category, App\Models\LiveVideo;

use App\Models\{ Order, OrderPayment, LiveVideoPayment, UserWalletPayment };

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\CommonRepository as CommonRepo;

use Carbon\Carbon;

use Illuminate\Validation\Rule;


class UserAccountApiController extends Controller
{
 	protected $loginUser;

    protected $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method two_step_auth_resend_code()
     *
     * @uses To resend the two step verification code
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function two_step_auth_resend_code(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'email' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::where('email', $request->email)->first();

            // Check the user details 

            if(!$user) {

                throw new Exception(api_error(1002), 1002);

            }

            if($user->is_two_step_auth_enabled){

                VerificationCode::where('email',$user->email)->delete();

                $verification_code = new VerificationCode;

                $verification_code->email = $user->email ?: $verification_code->email;

                $verification_code->username = $user->username;

                $verification_code->code = rand ( 1000 , 9999 );

                $verification_code->status = APPROVED;

                if($verification_code->save()){

                    DB::commit();

                    $email_data['subject'] = tr('two_step_authentication' , Setting::get('site_name'));

                    $email_data['email']  = $user->email ?? tr('n_a');

                    $email_data['name']  = $user->name ?? tr('n_a');

                    $email_data['verification_code']  = $verification_code->code ?? tr('n_a');

                    $email_data['page'] = "emails.users.two-step-verification-code";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                }

                $response = ['success' => true, 'message' => api_error(240), 'code' => 240];

                return response()->json($response, 200);

            }else{

                $response = ['success' => false, 'message' => api_error(242), 'code' => 242];

                return response()->json($response, 200);

            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method two_step_auth_update()
     *
     * @uses To enable/disable the two step verification
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id,email,password
     *
     * @return JSON Response
     */
    public function two_step_auth_update(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'password' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::where('id', $request->id)->first();

            // Check the user details 

            if(!$user) {

                throw new Exception(api_error(1002), 1002);

            }
            
            if(Hash::check($request->password, $user->password)) {

                $user->is_two_step_auth_enabled = $user->is_two_step_auth_enabled ? NO : YES;

                $user->save();

                $data = User::find($user->id);

                $code = $user->is_two_step_auth_enabled == YES ? 250 : 251; 
                
                DB::commit();

                return $this->sendResponse(api_success($code), $code, $data);

            } else {

                throw new Exception(api_error(102), 102);

            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method two_step_auth_login()
     *
     * @uses To login via two step verification
     *
     * @created Subham
     *
     * @updated
     *
     * @param request email,code
     *
     * @return JSON Response
     */
    public function two_step_auth_login(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'email' => 'required',
                'code' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::where('email', $request->email)
                    ->orWhere('username', $request->email)->first();

            // Check the user details 

            if(!$user) {

                throw new Exception(api_error(1002), 1002);

            }

            $verification_code = VerificationCode::where('email',$request->email)->orWhere('username', $request->email)->first();

            if($request->code == $verification_code->code){

                VerificationCode::where('email',$request->email)->delete();

                $session = UserLoginSession::firstWhere([
                    'user_id' => $user->id,  
                    'device_unique_id' => $request->device_unique_id
                ]);

                if($session) {

                    $user->token = $session->token;

                    $user->save();

                }

                $data = User::find($user->id);
                
                DB::commit();

                return $this->sendResponse(api_success(101), 101, $data);

            } else {

                throw new Exception(api_error(241), 241);

            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method login_session_index()
     *
     * @uses To display all the session
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function login_session_index(Request $request) {

        try {

            $base_query = $total_query = UserLoginSession::where('user_id',$request->id)->orderBy('user_login_sessions.last_session', 'desc');

            $data['total'] = $total_query->count() ?? 0;

            $session = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($session as $key => $value) {

                $value->last_session = common_date($value->last_session, $this->timezone);

                $value->session_image = Helper::get_login_session_image($value->device_type,$value->browser_type);
            
            }

            $data['session'] = $session ?? [];

            $data['user'] = $this->loginUser;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method register()
     *
     * @uses Registered user can register through manual or social login
     * 
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param Form data
     *
     * @return Json response with user details
     */
    public function register(Request $request) {
        try {

            DB::beginTransaction();

            $rules = [
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'device_token' => '',
                'login_by' => 'required|in:manual,facebook,google,apple,linkedin,instagram',
                'device_model' => 'nullable',
                // 'device_unique_id' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $allowed_social_logins = ['facebook', 'google', 'apple', 'linkedin', 'instagram'];

            if(in_array($request->login_by, $allowed_social_logins)) {

                // validate social registration fields
                $rules = [
                    'social_unique_id' => 'required',
                    'first_name' => 'nullable|max:255|min:2',
                    'last_name' => 'nullable|max:255|min:1',
                    'email' => 'required|email|max:255',
                    'mobile' => 'nullable|digits_between:6,13',
                    'picture' => '',
                    'gender' => 'nullable|in:male,female,others,rather-not-select',
                ];

                Helper::custom_validator($request->all(), $rules);

            } else {

                $rules = [

                        'name' => 'required|max:255|min:2',
                        'username' => 'required|max:255|min:1',
                        // 'first_name' => 'required|max:255|min:2',
                        // 'last_name' => 'required|max:255|min:1',
                        'email' => 'required|email|regex:/(.+)@(.+)\.(.+)/i|max:255|min:2',
                        'password' => 'required|min:6',
                        'picture' => 'mimes:jpeg,jpg,bmp,png',
                        'category_id' => 'nullable|integer|exists:categories,id',
                    ];

                Helper::custom_validator($request->all(), $rules);
                // validate email existence

                $rules = ['email' => 'unique:users,email'];

                Helper::custom_validator($request->all(), $rules);

            }

            $user_details = User::firstWhere('username','=',$request->username);
           
            if($user_details) {

                throw new Exception(api_error(181), 181);

            }

            $user = User::firstWhere('email' , $request->email);

            $send_email = NO;

            // Creating the user

            if(!$user) {

                $user = new User;

                register_mobile($request->device_type);

                $send_email = YES;

                $user->registration_steps = 1;

            } else {

                if(in_array($user->status, [USER_PENDING , USER_DECLINED])) {

                    throw new Exception(api_error(1000), 1000);
                
                }

            }

            if($request->login_by != 'manual' && $send_email == NO) {

                $user->name = $user->name;

                $user->first_name = $user->first_name;

                $user->last_name =  $user->last_name;

            } else {

                 $user->name = $request->name ?? $user->name;

                $user->first_name = $request->first_name ? $request->first_name : ($user->first_name ? $user->first_name : '');

                $user->last_name =  $request->last_name ? $request->last_name : ($user->last_name ? $user->last_name : '');

            }

            $user->email = $request->email ?? $user->email;

            $user->mobile = $request->mobile ?? "";

            $user->username = $request->username ? : "";

            if($request->has('password')) {

                $user->password = Hash::make($request->password ?: "123456");

            }

            $user->gender = $request->gender ?? "rather-not-select";

            $check_device_exist = User::firstWhere('device_token', $request->device_token);

            if($check_device_exist) {

                $check_device_exist->device_token = "";

                $check_device_exist->save();
            }

            $user->device_token = $request->device_token ?: "";

            $user->device_type = $request->device_type ?: DEVICE_WEB;

            $user->login_by = $request->login_by ?: 'manual';

            $user->social_unique_id = $request->social_unique_id ?: '';

            $user->timezone = $request->timezone ?? "America/New_York";

            $user->latitude = $request->latitude ?? '';

            $user->longitude = $request->longitude ?? '';

            // Upload picture

            if($request->login_by == 'manual') {

                if($request->hasFile('picture')) {

                    $user->picture = Helper::storage_upload_file($request->file('picture') , PROFILE_PATH_USER);

                }

            } else {

                if($send_email == YES){

                    $user->picture = $request->picture ?: ($user->picture ?? asset('placeholder.jpeg'));

                }

            }   

            if($user->save()) {

                if($request->device_model){

                    // UserLoginSession::where('user_id', $user->id)->update(['is_current_session' => IS_CURRENT_SESSION_NO]);

                    $session = UserLoginSession::firstWhere([
                    'user_id' => $user->id,
                    'device_unique_id' => $request->device_unique_id
                    ]) ?? new UserLoginSession();

                    $session->device_type = $request->device_type ?: $session->device_type;

                    $session->device_model = $request->device_model ?: $session->device_model;

                    $session->device_unique_id = $request->device_unique_id ?: $session->device_unique_id;

                    $session->device_token = $request->device_token ?: $session->device_token;

                    $session->browser_type = $request->browser_type ?: $session->browser_type;

                    $session->status = APPROVED;

                    $session->ip_address = $request->ip() ?: $session->ip_address;

                    $session->is_current_session = IS_CURRENT_SESSION;

                    $session->last_session = Carbon::now() ?: $session->last_session;

                    $session->user_id = $user->id ?? $session->user_id;

                    $session->save();

                } else {

                    CommonRepo::create_default_user_login_session($request, $user->id);

                    $request->request->add(['device_model' => $user->id, 'device_unique_id' => $user->id]);

                }

                $session = UserLoginSession::firstWhere([
                    'user_id' => $user->id, 
                    'device_unique_id' => $request->device_unique_id
                ]);

                if($session) {

                  $user->token = $session->token;

                  $user->save();
                }

                // Send welcome email to the new user:

                if($request->category_id){

                  $category = CategoryDetail::where('category_id',$request->category_id)
                                ->where('user_id', $user->id)
                                ->where('type', CATEGORY_TYPE_PROFILE)
                                ->first() ?? new CategoryDetail;

                  $category->category_id = $request->category_id;

                  $category->user_id = $user->id;

                  $category->save();
                }

                if($request->referral_code && Setting::get('is_referral_enabled')) {

                    CommonRepo::referral_register($request->referral_code, $user);

                }

                if($send_email) {

                    if($user->login_by == 'manual') {

                        $email_data['subject'] = tr('user_welcome_title').' '.Setting::get('site_name');

                        $email_data['page'] = "emails.users.welcome";

                        $email_data['data'] = $user;

                        $email_data['email'] = $user->email;

                        $email_data['name'] = $user->name;

                        $email_data['verification_code'] = $user->verification_code;

                        $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    }

                }

                if(in_array($user->status , [USER_DECLINED , USER_PENDING])) {
                
                    $response = ['success' => false , 'error' => api_error(1000) , 'error_code' => 1000];

                    DB::commit();

                    return response()->json($response, 200);
               
                }

                $data = User::find($user->id);

                $data->device_model = $request->device_model;

                $data->device_unique_id = $request->device_unique_id;

                if($user->is_email_verified == USER_EMAIL_VERIFIED) {

                    counter(); // For site analytics. Don't remove

                    $response = ['success' => true, 'message' => api_success(101), 'data' => $data];

                } else {

                    $response = ['success' => true, 'message' => api_error(1001), 'code' => 1001, 'data' => $data];

                    DB::commit();

                    return response()->json($response, 200);

                }

            } else {

                throw new Exception(api_error(103), 103);

            }

            DB::commit();

            return response()->json($response, 200);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method login()
     *
     * @uses Registered user can login using their email & password
     * 
     * @created Bhawya N 
     *
     * @updated Subham
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function login(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'device_token' => 'nullable',
                'device_type' => 'required|in:'.DEVICE_ANDROID.','.DEVICE_IOS.','.DEVICE_WEB,
                'login_by' => 'required|in:manual,facebook,google,apple,linkedin,instagram',
                'device_model' => 'nullable',
                // 'device_unique_id' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $rules = [
                'email' => 'required',
                'password' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::where('email', $request->email)->orWhere('username', $request->email)->first();
            
            $is_email_verified = YES;

            // Check the user details 

            if(!$user) {

                throw new Exception(api_error(1002), 1002);

            }

            // check the user password

            if(!Hash::check($request->password, $user->password)) {

                throw new Exception(api_error(102), 102);

            }

            // check the user approved status

            if($user->status != USER_APPROVED) {

                throw new Exception(api_error(1000), 1000);

            }

            if($user->is_email_verified != USER_EMAIL_VERIFIED) {

                $session = UserLoginSession::firstWhere([
                    'user_id' => $user->id, 
                    'device_unique_id' => $request->device_unique_id
                ]) ?? new UserLoginSession();

                $session->device_type = $request->device_type ?: $session->device_type;

                $session->device_model = $request->device_model ?: $session->device_model;

                $session->device_unique_id = $request->device_unique_id ?: $session->device_unique_id;

                $session->device_token = $request->device_token ?: $session->device_token;

                $session->browser_type = $request->browser_type ?: $session->browser_type;

                $session->status = APPROVED;

                $session->ip_address = $request->ip() ?: $session->ip_address;

                $session->is_current_session = IS_CURRENT_SESSION;

                $session->last_session = Carbon::now() ?: $session->last_session;

                $session->user_id = $user->id ?? $session->user_id;

                if ($session->id) {
                    
                    $session->token = Helper::generate_token();

                    $session->token_expiry = Helper::generate_token_expiry();
                }

                $session->save();

                \Log::info("Session token".print_r($session, true));

                $user->token = $session->token;

                $user->save();

                DB::commit();

                $data = User::find($user->id);

                $response = ['success' => true, 'message' => api_error(1001), 'code' => 1001, 'data' => $data];

                return response()->json($response, 200);

            }

            // Generate new tokens
            
            // $user->token = Helper::generate_token();

            $user->token_expiry = Helper::generate_token_expiry();
            
            // Save device details

            $check_device_exist = User::firstWhere('device_token', $request->device_token);
            
            if($check_device_exist) {

                $check_device_exist->device_token = "";
                
                $check_device_exist->save();
            }
            
            $user->device_token = $request->device_token ?? $user->device_token;

            $user->device_type = $request->device_type ?? $user->device_type;

            $user->login_by = $request->login_by ?? $user->login_by;

            $user->timezone = $request->timezone ?? $user->timezone;

            $user->save();

            $token = $user->token;

            if($request->device_model) {

                // UserLoginSession::where('user_id', $user->id)->update(['is_current_session' => IS_CURRENT_SESSION_NO]);

                $session = UserLoginSession::firstWhere([
                    'user_id' => $user->id, 
                    'device_unique_id' => $request->device_unique_id
                ]) ?? new UserLoginSession();

                $session->device_type = $request->device_type ?: $session->device_type;

                $session->device_model = $request->device_model ?: $session->device_model;

                $session->device_unique_id = $request->device_unique_id ?: $session->device_unique_id;

                $session->device_token = $request->device_token ?: $session->device_token;

                $session->browser_type = $request->browser_type ?: $session->browser_type;

                $session->status = APPROVED;

                $session->ip_address = $request->ip() ?: $session->ip_address;

                $session->is_current_session = IS_CURRENT_SESSION;

                $session->last_session = Carbon::now() ?: $session->last_session;

                $session->user_id = $user->id ?? $session->user_id;

                $session->token = Helper::generate_token();

                $session->token_expiry = Helper::generate_token_expiry();

                $session->save();

            } else {

                CommonRepo::create_default_user_login_session($request, $user->id);

                $request->request->add(['device_model' => $user->id]);
            }

            $check_session = UserLoginSession::firstWhere([
                    'user_id' => $user->id, 
                    'device_unique_id' => $request->device_unique_id
                ]);

            if($check_session) {

                $user->token = $check_session->token;

                $user->save();
            }

            if($user->is_two_step_auth_enabled){

                VerificationCode::where('email',$user->email)->delete();

                $verification_code = new VerificationCode;

                $verification_code->email = $user->email ?: $verification_code->email;

                $verification_code->username = $user->username;

                $verification_code->code = rand ( 1000 , 9999 );

                $verification_code->status = APPROVED;

                if($verification_code->save()){

                    DB::commit();

                    $email_data['subject'] = tr('two_step_authentication' , Setting::get('site_name'));

                    $email_data['email']  = $user->email ?? tr('n_a');

                    $email_data['name']  = $user->name ?? tr('n_a');

                    $email_data['verification_code']  = $verification_code->code ?? tr('n_a');

                    $email_data['page'] = "emails.users.two-step-verification-code";

                    $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                }

                return $this->sendResponse(api_success(240), $success_code = 240, $data = $user);

            }

            DB::commit();

            counter(); // For site analytics. Don't remove

            $data = User::find($user->id);

            $data->device_model = $request->device_model;

            $data->device_unique_id = $request->device_unique_id;

            return $this->sendResponse(api_success(101), 101, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method username_validation()
     *
     * @uses
     * 
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Email & Password
     *
     * @return Json response with user details
     */
    public function username_validation(Request $request) {

        try {
            
            $rules = [ 'username' => 'required|regex:/^[a-zA-Z0-9-._]+$/u' ];

            $custom_errors = [ 'regex' => api_error(265) ];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $user = User::where(['username' => $request->username])->exists();
           
            if($user) {

                throw new Exception(api_error(181), 181);

            }
            
            return $this->sendResponse(api_success(161), 161, []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /**
     * @method forgot_password()
     *
     * @uses If the user forgot his/her password he can hange it over here
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - Email id
     *
     * @return send mail to the valid user
     */
    
    public function forgot_password(Request $request) {

        try {

            DB::beginTransaction();

            // Check email configuration and email notification enabled by admin

            if(Setting::get('is_email_notification') != YES ) {

                throw new Exception(api_error(106), 106);
                
            }
            
            $rules = ['email' => 'required|email|exists:users,email']; 

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::firstWhere('email' , $request->email);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            if($user->login_by != 'manual') {

                throw new Exception(api_error(118), 118);
                
            }

            // check email verification

            if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(api_error(1001), 1001);
            }

            // Check the user approve status

            if(in_array($user->status , [USER_DECLINED , USER_PENDING])) {
                throw new Exception(api_error(1000), 1000);
            }

            $token = app('auth.password.broker')->createToken($user);

            \App\Models\PasswordReset::where('email', $user->email)->delete();

            \App\Models\PasswordReset::insert([
                'email'=>$user->email,
                'token'=>$token,
                'created_at'=>Carbon::now()
            ]);

            $email_data['subject'] = tr('reset_password_title' , Setting::get('site_name'));

            $email_data['email']  = $user->email;

            $email_data['name']  = $user->name;

            $email_data['page'] = "emails.users.forgot-password";

            $email_data['url'] = Setting::get('frontend_url')."reset-password/".$token;
            
            $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            DB::commit();

            return $this->sendResponse(api_success(102), $success_code = 102, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }


    /**
     * @method reset_password()
     *
     * @uses To reset the password
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param object $request - Email id
     *
     * @return send mail to the valid user
     */
    
    public function reset_password(Request $request) {

        try {

            $rules = [
                'password' => 'required|confirmed|min:6',
                'reset_token' => 'required|string',
                'password_confirmation'=>'required'
            ]; 

            Helper::custom_validator($request->all(), $rules, $custom_errors =[]);

            DB::beginTransaction();

            $password_reset = \App\Models\PasswordReset::where('token', $request->reset_token)->first();

            if(!$password_reset){

                throw new Exception(api_error(163), 163);
            }
            
            $user = User::where('email', $password_reset->email)->first();

            $user->password = \Hash::make($request->password);

            $user->save();

            \App\Models\PasswordReset::where('email', $user->email) ->delete();

            DB::commit();

            $session = UserLoginSession::firstWhere([
                    'user_id' => $user->id, 
                    'device_unique_id' => $request->device_unique_id
                ]);

            if($session) {

                $user->token = $session->token;

                $user->save();

            }

            $data = $user;

            return $this->sendResponse(api_success(153), $success_code = 153, $data);

        } catch(Exception $e) {

             DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }


   }

    /**
     * @method change_password()
     *
     * @uses To change the password of the user
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - Password & confirm Password
     *
     * @return json response of the user
     */
    public function change_password(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'password' => 'required|confirmed|min:6',
                'old_password' => 'required|min:6',
            ]; 

            Helper::custom_validator($request->all(), $rules, $custom_errors =[]);

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            if($user->login_by != "manual") {

                throw new Exception(api_error(118), 118);
                
            }

            if(Hash::check($request->old_password,$user->password)) {

                $user->password = Hash::make($request->password);
                
                if($user->save()) {

                    DB::commit();

                    $email_data['subject'] = tr('change_password_email_title' , Setting::get('site_name'));

                    $email_data['email']  = $user->email;

                    $email_data['page'] = "emails.users.change-password";

                    // $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    return $this->sendResponse(api_success(104), $success_code = 104, $data = []);
                
                } else {

                    throw new Exception(api_error(103), 103);   
                }

            } else {

                throw new Exception(api_error(108) , 108);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method profile()
     *
     * @uses To display the user details based on user  id
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function profile(Request $request) {

        try {

            $user = User::firstWhere('id' , $request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $user->updated_formatted = common_date($user->updated_at, $this->timezone, 'd M Y');

            $is_only_wallet_payment = Setting::get('is_only_wallet_payment');

            $user_subscription = \App\Models\UserSubscription::where('user_id', $user->id)
              ->when($is_only_wallet_payment == NO, function ($q) use ($is_only_wallet_payment) {
                  return $q->OriginalResponse();
              })
              ->when($is_only_wallet_payment == YES, function($q) use ($is_only_wallet_payment) {
                  return $q->TokenResponse();
              })->first();

            if($is_only_wallet_payment) {

              $user->video_call_amount = $user->video_call_token;

              $user->audio_call_amount = $user->audio_call_token;

            }
            
            $user->monthly_amount = $user_subscription->monthly_amount ?? 0.00;

            $user->yearly_amount = $user_subscription->yearly_amount ?? 0.00;

            $video_query = $image_query = \App\Models\PostFile::where('user_id', $request->id);

            $user->total_videos = $video_query->where('file_type', POSTS_VIDEO)->count();

            $user->total_images = $image_query->where('file_type', POSTS_IMAGE)->count();

            $user->created_formatted = common_date($user->created_at, $this->timezone, 'd M Y');

            $user->is_one_to_one_call_enabled = Setting::get('is_one_to_one_call_enabled') ?? 0;

            $user->is_one_to_many_call_enabled = Setting::get('is_one_to_many_call_enabled') ?? 0;
         
            $user_category = CategoryDetail::where('user_id', $request->id)->where('type', CATEGORY_TYPE_PROFILE)->first();
            
            $categories = selected(Category::Approved()->get(), $user_category->category_id ?? 0, 'id');

            $user->categories = $categories;

            $user->category_id = $user_category->category_id ?? 0;

            // only for mobile apps
            $user->selected_category = $user_category ? Category::find($user_category->category_id) : emptyObject();

            $user_live_videos = LiveVideo::where(['user_id' => $request->id, 'is_streaming' => IS_STREAMING_YES, 'status' => VIDEO_STREAMING_ONGOING])
                                        ->orderBy('created_at', 'desc');

            $user->is_user_live = $user_live_videos->count() ? IS_STREAMING_YES : IS_STREAMING_NO;

            $user->ongoing_live_video = $user_live_videos->first();

            $user->payment_info = CommonRepo::subscriptions_user_payment_check($user, $request);

            return $this->sendResponse($message = "", $success_code = "", $user);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getLine());

        }
    
    }
 
    /**
     * @method update_profile()
     *
     * @uses To update the user details
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param objecct $request : User details
     *
     * @return json response with user details
     */
    public function update_profile(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'first_name' => 'nullable|max:255',
                    'last_name' => 'nullable|max:255',
                    'email' => 'email|unique:users,email,'.$request->id.'|regex:/(.+)@(.+)\.(.+)/i|max:255',
                    'username' => 'nullable|unique:users,username,'.$request->id.'|max:255|regex:/^[a-zA-Z0-9-._]+$/u',
                    'mobile' => 'nullable|digits_between:6,13',
                    'picture' => 'nullable|mimes:jpeg,jpg,bmp,png',
                    'cover' => 'nullable|mimes:jpeg,jpg,bmp,png',
                    'gender' => 'nullable|in:male,female,others,rather-not-select',
                    'height' => 'nullable|numeric',
                    'weight' => 'nullable|numeric',
                    'device_token' => '',
                    'ios_theme' => 'nullable|numeric|in:0,1,2',
                    'monthly_amount' => 'nullable|numeric|min:0',
                    'yearly_amount' => 'nullable|numeric|min:0',
            ];

            $custom_errors = [ 'regex' => api_error(265) ];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            // Validation end
            
            $user = User::find($request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $user_details = User::where('id', '!=' , $request->id)
                ->firstWhere('username','=',$request->username);
           
            if($user_details) {

                throw new Exception(api_error(181), 181);

            }

            $user->name = $request->name ?: $user->name;

            $user->username = $request->username ? : $user->username;

            $user->first_name = $request->first_name ?: $user->first_name;

            $user->last_name = $request->last_name ?: $user->last_name;
            
            if($request->has('email')) {

                $user->email = $request->email;
            }

            $user->ios_theme = $request->ios_theme ?? $user->ios_theme;
            
            $user->mobile = $request->mobile ?: $user->mobile;

            $user->about = $request->filled('about') ? $request->about : $user->about;

            $user->is_online_status = $request->filled('is_online_status') ? $request->is_online_status : $user->is_online_status;

            $user->default_payment_method = $request->filled('default_payment_method') ? $request->default_payment_method : $user->default_payment_method;

            $user->gender = $request->filled('gender') ? $request->gender : $user->gender;

            $user->address = $request->filled('address') ? $request->address : $user->address;

            $user->eyes_color = $request->filled('eyes_color') ? $request->eyes_color : $user->eyes_color;

            $user->height = $request->filled('height') ? $request->height : $user->height;
            
            $user->weight = $request->filled('weight') ? $request->weight : $user->weight;

            $social_links = Helper::get_social_medias();

            foreach($social_links as $social_link) {

                $user->$social_link = $request->filled($social_link) ? $request->$social_link : $user->$social_link;

            }
            
            if(Setting::get('is_only_wallet_payment')) {

              $user->video_call_token = $request->video_call_amount ?? 0;

              $user->audio_call_token = $request->audio_call_amount ?? 0;

              $user->video_call_amount = $user->video_call_token * Setting::get('token_amount');

              $user->audio_call_amount = $user->audio_call_token * Setting::get('token_amount');

            } else {

              $user->video_call_amount = $request->video_call_amount ?? ($user->video_call_amount ?: 0.00);

              $user->audio_call_amount = $request->audio_call_amount ?? ($user->audio_call_amount ?: 0.00);

            }

            $user->latitude = $request->filled('latitude') ? $request->latitude : $user->latitude;

            $user->longitude = $request->filled('longitude') ? $request->longitude : $user->longitude;

            $user->content_creator_step = $user->content_creator_step >= CONTENT_CREATOR_BILLING_UPDATED ? CONTENT_CREATOR_SUBSCRIPTION_UPDATED : $user->content_creator_step;

            // Upload picture
            if($request->hasFile('picture') != "") {

                Helper::storage_delete_file($user->picture, PROFILE_PATH_USER); // Delete the old pic

                $user->picture = Helper::storage_upload_file($request->file('picture'), PROFILE_PATH_USER);
            
            }

            if($request->hasFile('cover') != "") {

                Helper::storage_delete_file($user->cover, PROFILE_PATH_USER); // Delete the old pic

                $user->cover = Helper::storage_upload_file($request->file('cover'), PROFILE_PATH_USER);
            
            }

            if($user->save()) {

                $user_subscription = \App\Models\UserSubscription::where('user_id', $request->id)->first();

                if($request->category_id) {

                    $category = CategoryDetail::where('user_id', $user->id)->where('type', CATEGORY_TYPE_PROFILE)->first() ?? new CategoryDetail;

                    $category->category_id = $request->category_id;

                    $category->user_id = $user->id;

                    $category->save();
                }

                DB::commit();

                if($request->filled('monthly_amount') || $request->filled('yearly_amount')) {

                    // Check the user is eligibility

                    $account_response = \App\Repositories\CommonRepository::user_premium_account_check($user)->getData();

                    if(!$account_response->success) {

                        throw new Exception($account_response->error, $account_response->error_code);
                    }

                    if(!$user_subscription) {

                        $user_subscription = new \App\Models\UserSubscription;

                        $change_expiry_user_ids = \App\Models\UserSubscriptionPayment::where('user_subscription_id', 0)->where('to_user_id', $request->id)->pluck('from_user_id');

                        \App\Models\Follower::whereIn('user_id', $change_expiry_user_ids)->where('follower_id', $request->id)->delete();

                        \App\Models\UserSubscriptionPayment::where('user_subscription_id', 0)->where('to_user_id', $request->id)->update(['is_current_subscription' => NO, 'expiry_date' => date('Y-m-d H:i:s'), 'cancel_reason' => 'Model added subscription']);

                    }

                    if($user_subscription->monthly_amount == 0.00 || $user_subscription->yearly_amount == 0.00) {

                        if($request->monthly_amount > 0.00 || $request->yearly_amount > 0.00) {

                            $change_expiry_user_ids = \App\Models\UserSubscriptionPayment::where('user_subscription_id', 0)->where('to_user_id', $request->id)->pluck('from_user_id')->implode(',') ?? "";

                            \App\Models\Follower::whereIn('follower_id', [$change_expiry_user_ids])->where('user_id', $request->id)->delete();
                            
                            \App\Models\UserSubscriptionPayment::where('user_subscription_id', 0)->where('to_user_id', $request->id)->update(['is_current_subscription' => NO, 'expiry_date' => date('Y-m-d H:i:s'), 'cancel_reason' => 'Model added subscription']);
                        }

                    }

                    $user_subscription->user_id = $request->id;

                    if(Setting::get('is_only_wallet_payment')) {

                      $user_subscription->monthly_token = $request->filled('monthly_amount') ? $request->monthly_amount: ($user_subscription->monthly_token ?: 0.00);

                      $user_subscription->yearly_token = $request->filled('yearly_amount') ? $request->yearly_amount: ($user_subscription->yearly_token ?: 0.00);

                      $user_subscription->monthly_amount = $user_subscription->monthly_token * Setting::get('token_amount');

                      $user_subscription->yearly_amount = $user_subscription->yearly_token * Setting::get('token_amount');

                    } else {

                      $user_subscription->monthly_amount = $request->filled('monthly_amount') ? $request->monthly_amount : ($user_subscription->monthly_amount ?: 0.00);

                      $user_subscription->yearly_amount = $request->filled('yearly_amount')? $request->yearly_amount : ($user_subscription->yearly_amount ?: 0.00);

                    }


                    $user_subscription->save();

                    $user->user_account_type = USER_PREMIUM_ACCOUNT;

                    $user->is_content_creator = CONTENT_CREATOR;

                    $user->content_creator_step = CONTENT_CREATOR_APPROVED;

                    $user->save();

                    Log::info("user".print_r($user, true));

                    DB::commit();

                }

                $data = User::find($user->id);

                $user_category = CategoryDetail::where('user_id', $request->id)->where('type', CATEGORY_TYPE_PROFILE)->first();
            
                $categories = selected(Category::Approved()->get(), $user_category->category_id ?? 0, 'id');

                $data->categories = $categories;

                $data->category_id = $user_category->category_id ?? 0;

                $data->selected_category = $user_category ? Category::find($user_category->category_id) : emptyObject();

                $data->monthly_amount = $user_subscription->monthly_amount ?? 0.00;

                $data->yearly_amount = $user_subscription->yearly_amount ?? 0.00;

                $data->payment_info = CommonRepo::subscriptions_user_payment_check($user, $request);

                return $this->sendResponse($message = api_success(111), $success_code = 111, $data);

            } else {    

                throw new Exception(api_error(103), 103);
            
            }

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
   
    }

    /**
     * @method delete_account()
     * 
     * @uses Delete user account based on user id
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - Password and user id
     *
     * @return json with boolean output
     */

    public function delete_account(Request $request) {

        try {

            DB::beginTransaction();

            $request->request->add([ 
                'login_by' => $this->loginUser ? $this->loginUser->login_by : "manual",
            ]);

            // Validation start

            $rules = ['password' => 'required_if:login_by,manual'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
                
            }

            // The password is not required when the user is login from social. If manual means the password is required

            if($user->login_by == 'manual') {

                if(!Hash::check($request->password, $user->password)) {
         
                    throw new Exception(api_error(167), 167); 
                }
            
            }

            if($user->delete()) {

                DB::commit();

                return $this->sendResponse(api_success(103), $success_code = 103, $data = []);

            } else {

                throw new Exception(api_error(119), 119);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method logout()
     *
     * @uses Logout the user
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param 
     * 
     * @return
     */
    public function logout(Request $request) {
        
        if($request->device_model || $request->device_unique_id){

            $user = User::firstWhere('id' , $request->id);

            // UserLoginSession::where('user_id', $user->id)->update(['is_current_session' => IS_CURRENT_SESSION_NO]);

            $session = UserLoginSession::firstWhere([
                    'user_id' => $user->id, 
                    'device_unique_id' => $request->device_unique_id
                ]) ?? new UserLoginSession();

            $session->device_type = $request->device_type ?: $session->device_type;

            $session->device_model = $request->device_model ?: $session->device_model;

            $session->device_unique_id = $request->device_unique_id ?: $session->device_unique_id;

            $session->browser_type = $request->browser_type ?: $session->browser_type;

            $session->status = APPROVED;

            $session->ip_address = $request->ip() ?: $session->ip_address;

            $session->is_current_session = IS_CURRENT_SESSION_NO;

            $session->last_session = Carbon::now() ?: $session->last_session;

            $session->user_id = $user->id ?? $session->user_id;

            $session->save();

        }

        \Cache::forget($request->id);

        return $this->sendResponse(api_success(106), 106);

    }

    /**
     * @method cards_list()
     *
     * @uses get the user payment mode and cards list
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer id
     * 
     * @return
     */

    public function cards_list(Request $request) {

        try {

            $user_cards = \App\Models\UserCard::where('user_id' , $request->id)->get();

            $card_payment_mode = $payment_modes = [];

            $card_payment_mode['name'] = "Card";

            $card_payment_mode['payment_mode'] = "card";

            $card_payment_mode['is_default'] = 1;

            array_push($payment_modes , $card_payment_mode);

            $data['payment_modes'] = $payment_modes;   

            $data['cards'] = $user_cards ? $user_cards : []; 

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }
    
    /**
     * @method cards_add()
     *
     * @uses used to add card to the user
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param card_token
     * 
     * @return JSON Response
     */
    public function cards_add(Request $request) {

        try {

            if(Setting::get('stripe_secret_key')) {

                \Stripe\Stripe::setApiKey(Setting::get('stripe_secret_key'));

            } else {

                throw new Exception(api_error(121), 121);

            }

            // Validation start

            $rules = ['card_token' => 'required'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            
            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
                
            }

            DB::beginTransaction();

            // Get the key from settings table

            $customer = \Stripe\Customer::create([
                    // "card" => $request->card_token,
                    // "card" => 'tok_visa',
                    "email" => $user->email,
                    "description" => "Customer for ".Setting::get('site_name'),
                    // 'payment_method' => $request->card_token,
                    // 'default_payment_method'
                    // 'source' => $request->card_token
                ]);

            $stripe = new \Stripe\StripeClient(Setting::get('stripe_secret_key'));

            $intent = \Stripe\SetupIntent::create([
              'customer' => $customer->id,
              'payment_method' => $request->card_token
            ]);

            $stripe->setupIntents->confirm($intent->id,['payment_method' => $request->card_token]);


            $retrieve = $stripe->paymentMethods->retrieve($request->card_token, []);
            
            $card_info_from_stripe = $retrieve->card ? $retrieve->card : [];

            // \Log::info("card_info_from_stripe".print_r($card_info_from_stripe, true));

            if($customer && $card_info_from_stripe) {

                $customer_id = $customer->id;

                $card = new \App\Models\UserCard;

                $card->user_id = $request->id;

                $card->customer_id = $customer_id;

                $card->card_token = $request->card_token ?? "NO-TOKEN";

                $card->card_type = $card_info_from_stripe->brand ?? "";

                $card->last_four = $card_info_from_stripe->last4 ?? '';

                $card->card_holder_name = $request->card_holder_name ?: $this->loginUser->name;

                // $cards->month = $card_details_from_stripe->exp_month ?? "01";

                // $cards->year = $card_details_from_stripe->exp_year ?? "01";

                // Check is any default is available

                $check_card = \App\Models\UserCard::where('user_id',$request->id)->count();

                $card->is_default = $check_card ? NO : YES;

                if($card->save()) {

                    if($user) {

                        // $user->user_card_id = $check_card ? $user->user_card_id : $card->id;

                        $user->save();
                    }

                    $data = \App\Models\UserCard::firstWhere('id' , $card->id);

                    DB::commit();

                    return $this->sendResponse(api_success(105), 105, $data);

                } else {

                    throw new Exception(api_error(114), 114);
                    
                }
           
            } else {

                throw new Exception(api_error(121) , 121);
                
            }

        } catch(Stripe_CardError | Stripe_InvalidRequestError | Stripe_AuthenticationError | Stripe_ApiConnectionError | Stripe_Error $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode() ?: 101);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode() ?: 101);
        }

    }

    /**
     * @method cards_delete()
     *
     * @uses delete the selected card
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer user_card_id
     * 
     * @return JSON Response
     */

    public function cards_delete(Request $request) {

        try {

            DB::beginTransaction();

            // validation start

            $rules = [
                'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // validation end

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            \App\Models\UserCard::where('id', $request->user_card_id)->delete();

            if($user->payment_mode = CARD) {

                // Check he added any other card

                if($check_card = \App\Models\UserCard::firstWhere('user_id' , $request->id)) {

                    $check_card->is_default =  DEFAULT_TRUE;

                    $user->user_card_id = $check_card->id;

                    $check_card->save();

                } else { 

                    $user->payment_mode = COD;

                    $user->user_card_id = DEFAULT_FALSE;
                
                }
           
            }

            // Check the deleting card and default card are same

            if($user->user_card_id == $request->user_card_id) {

                $user->user_card_id = DEFAULT_FALSE;

                $user->save();
            }
            
            $user->save();
                
            DB::commit();

            return $this->sendResponse(api_success(109), 109, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method cards_default()
     *
     * @uses update the selected card as default
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function cards_default(Request $request) {

        try {

            DB::beginTransaction();

            // validation start

            $rules = [
                'user_card_id' => 'required|integer|exists:user_cards,id,user_id,'.$request->id,
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // validation end

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }
        
            $old_default_cards = \App\Models\UserCard::where('user_id' , $request->id)->where('is_default', YES)->update(['is_default' => NO]);

            $user_cards = \App\Models\UserCard::where('id' , $request->user_card_id)->update(['is_default' => YES]);

            $user->user_card_id = $request->user_card_id;

            $user->save();

            DB::commit();

            return $this->sendResponse(api_success(108), 108);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    } 

    /**
     * @method payment_mode_default()
     *
     * @uses update the selected card as default
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function payment_mode_default(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [ 'payment_mode' => [ 'required', Rule::in([COD, CARD, PAYPAL, BANK_TRANSFER, PAYMENT_OFFLINE, PAYMENT_MODE_WALLET, CCBILL, COINPAYMENT])] ];

            Helper::custom_validator($request->all(), $rules);

            $user = User::find($request->id);

            $user->payment_mode = $request->payment_mode ?: CARD;

            $user->save();           

            DB::commit();

            return $this->sendResponse($message = "Mode updated", $code = 200, $data = ['payment_mode' => $request->payment_mode]);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method user_premium_account_check()
     *
     * @uses check the user is eligiable for the premium acounts
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param integer id
     * 
     * @return JSON Response
     */
    public function user_premium_account_check(Request $request) {

        try {

            $user = User::find($request->id);

            $account_response = \App\Repositories\CommonRepository::user_premium_account_check($user)->getData();

            if(!$account_response->success) {

                throw new Exception($account_response->error, $account_response->error_code);
            }           

            return $this->sendResponse($message = $account_response->message, $code = 200, $data = $user);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method regenerate_email_verification_code()
     *
     * @uses 
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function regenerate_email_verification_code(Request $request) {

        try {

            DB::beginTransaction();

            $user = \App\Models\User::find($request->id);

            $user->verification_code = Helper::generate_email_code();

            $user->verification_code_expiry = \Helper::generate_email_expiry();

            $user->save();

            $email_data['subject'] = Setting::get('site_name');

            $email_data['page'] = "emails.users.verification-code";

            $email_data['data'] = $user;

            $email_data['email'] = $user->email;

            $email_data['verification_code'] = $user->verification_code;

            $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

            DB::commit();

            return $this->sendResponse($message = api_success(147), $code = 147, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method verify_email()
     *
     * @uses 
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function verify_email(Request $request) {

        try {

            DB::beginTransaction();
            
            $rules = ['verification_code' => 'required|min:6|max:6'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::find($request->id);

            if($user->verification_code != $request->verification_code) {

                throw new Exception(api_error(254), 254);

            }

            $user->is_email_verified = USER_EMAIL_VERIFIED;

            $session = UserLoginSession::firstWhere([
                'user_id' => $user->id, 
                'device_unique_id' => $request->device_unique_id
            ]);

            if($session) {

                $user->token = $session->token;

            }

            $user->save();

            DB::commit();

            $data = User::CommonResponse()->find($user->id);

            return $this->sendResponse($message = api_success(148), $code = 148, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method notifications_status_update()
     *
     * @uses To enable/disable notifications of email / push notification
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function notifications_status_update(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [ 'status' => [ 'required', 'numeric', Rule::in([YES, NO])] ];

            Helper::custom_validator($request->all(), $rules);
                
            $user = User::find($request->id);

            $user->is_email_notification = $user->is_push_notification = $request->status;

            $user->save();

            $data = \App\Models\User::firstWhere('id', $request->id);
            
            DB::commit();

            $code = $request->status == YES ? 245 : 246;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

     /**
     * @method feature_story_save()
     *
     * @uses Save Users Feature Story
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function feature_story_save(Request $request) {

        try {

            DB::beginTransaction();

            // $rules = ['file' => 'required|mimes:mp4']; 

            $rules = ['file' => 'required|file'];

            Helper::custom_validator($request->all(), $rules);
                
            $user = User::find($request->id);

            // Upload picture
            if($request->hasFile('file') != "") {

                Helper::storage_delete_file($user->featured_story, PROFILE_PATH_USER);

                $user->featured_story = Helper::storage_upload_file($request->file('file'), PROFILE_PATH_USER);
                
                $user->save();
            }

            $data = \App\Models\User::firstWhere('id', $request->id);
            
            DB::commit();

            return $this->sendResponse(api_success(243), 243, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method feature_story_delete()
     *
     * @uses Save Users Feature Story
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function feature_story_delete(Request $request) {

        try {

            DB::beginTransaction();
                
            $user = User::find($request->id);

            Helper::storage_delete_file($user->featured_story, PROFILE_PATH_USER);

            $user->featured_story = '';
            
            $user->save();

            $data = \App\Models\User::firstWhere('id', $request->id);
            
            DB::commit();

            return $this->sendResponse(api_success(247), 247, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method user_billing_accounts_list()
     *
     * @uses To list user billing accounts
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param
     *
     * @return json response with details
     */

    public function user_billing_accounts_list(Request $request) {

        try {

            $user_billing_accounts = \App\Models\UserBillingAccount::where('user_id', $request->id)->CommonResponse()->get();

            $data['billing_accounts'] = $user_billing_accounts;

            $data['total'] = $user_billing_accounts->count();

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_view()
     *
     * @uses Accounts Detailed view
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param
     *
     * @return json response with details
     */

    public function user_billing_accounts_view(Request $request) {

        try {

            $user_billing_accounts = \App\Models\UserBillingAccount::where('id', $request->user_billing_account_id)->CommonResponse()->get();

            $data['billing_accounts'] = $user_billing_accounts;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_save()
     *
     * @uses To save account details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_billing_accounts_save(Request $request) {

        try {

            DB::beginTransaction();

             // Validation start
            $rules = [
                'user_billing_account_id' => 'nullable|exists:user_billing_accounts,id',
                'first_name' => 'required',
                'last_name' => 'required',
                'account_number' => 'required|numeric',
                'bank_type' => 'nullable',
                'route_number' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $request->request->add(['user_id' => $request->id]);

            $user = User::find($request->user_id);   
            
            if(!$user) {

                throw new Exception(tr('user_not_found'), 101);
                
            }

            if($request->user_billing_account_id) {
                
                $user_billing_account = \App\Models\UserBillingAccount::updateOrCreate(['id' => $request->user_billing_account_id, 'account_number' => $request->account_number, 'user_id' => $request->id], $request->all());

            } else {
                
                $user_billing_account = \App\Models\UserBillingAccount::updateOrCreate(['account_number' => $request->account_number, 'user_id' => $request->id], $request->all());

                if(\App\Models\UserBillingAccount::where('user_id', $request->id)->count() <= 1) {

                    $user_billing_account->is_default = YES;

                }

            }

            if ($user_billing_account->save()) {
                
                $user->content_creator_step = CONTENT_CREATOR_BILLING_UPDATED;

                $user->save();

                DB::commit();
            }

            return $this->sendResponse(api_success(112), $success_code = 112, $user_billing_account);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_delete()
     *
     * @uses To delete account details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_billing_accounts_delete(Request $request) {

        try {

            DB::beginTransaction();

             // Validation start

            $rules = ['user_billing_account_id' => 'required|exists:user_billing_accounts,id,user_id,'.$request->id];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end
            $user_billing_account = \App\Models\UserBillingAccount::destroy($request->user_billing_account_id);

            DB::commit();

            $data['user_billing_account_id'] = $request->user_billing_account_id;

            return $this->sendResponse(api_success(113), $success_code = 113, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_billing_accounts_default()
     *
     * @uses To make account default
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_billing_accounts_default(Request $request) {

        try {

            DB::beginTransaction();

             // Validation start

            $rules = ['user_billing_account_id' => 'required|exists:user_billing_accounts,id,user_id,'.$request->id];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $old_accounts = \App\Models\UserBillingAccount::where('user_id' , $request->id)->where('is_default', YES)->update(['is_default' => NO]);

            $user_billing_account = \App\Models\UserBillingAccount::where('id' , $request->user_billing_account_id)->update(['is_default' => YES]);

            DB::commit();

            $data['user_billing_account'] = $user_billing_account;

            return $this->sendResponse(api_success(137), $success_code = 137, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method other_profile()
     *
     * @uses Content Creators Profile view
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request - User ID
     *
     * @return json response with user details
     */

    public function other_profile(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::OtherResponse()->where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $request->request->add(['user_id' => $user->id]);

            $user->updated_formatted = common_date($user->updated_at, $this->timezone, 'd M Y');

            $user->created_formatted = common_date($user->created_at, $this->timezone, 'd M Y');

            $data['user'] = $user;

            $data['payment_info'] = CommonRepo::subscriptions_user_payment_check($user, $request);

            $data['is_favuser'] = \App\Models\FavUser::where('user_id', $request->id)->where('fav_user_id', $user->id)->count() ? YES : NO;

            $data['share_link'] = Setting::get('frontend_url').$request->user_unique_id;

            $data['is_block_user'] = Helper::is_block_user($request->id, $user->user_id);

            $data['total_followers'] = \App\Models\Follower::where('user_id', $request->user_id)->where('status', YES)->count();

            $data['total_followings'] = \App\Models\Follower::where('follower_id', $request->user_id)->where('status', YES)->count();

            $data['total_posts'] = \App\Models\Post::where('user_id', $request->user_id)->count();

            $video_query = $image_query = \App\Models\PostFile::where('user_id', $request->user_id);

            $data['total_videos'] = $video_query->where('file_type', POSTS_VIDEO)->count();

            $data['total_images'] = $image_query->where('file_type', POSTS_IMAGE)->count();

            $live_video = \App\Models\LiveVideo::CurrentLive()->where('live_videos.user_id', $user->user_id)->first();

            $user->live_video_id = $live_video->id ?? '';

            $user->live_video_unique_id = $live_video->live_video_unique_id ?? '';
            
            $user->is_one_to_one_call_enabled = Setting::get('is_one_to_one_call_enabled') ?? 0;

            $user->is_one_to_many_call_enabled = Setting::get('is_one_to_many_call_enabled') ?? 0;

            $user_category = CategoryDetail::where('user_id', $user->user_id)->where('type', CATEGORY_TYPE_PROFILE)->first();

            $categories = Category::where('id', $user_category->category_id ?? 0)->first() ?? emptyObject();

            $user->categories = $categories;

            $user->selected_category = $user_category ? Category::find($user_category->category_id) : emptyObject();

            $user_live_videos = LiveVideo::where(['user_id' => $user->id, 'is_streaming' => IS_STREAMING_YES, 'status' => VIDEO_STREAMING_ONGOING])
                                        ->orderBy('created_at', 'desc');

            $user->is_user_live = $user_live_videos->count() ? IS_STREAMING_YES : IS_STREAMING_NO;

            $user->ongoing_live_video = $user_live_videos->first();

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method other_profile_posts()
     *
     * @uses Content Creators Posts
     *
     * @created Bhawya N
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function other_profile_posts(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $report_post_ids = report_posts($request->id);

            $is_only_wallet_payment = Setting::get('is_only_wallet_payment');

            $base_query = $total_query = \App\Models\Post::with('postFiles')
              ->whereNotIn('posts.id',$report_post_ids)
              ->where('user_id', $user->id)
              ->when($is_only_wallet_payment == NO, function ($q) use ($is_only_wallet_payment) {
                return $q->OriginalResponse();
              })
              ->when($is_only_wallet_payment == YES, function($q) use ($is_only_wallet_payment) {
                return $q->TokenResponse();
              });

            if($request->type != POSTS_ALL) {

                $type = $request->type;

                $base_query = $base_query->whereHas('postFiles', function($q) use($type) {
                        $q->where('post_files.file_type', $type);
                    });
            }

            $data['total'] = $total_query->count() ?? 0;

            $posts = $base_query->skip($this->skip)->take($this->take)->orderBy('posts.created_at', 'desc')->get();

            $posts = \App\Repositories\PostRepository::posts_list_response($posts, $request);

            $data['posts'] = $posts ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions()
     *
     * @uses get subscriptions list for selected user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_subscriptions(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $user_subscription = \App\Models\UserSubscription::where('user_id', $user->id)->first();

            $data['user_subscription'] = $user_subscription ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions_history()
     *
     * @uses get subscriptions list for selected user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_subscriptions_history(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $user_subscription = \App\Models\UserSubscription::where('user_id', $request->id)->first();

            $data['user_subscription'] = $user_subscription ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions_autorenewal()
     *
     * @uses get subscriptions list for selected user
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function user_subscriptions_autorenewal(Request $request) {

        try {

            // Validation start
            $rules = ['user_unique_id' => 'required|exists:users,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(1002), 1002);
            }

            $user_subscription = \App\Models\UserSubscription::where('user_id', $request->id)->first();

            $data['user_subscription'] = $user_subscription ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method user_subscriptions_payment_by_stripe()
     *
     * @uses pay for subscription using paypal
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function user_subscriptions_payment_by_stripe(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();
            

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription ?? new \App\Models\UserSubscription;
            
            if(!$user_subscription) {
                
                if($request->is_free == YES) {

                    $user_subscription->user_id = $user->id;

                    $user_subscription->save();
                    
                } else {

                    // throw new Exception(api_error(155), 155);   
 
                }

            }
           
            $check_user_payment = \App\Models\UserSubscriptionPayment::UserPaid($request->id, $user->id)->first();

            if($check_user_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $request->plan_type == PLAN_TYPE_YEAR ? $user_subscription->yearly_amount : $user_subscription->monthly_amount;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($subscription_amount,$request);

                    $subscription_amount = $subscription_amount - $promo_amount;
                }

            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $subscription_amount ?: 0.00;

            if($user_pay_amount > 0) {

                $user_card = \App\Models\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
                    'total' => $total, 
                    'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);


                $card_payment_response = PaymentRepo::user_subscriptions_payment_by_stripe($request, $user_subscription)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

           $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription, $promo_amount)->getData();

           if($payment_response->success) {

                DB::commit();

                $code = $user_pay_amount > 0 ? 140 : 235;

                return $this->sendResponse(api_success($code), $code, $payment_response->data);

            } else {
            
                throw new Exception($payment_response->error, $payment_response->error_code);
            
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method user_subscriptions_payment_by_wallet()
     * 
     * @uses send money to other user
     *
     * @created Vithya R 
     *
     * @updated Vithya R
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_subscriptions_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required|in:'.PLAN_TYPE_YEAR.','.PLAN_TYPE_MONTH,
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            // $user_subscription = $user->userSubscription;

            $is_only_wallet_payment = Setting::get('is_only_wallet_payment');

            $user_subscription = \App\Models\UserSubscription::where('user_id', $user->id)
              ->when($is_only_wallet_payment == NO, function ($q) use ($is_only_wallet_payment) {
                  return $q->OriginalResponse();
              })
              ->when($is_only_wallet_payment == YES, function($q) use ($is_only_wallet_payment) {
                  return $q->TokenResponse();
              })->first();

            if(!$user_subscription) {
                throw new Exception(api_error(155), 155);   
            }

            $check_user_payment = \App\Models\UserSubscriptionPayment::UserPaid($request->id, $user->id)->first();

            if($check_user_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $request->plan_type == PLAN_TYPE_YEAR ? $user_subscription->yearly_amount : $user_subscription->monthly_amount;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($subscription_amount,$request);

                    $subscription_amount = $subscription_amount - $promo_amount;
                }

            }

            // Check the user has enough balance 

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }            

            if($remaining < $subscription_amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $subscription_amount * Setting::get('token_amount'), 
                'user_pay_amount' => $subscription_amount,
                'paid_amount' => $subscription_amount * Setting::get('token_amount'),
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'to_user_id' => $user_subscription->user_id,
                'payment_id' => 'WPP-'.rand(),
                'tokens' => $subscription_amount,
                'usage_type' => USAGE_TYPE_SUBSCRIPTION
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription,$promo_amount)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                DB::commit();

                $code = $subscription_amount > 0 ? 140 : 235;

                return $this->sendResponse(api_success($code), $code, $payment_response->data ?? []);

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method lists_index()
     *
     * @uses To display the user details based on user  id
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function lists_index(Request $request) {

        try {

            $user = User::firstWhere('id' , $request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $report_posts = report_posts($request->id);

            $blocked_users = blocked_users($request->id);
            
            $post_ids = \App\Models\PostBookmark::where('user_id', $request->id)->Approved()->orderBy('post_bookmarks.created_at', 'desc')->pluck('post_id');

            $post_ids = $post_ids ? $post_ids->toArray() : [];

            $total_bookmarks = \App\Models\Post::with('postFiles')->Approved()->whereIn('posts.id', $post_ids)->whereNotIn('posts.user_id',$blocked_users)->whereNotIn('posts.id',$report_posts)->whereHas('user')->orderBy('posts.created_at', 'desc')->with('postBookmark')->count();

            $data = [];

            $data['username'] = $user->username;

            $data['name'] = $user->name;

            $data['user_unique_id'] = $user->unique_id;

            $data['user_id'] = $user->user_id;

            $data['total_followers'] = $user->total_followers ?? 0;

            $data['total_followings'] = $user->total_followings ?? 0;

            $data['total_posts'] = $user->total_posts ?? 0;

            $data['total_fav_users'] = $user->total_fav_users ?? 0;

            $data['total_bookmarks'] = $total_bookmarks ?? 0;

            $data['blocked_users'] = count(blocked_users($request->id)) ?? 0;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method payments_index()
     *
     * @uses To display the user details based on user  id
     *
     * @created Bhawya N 
     *
     * @updated Bhawya N
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function payments_index(Request $request) {

        try {

            $user = User::firstWhere('id' , $request->id);

            if(!$user) { 

                throw new Exception(api_error(1002) , 1002);
            }

            $data = [];

            $data['user'] = $user;

            $data['user_withdrawals_min_amount'] = Setting::get('user_withdrawals_min_amount', 10);

            $data['user_withdrawals_min_amount_formatted'] = formatted_amount(Setting::get('user_withdrawals_min_amount', 10));

            $data['user_wallet'] = \App\Models\UserWallet::where('user_id', $request->id)->first();

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }

    /** 
     * @method bell_notifications_index()
     *
     * @uses Get the user notifications
     *
     * @created Vithya R
     *
     * @updated Vithya R
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function bell_notifications_index(Request $request) {

        try {

            DB::beginTransaction();

            $bell_notification = \App\Models\BellNotification::where('to_user_id', $request->id)->where('is_read', BELL_NOTIFICATION_STATUS_UNREAD)->update(['is_read' => BELL_NOTIFICATION_STATUS_READ]);

            $base_query = $total_query = \App\Models\BellNotification::where('to_user_id', $request->id)->orderBy('created_at', 'desc')->whereHas('fromUser');

            if ($request->notification_type) {
                
                $base_query = $base_query->where('notification_type', $request->notification_type);
            }

            $notifications = $base_query->skip($this->skip)->take($this->take)->get() ?? [];

            foreach ($notifications as $key => $notification) {
                $notification->updated_formatted = common_date($notification->updated_at, $this->timezone, 'd M Y');
            }

            $data['notifications'] = $notifications;

            $data['total'] = $total_query->count() ?? 0;

            DB::commit();

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            DB::rollback();
            
            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }


    /**
     * @method chat_users_save()
     * 
     * @uses - To save the chat users.
     *
     * @created Ganesh
     *
     * @updated Ganesh
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function chat_users_save(Request $request) {

        try {

            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
            ];


            Helper::custom_validator($request->all(), $rules);

            DB::beginTransaction();

            $chat_user = \App\Models\ChatUser::where('from_user_id', $request->from_user_id)->where('to_user_id', $request->to_user_id)->first();

            if($chat_user) {

                // throw new Exception(api_error(162) , 162);
            } else {

                $chat_user = new \App\Models\ChatUser();

                $chat_user->from_user_id = $request->from_user_id;

                $chat_user->to_user_id = $request->to_user_id;
                
                $chat_user->save();
            }

            DB::commit();

            return $this->sendResponse("", "", $chat_user);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }


    /** 
     * @method block_users_save()
     *
     * @uses block the user using user_id
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param object $request - User Id
     *
     * @return json response with user details
     */

    public function block_users_save(Request $request) {

        try {

            // Validation start
            $rules = [
                'user_id' => 'required|exists:users,id',
                'reason' => 'nullable|max:255'
            ];

            Helper::custom_validator($request->all(),$rules, $custom_errors=[]);

            if($request->id == $request->user_id) {

                throw new Exception(api_error(182) , 182);
                
            }

            $check_blocked_user = \App\Models\BlockUser::where('block_by', $request->id)->where('blocked_to', $request->user_id)->first();

            // Check the user already blocked 

            if($check_blocked_user) {

                $block_user = $check_blocked_user->delete();

                $code = 156;

            } else {

                $custom_request = new Request();

                $custom_request->request->add(['block_by' => $request->id, 'blocked_to' => $request->user_id,'reason'=>$request->reason]);

                $block_user = \App\Models\BlockUser::updateOrCreate($custom_request->request->all());

                $code = 155;

                // Check the user already following the selected users

                $follower = \App\Models\Follower::where('user_id', $request->user_id)->where('follower_id', $request->id)->delete();

                $follower = \App\Models\Follower::where('user_id', $request->id)->where('follower_id', $request->user_id)->delete();

                $user_subscription_payment = \App\Models\UserSubscriptionPayment::where('to_user_id', $request->user_id)->where('from_user_id', $request->id)->where('is_current_subscription', YES)->first();

                if($user_subscription_payment) {

                    $user_subscription_payment->is_current_subscription = NO;

                    $user_subscription_payment->cancel_reason = 'unfollowed';

                    $user_subscription_payment->save();
                }

            }

            DB::commit(); 

            $data = [];

            $data['total_followers'] = \App\Models\Follower::where('user_id', $request->id)->where('status', YES)->count();

            $data['total_followings'] = \App\Models\Follower::where('follower_id', $request->id)->where('status', YES)->count();

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    
    }


    /**
     * @method block_users()
     * 
     * @uses list of blocked users
     *
     * @created Ganesh 
     *
     * @updated Ganesh
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function block_users(Request $request) {

        try {

            $base_query = $total_query = \App\Models\BlockUser::where('block_by', $request->id)->Approved()->orderBy('block_users.created_at', 'DESC')->whereHas('blockeduser');

            $block_users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['block_users'] = $block_users ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }



     /** 
     * @method user_subscriptions_payment_by_paypal()
     *
     * @uses pay for subscription using paypal
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function user_subscriptions_payment_by_paypal(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'payment_id' => 'required',
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();
            
            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                
                if($request->is_free == YES) {

                    $user_subscription = new \App\Models\UserSubscription;

                    $user_subscription->user_id = $user->id;

                    $user_subscription->save();
                    
                } else {

                    throw new Exception(api_error(155), 155);   
 
                }

            }
           
            $check_user_payment = \App\Models\UserSubscriptionPayment::UserPaid($request->id, $user->id)->first();

            if($check_user_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $request->plan_type == PLAN_TYPE_YEAR ? $user_subscription->yearly_amount : $user_subscription->monthly_amount;

            $user_pay_amount = $subscription_amount ?: 0.00;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($subscription_amount,$request);

                    $subscription_amount = $subscription_amount - $promo_amount;
                }

            }

            $request->request->add(['payment_mode'=> PAYPAL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

            $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription, $promo_amount)->getData();

            if($payment_response->success) {

                DB::commit();

                $code = $subscription_amount > 0 ? 140 : 235;

                return $this->sendResponse(api_success($code), $code, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }



    /**
     * @method verified_badge_status()
     *
     * @uses used to update verified badge status
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param card_token
     * 
     * @return JSON Response
     */
    public function verified_badge_status(Request $request) {

        try {

            if(!Setting::get('is_verified_badge_enabled')) {

                throw new Exception(api_error(166), 166);

            } 

            DB::beginTransaction();

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
                
            }

            $user->is_verified_badge  = $user->is_verified_badge == YES ? NO :YES;

            $user->save();

            DB::commit();

            $code = $user->is_verified_badge == YES ? 159 : 160;

            return $this->sendResponse(api_success($code), $code, $user);

            } catch(Exception $e) {

                DB::rollback();

                return $this->sendError($e->getMessage(), $e->getCode());
            
            }

        }


    /**
     * @method user_tips_history()
     * 
     * @uses User tips history
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_tips_history(Request $request) {

        try {

            $base_query = $total = \App\Models\UserTip::CommonResponse()->where('user_id', $request->id);

            $history = $base_query->orderBy('created_at', 'desc')->skip($this->skip)->take($this->take)->get();

            $data['history'] = $history ?? [];

            $data['total'] = $total->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

     /** 
     * @method content_creators_list()
     *
     * @uses List of content creators
     *
     * @created Jeevan
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function content_creators_list(Request $request) {

        try {

            $blocked_user_ids = blocked_users($request->id);

            $base_query = $total_query = User::DocumentVerified()->whereNotIn('users.id',$blocked_user_ids)->Approved()->where('users.is_content_creator', CONTENT_CREATOR);
            
            if ($request->category_id) {

                $user_categories = CategoryDetail::select('user_id')
                           ->where('category_id', $request->category_id)
                           ->where('type', CATEGORY_TYPE_PROFILE)
                           ->pluck('user_id')->toArray();

                $base_query = $base_query->whereIn('users.id', $user_categories);
                           
                 
            }

            $users = $base_query->skip($this->skip)->take($this->take)->get();
            
            $users = \App\Repositories\PostRepository::content_creators_list_response($users, $request);
            
            $data['content_creators'] = $users;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method content_creators_dashboard()
     *
     * @uses Show the Content creator Dashboard.
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

    public function content_creators_dashboard(Request $request) {

        try {

            $data = new \stdClass;

            $data->total_posts = Post::where('user_id',$request->id)->count();

            $post_ids = Post::where('user_id',$request->id)->pluck('id');

            $sum_value = Setting::get('is_only_wallet_payment') ? 'user_token' : 'user_amount';

            $subscription_payments = UserSubscriptionPayment::where('to_user_id',$request->id)->where('status', PAID)->sum($sum_value);

            $data->total_post_likes = PostLike::where('user_id',$request->id)->count();

            $user_tips = UserTip::where('to_user_id',$request->id)->where('status',PAID)->sum($sum_value);

            $post_payments = PostPayment::where('status',PAID)->whereIn('post_id',$post_ids)->sum($sum_value);

            $video_call_payments = VideoCallPayment::where('status',PAID)->where('model_id',$request->id)->sum($sum_value);

            $audio_call_payments = AudioCallPayment::where('status',PAID)->where('model_id',$request->id)->sum($sum_value);

            $chat_asset_payments = ChatAssetPayment::where('status',PAID)->where('from_user_id', $request->id)->sum($sum_value);

            $order_payments = UserWalletPayment::where(['user_id' => $request->id, 'usage_type' => USAGE_TYPE_ORDER, 'payment_type' => WALLET_PAYMENT_TYPE_CREDIT, 'status' => PAID])
                            ->sum($sum_value);

            $live_video_payments = LiveVideoPayment::where(['user_id' => $request->id, 'status' => PAID])->sum($sum_value);

            $data->subscription_payments = formatted_amount($subscription_payments);

            $data->user_tips = formatted_amount($user_tips);

            $data->post_payments = formatted_amount($post_payments);

            $data->video_call_payments = formatted_amount($video_call_payments);

            $data->audio_call_payments = formatted_amount($audio_call_payments);

            $data->chat_asset_payments = formatted_amount($chat_asset_payments);

            $data->order_payments = formatted_amount($order_payments);

            $data->live_video_payments = formatted_amount($live_video_payments);

            $data->analytics = last_x_months_content_creator_data(12, $request->id);

            $data->total_payments = formatted_amount(($user_tips + $post_payments + $subscription_payments + $video_call_payments + $audio_call_payments + $chat_asset_payments + $order_payments + $live_video_payments) ?? 0.00);

            $blocked_users = blocked_users($request->id);

            $base_query = $total_query = Follower::CommonResponse()->whereNotIn('follower_id',$blocked_users)->whereHas('follower')->where('followers.status',FOLLOWER_ACTIVE)->where('user_id', $request->id);

            $followers = $base_query->skip($this->skip)->take(5)->orderBy('followers.created_at', 'desc')->get();

            $data->followers = $followers;

            $data->user_id = $request->id;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
        
    
    }

    /**
     * @method login_session_delete()
     *
     * @uses To delete perticular session
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function login_session_delete(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [
                'user_login_session_id' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user_login_session = UserLoginSession::where('user_id',$request->id)->where('id',$request->user_login_session_id)->first();
            
            if(!$user_login_session) {
                throw new Exception(api_error(250), 250);
            }

            if($user_login_session->delete()) {

                DB::commit();

                return $this->sendResponse(api_success(244), $success_code = 244, []);

            } 

            throw new Exception(api_error(103), 103);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method login_session_delete_all()
     *
     * @uses To delete all the session
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function login_session_delete_all(Request $request) {

        try {

            DB::beginTransaction();

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
                
            }

            if(UserLoginSession::where('user_id',$request->id)->delete()) {

                DB::commit();

                return $this->sendResponse(api_success(244), $success_code = 244, []);

            } 

            throw new Exception(api_error(103), 103);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method push_notification_update()
     *
     * @uses To enable/disable push notifications of user
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function push_notification_update(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [ 'is_push_notification' => [ 'required', 'numeric', Rule::in([YES, NO])] ]; 

            Helper::custom_validator($request->all(), $rules);
                
            $user = User::find($request->id);

            $user->is_push_notification = $request->is_push_notification;

            $user->save();

            $data = User::firstWhere('id', $request->id);
            
            DB::commit();

            $code = $user->is_push_notification == YES ? 809 : 810;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method email_notification_update()
     *
     * @uses To enable/disable email notifications of user
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param - 
     *
     * @return JSON Response
     */
    public function email_notification_update(Request $request) {

        try {

            DB::beginTransaction();

            $rules = [ 'is_email_notification' => [ 'required', 'numeric', Rule::in([YES, NO])] ];

            Helper::custom_validator($request->all(), $rules);
                
            $user = User::find($request->id);

            $user->is_email_notification = $request->is_email_notification;

            $user->save();

            $data = User::firstWhere('id', $request->id);
            
            DB::commit();

            $code = $user->is_email_notification == YES ? 811 : 812;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch (Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method category_listing()
     *
     * @uses List of User/Post based on category
     *
     * @created Arun
     *
     * @updated Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function category_listing(Request $request) {

        try {

            $user_id = $request->id;

            $report_posts = report_posts($request->id);

            $blocked_users = array_merge([$request->id], blocked_users($request->id));

            // $follower_ids = get_follower_ids($request->id);

            $free_post = Post::where('is_paid_post', UNPAID)->pluck('id');

            $paid_post = Post::PaidApproved()
                                ->whereHas('postPayments', function($q) use ($user_id) {
                                    return $q->where('user_id', $user_id);
                                })->pluck('id');

            $post_ids = $free_post->merge($paid_post);

            $posts_base_query = Post::Approved()
                                    ->whereHas('user')
                                    ->whereHas('postFiles')
                                    ->whereNotIn('user_id', $blocked_users)
                                    ->whereNotIn('id', $report_posts)
                                    ->whereIn('id', $post_ids)
                                    ->orderByDesc('created_at');

            $users_base_query = User::whereNotIn('id', $blocked_users)->orderByDesc('created_at');

            if ($request->category_id) {

                $selected_post_category_ids = CategoryDetail::where(['category_id' => $request->category_id, 'type' => CATEGORY_TYPE_POST])->pluck('post_id');

                $user_ids = CategoryDetail::where(['category_id' => $request->category_id, 'type' => CATEGORY_TYPE_PROFILE])->pluck('user_id');
                
                $posts_base_query = $posts_base_query->whereIn('id', $selected_post_category_ids);

                $users_base_query = $users_base_query->whereIn('id', $user_ids);
            }

            $post_count = $posts_base_query->count() ?? 0;

            $posts = $posts_base_query->skip($this->skip)->take($this->take)->get();

            $posts = $posts->map(function ($post, $key) use ($request) {

                        $post->postFiles = PostFile::where('post_id', $post->post_id)
                                            ->OriginalResponse()
                                            ->first();

                        $post->is_user_liked = $post->postLikes->where('user_id', $request->id)->count() ? YES : NO;

                        $post->share_link = Setting::get('frontend_url')."post/".$post->post_unique_id;

                        $post->type = CATEGORY_TYPE_POST;

                        return $post;
                    });

            $users_count = ($users_base_query->count() ?? 0) / 2;

            $user_count = floor($users_count);

            $users = $users_base_query->skip($this->skip)->take($this->take)->get()->toArray();

            $users_arr = array();

            foreach ($users as $key => $value) {

                if ($key % 2 == 0) {
                    
                    $users_arr[$key][] = $value;
                }else {

                    $users_arr[$key-1][] = $value;
                }

            }

            $users_arr = array_map(function ($e){
                
               if( count($e) < 2 ){
                  unset($e); 
                  return; //this way i get a NULL element
               }

               return (object)$e;

            }, $users_arr);

            $users_arr = array_filter($users_arr);

            $categories = array_merge($posts->toArray(),$users_arr);

            shuffle($categories);

            $data['categories'] = $categories ?? [];

            $data['total'] = floor($users_count + $post_count) ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method content_creators_revenue_dashboard()
     *
     * @uses to get the data for content creator dashboard - Mobile 
     *
     * @created Karthick
     *
     * @updated 
     *
     * @param 
     * 
     * @return return view page
     *
     */

    public function content_creators_revenue_dashboard(Request $request) {

        try {

            $year = $request->year ? : date('Y');

            $data = new \stdClass;

            $last_week_payments = $current_week_payments = 0;

            $data->total_posts = Post::where('user_id',$request->id)->count();

            $data->currency = $currency = Setting::get('currency', '$');

            $post_ids = Post::where('user_id', $request->id)->pluck('id');

            $data->total_post_likes = PostLike::where('user_id',$request->id)->count();

            $sum_value = Setting::get('is_only_wallet_payment') ? 'user_token' : 'user_amount';

            $subscription_payments = UserSubscriptionPayment::where(['to_user_id' => $request->id, 'status' => PAID])->sum($sum_value);

            $user_tips = UserTip::where(['to_user_id' => $request->id, 'status' => PAID])->sum($sum_value);

            $post_payments = PostPayment::where('status',PAID)->whereIn('post_id',$post_ids)->sum($sum_value);

            $video_call_payments = VideoCallPayment::where(['model_id' => $request->id, 'status' => PAID])->sum($sum_value);

            $audio_call_payments = AudioCallPayment::where(['model_id' => $request->id, 'status' => PAID])->sum($sum_value);

            $chat_asset_payments = ChatAssetPayment::where(['from_user_id' => $request->id, 'status' => PAID])->sum($sum_value);

            $order_payments =  UserWalletPayment::where(['user_id' => $request->id, 'usage_type' => USAGE_TYPE_ORDER, 'payment_type' => WALLET_PAYMENT_TYPE_CREDIT, 'status' => PAID])
                                ->sum($sum_value);

            $live_video_payments = LiveVideoPayment::where(['user_id' => $request->id, 'status' => PAID])->sum($sum_value);

            $total_payments = $subscription_payments + $user_tips + $post_payments + $video_call_payments + $audio_call_payments + $chat_asset_payments + $order_payments + $live_video_payments;

            $payments = ['subscription_payments', 'user_tips', 'post_payments', 'video_call_payments', 'audio_call_payments', 'chat_asset_payments', 'order_payments', 'live_video_payments'];

            $payment_types = [SUBSCRIPTION_PAYMENTS, USER_TIPS, POST_PAYMENTS, VIDEO_CALL_PAYMENTS, AUDIO_CALL_PAYMENTS, CHAT_ASSET_PAYMENTS, ORDER_PAYMENTS, LIVE_VIDEO_PAYMENTS];

            foreach($payments as $key => $payment) {

                $data->$payment = Helper::dashboard_data_formatted($$payment, $payment_types[$key], $request->id);

                $last_week_payments += $data->$payment->last_week_payments ?? 0;

                $current_week_payments += $data->$payment->current_week_payments ?? 0;

                unset($data->$payment->last_week_payments, $data->$payment->current_week_payments);
            }

            $data->total_payments = Helper::dashboard_data_formatted($total_payments, TOTAL_PAYMENTS, $request->id, $last_week_payments, $current_week_payments);

            unset($data->total_payments->last_week_payments, $data->total_payments->current_week_payments);

            $data->analytics = Helper::content_creator_analytics_data($request->id, $year);

            return $this->sendResponse("", "", $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    }

    /** 
     * @method user_category_listing()
     *
     * @uses List of User based on category
     *
     * @created Arun
     *
     * @updated Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function user_category_listing(Request $request) {

        try {

            $user_id = $request->id;

            $report_posts = report_posts($request->id);

            $blocked_users = array_merge([$request->id], blocked_users($request->id));

            // $follower_ids = get_follower_ids($request->id);

            $users_base_query = User::whereNotIn('users.id',$blocked_users)->orderBy('created_at', 'desc');

            if ($request->category_id) {

                $user_ids = CategoryDetail::where(['category_id' => $request->category_id, 'type' => CATEGORY_TYPE_PROFILE])->pluck('user_id');
        
                $users_base_query = $users_base_query->whereIn('id', $user_ids);
            }

            $data['total'] = $users_base_query->count() ?? 0;

            $users = $users_base_query->skip($this->skip)->take($this->take)->get();

            $users = $users->map(function($user, $key) use($request) {

                $user->total_videos = PostFile::where('user_id', $user->id)->where('file_type', FILE_TYPE_VIDEO)->count() ?? 0;

                $user->total_images = PostFile::where('user_id', $user->id)->where('file_type', FILE_TYPE_IMAGE)->count() ?? 0;

                $user->total_audios = PostFile::where('user_id', $user->id)->where('file_type', FILE_TYPE_AUDIO)->count() ?? 0;

                return $user;

            });

            $data['users'] = $users ?? [];

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

}
