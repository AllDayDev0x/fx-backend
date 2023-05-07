<?php 

namespace App\Helpers;

use Mailgun\Mailgun;

use Hash, Exception, Auth, Mail, File, Log, Storage, Setting, DB, Validator, Image;

use App\Models\Admin, App\Models\User, App\ContentCreator, App\Models\StaticPage;

use App\Models\ReferralCode, App\Models\UserReferral;

use App\Models\{ OrderPayment, Order, Post, UserSubscriptionPayment, PostPayment, UserTip, VideoCallPayment, AudioCallPayment, ChatAssetPayment, LiveVideoPayment, UserWalletPayment };

use App\Models\{ LiveVideo, Follower };

use Hexters\CoinPayment\CoinPayment;

use Carbon\Carbon;

class Helper {

    public static function clean($string) {

        $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.

        return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
    }

    public static function generate_token() {
        
        return Helper::clean(Hash::make(rand() . time() . rand()));
    }

    public static function generate_token_expiry() {

        $token_expiry_hour = Setting::get('token_expiry_hour') ? Setting::get('token_expiry_hour') : 1;
        
        return time() + $token_expiry_hour*3600;  // 1 Hour
    }

    // Note: $error is passed by reference
    
    public static function is_token_valid($entity, $id, $token, $device_unique_id, &$error) {
        // Temp return

        return TRUE;

        $user_session = \App\Models\UserLoginSession::where('user_id', '=', $id)
            ->where('token', $token)
            ->where('device_unique_id', $device_unique_id)
            ->first();

        if (($entity == USER) && $user_session){

            if ($user_session->token_expiry > time()) {
                // Token is valid
                $error = NULL;
                return true;
            } else {
                $error = api_error(1003);
                return FALSE;
            }
        }

        $error = api_error(1004);
        
        return FALSE;
   
    }

    public static function generate_email_code($value = "") {

        return mt_rand(100000, 999999);
    }

    public static function generate_email_expiry() {

        $token_expiry = Setting::get('token_expiry_hour') ?: 1;
            
        return time() + $token_expiry*3600;  // 1 Hour

    }

    // Check whether email verification code and expiry

    public static function check_email_verification($verification_code , $user_id , &$error) {

        if(!$user_id) {

            $error = tr('user_id_empty');

            return FALSE;

        } else {

            $user = User::find($user_id);
        }

        // Check the data exists

        if($user) {

            // Check whether verification code is empty or not

            if($verification_code) {

                // Log::info("Verification Code".$verification_code);

                // Log::info("Verification Code".$user->verification_code);

                if ($verification_code ===  $user->verification_code ) {

                    // Token is valid

                    $error = NULL;

                    // Log::info("Verification CODE MATCHED");

                    return true;

                } else {

                    $error = tr('verification_code_mismatched');

                    // Log::info(print_r($error,true));

                    return FALSE;
                }

            }
                
            // Check whether verification code expiry 

            if ($user->verification_code_expiry > time()) {

                // Token is valid

                $error = NULL;

                Log::info(tr('token_expiry'));

                return true;

            } else if($user->verification_code_expiry < time() || (!$user->verification_code || !$user->verification_code_expiry) ) {

                $user->verification_code = Helper::generate_email_code();
                
                $user->verification_code_expiry = Helper::generate_email_expiry();
                
                $user->save();

                // If code expired means send mail to that user

                $subject = tr('verification_code_title');
                $email_data = $user;
                $page = "emails.welcome";
                $email = $user->email;
                $result = Helper::send_email($page,$subject,$email,$email_data);

                $error = tr('verification_code_expired');

                Log::info(print_r($error,true));

                return FALSE;
            }
       
        }

    }
    
    public static function generate_password() {

        $new_password = time();
        $new_password .= rand();
        $new_password = sha1($new_password);
        $new_password = substr($new_password,0,8);
        return $new_password;
    }

    public static function file_name() {

        $file_name = time();
        $file_name .= rand();
        $file_name = sha1($file_name);

        return $file_name;    
    }

    public static function upload_file($picture , $folder_path = COMMON_FILE_PATH) {

        $file_path_url = "";

        $file_name = Helper::file_name();

        $ext = $picture->getClientOriginalExtension();

        $local_url = $file_name . "." . $ext;

        $inputFile = base_path('public'.$folder_path.$local_url);

        $picture->move(public_path().$folder_path, $local_url);

        $file_path_url = Helper::web_url().$folder_path.$local_url;

        return $file_path_url;
    
    }

    public static function web_url() 
    {
        return url('/');
    }

    public static function delete_file($picture, $path = COMMON_FILE_PATH) {

        if ( file_exists( public_path() . $path . basename($picture))) {

            File::delete( public_path() . $path . basename($picture));
      
        } else {

            return false;
        }  

        return true;    
    }
 
    public static function send_email($page,$subject,$email,$email_data) {

        // check the email notification

        if(Setting::get('is_email_notification') == YES) {

            // Don't check with envfile function. Because without configuration cache the email will not send

            if( config('mail.username') &&  config('mail.password')) {

                try {

                    $site_url=url('/');

                    $isValid = 1;

                    if(envfile('MAIL_MAILER') == 'mailgun' && Setting::get('MAILGUN_PUBLIC_KEY')) {

                        Log::info("isValid - STRAT");

                        # Instantiate the client.

                        $email_address = new Mailgun(Setting::get('MAILGUN_PUBLIC_KEY'));

                        $validateAddress = $email;

                        # Issue the call to the client.
                        $result = $email_address->get("address/validate", ['address' => $validateAddress]);

                        # is_valid is 0 or 1

                        $isValid = $result->http_response_body->is_valid;

                        Log::info("isValid FINAL STATUS - ".$isValid);

                    }

                    if($isValid) {

                        if (Mail::queue($page, ['email_data' => $email_data,'site_url' => $site_url], 
                                function ($message) use ($email, $subject) {

                                    $message->to($email)->subject($subject);
                                }
                        )) {

                            $message = api_success(102);

                            $response_array = ['success' => true , 'message' => $message];

                            return json_decode(json_encode($response_array));

                        } else {

                            throw new Exception(api_error(116) , 116);
                            
                        }

                    } else {

                        $error = api_error();

                        throw new Exception($error, 115);                  

                    }

                } catch(\Exception $e) {

                    $error = $e->getMessage();

                    $error_code = $e->getCode();

                    $response_array = ['success' => false , 'error' => $error , 'error_code' => $error_code];
                    
                    return json_decode(json_encode($response_array));

                }
            
            } else {

                $error = api_error(106);

                $response_array = ['success' => false , 'error' => $error , 'error_code' => 106];
                    
                return json_decode(json_encode($response_array));

            }
        
        } else {
            Log::info("email notification disabled by admin");
        }
    
    }

    public static function push_message($code) {

        switch ($code) {
            case 601:
                $string = tr('push_no_provider_available');
                break;
           
            default:
                $string = "";
        }

        return $string;

    }  

    // Convert all NULL values to empty strings
    public static function null_safe($input_array) {
 
        $new_array = [];

        foreach ($input_array as $key => $value) {

            $new_array[$key] = ($value == NULL) ? "" : $value;
        }

        return $new_array;
    }

    /**
     * Creating date collection between two dates
     *
     * <code>
     * <?php
     * # Example 1
     * generate_date_range("2014-01-01", "2014-01-20", "+1 day", "m/d/Y");
     *
     * # Example 2. you can use even time
     * generate_date_range("01:00:00", "23:00:00", "+1 hour", "H:i:s");
     * </code>
     *
     * @param string since any date, time or datetime format
     * @param string until any date, time or datetime format
     * @param string step
     * @param string date of output format
     * @return array
     */
    public static function generate_date_range($month = "", $year = "", $step = '+1 day', $output_format = 'd/m/Y', $loops = 2) {

        $month = $set_current_month = $month ?: date('F');

        $year = $set_current_year = $year ?: date('Y');

        $last_month = date('F', strtotime('+'.$loops.' months'));

        $dates = $response = [];

        // $response = new \stdClass;

        $response = [];

        $current_loop = 1;

        while ($current_loop <= $loops) {
        
            $month_response = new \stdClass;

            $timestamp = strtotime($set_current_month.' '.$set_current_year); // Get te timestamp from the given 

            $first_date_of_the_month = date('Y-m-01', $timestamp);

            $last_date_of_month  = date('Y-m-t', $timestamp); 

            $dates = [];

            $set_current_date = strtotime($first_date_of_the_month); // time convertions and set dates

            $last_date_of_month = strtotime($last_date_of_month);  // time convertions and set dates

            // Generate dates based first and last dates

            while( $set_current_date <= $last_date_of_month ) {

                $dates[] = date($output_format, $set_current_date);

                $set_current_date = strtotime($step, $set_current_date);
            }

            $month_response->month = $set_current_month;

            $month_response->total_days = count($dates);

            $month_response->dates = $dates;


            $set_current_month = date('F', strtotime("+".$current_loop." months", $last_date_of_month));

            $set_current_year = date('Y', strtotime("+".$current_loop." months", $last_date_of_month));


            $current_loop++;

            array_push($response, $month_response);

        }

        return $response;
    }

    /**
     *
     * @method get_months()
     *
     * @uses get months list or get month number
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return 
     */

    public static function get_months($get_month_name = "") {

        $months = ['01' => 'January', '02' => 'February','03' => 'March','04' => 'April','05' => 'May','06' => 'June','07' => 'July ','08' => 'August','09' => 'September','10' => 'October','11' => 'November','12' => 'December'];

        if($get_month_name) {

            return $months[$get_month_name];

        }

        return $months;
    }

    /**
    * @method generate_referral_code()
    *
    * @uses used to genarate referral code to the owner
    *
    * @created Akshata
    * 
    * @updated 
    *
    * @param $value
    *
    * @return boolean
    */
    public static function generate_referral_code($value = "") {

        $referral_name = strtolower(substr(str_replace(' ','',$value),0,3));
        
        $referral_random_number = rand(100,999);

        $referral_code = $referral_name.$referral_random_number;

        return $referral_code;
    }

    /**
    * @method referral_code_earnings_update()
    *
    * @uses used to update referral bonus to the owner
    *
    * @created vithya R
    * 
    * @updated vithya R
    *
    * @param string $referral_code
    *
    * @return boolean
    */

    public static function referral_code_earnings_update($referral_code) {

        $referrer_user = User::where('referral_code', $referral_code)->first();

        if(!$referrer_user) {

            throw new Exception(api_error(132), 132);
            
        }

        $referrer_bonus = Setting::get('referrer_bonus', 1) ?: 0;

        $referrer_user->referrer_bonus += $referrer_bonus;
        
        $referrer_user->save();

        Log::info("referral_code_earnings_update - ".$referrer_bonus);

        return true;

    }

    public static function get_times() {

        $times = ['flexible' => 'Flexible', '12 AM' => '12 AM(midnight)', '1 AM' => '1 AM', '2 AM' => '2 AM', '3 AM' => '3 AM', '4 AM' => '4 AM', '5 AM' => '5 AM', '6 AM' => '6 AM', '7 AM' => '7 AM', '8 AM' => '8 AM', '9 AM' => '9 AM', '10 AM' => '10 AM', '11 AM' => '11 AM', '12 PM' => '12 PM(Afternoon)', '1 PM' => '1 PM', '2 PM' => '2 PM', '3 PM' => '3 PM', '4 PM' => '4 PM', '5 PM' => '5 PM', '6 PM' => '6 PM', '7 PM' => '7 PM', '8 PM' => '8 PM', '9 PM' => '9 PM', '10 PM' => '10 PM', '11 PM' => '11 PM'];

        return $times;
    }

    public static function custom_validator($request, $request_inputs, $custom_errors = []) {

        $validator = Validator::make($request, $request_inputs, $custom_errors);

        if($validator->fails()) {

            $error = implode(',', $validator->messages()->all());

            throw new Exception($error, 101);
               
        }

        return $validator->validated();
    }

    /**
     * @method settings_generate_json()
     *
     * @uses used to update settings.json file with updated details.
     *
     * @created vidhya
     * 
     * @updated vidhya
     *
     * @param -
     *
     * @return boolean
     */
    
    public static function settings_generate_json() {

        $settings = \App\Models\Settings::get();

        $sample_data = [];

        foreach ($settings as $key => $setting) {

            $sample_data[$setting->key] = $setting->value;
        }

        $static_page_ids1 = ['about', 'terms', 'privacy', 'contact'];

        $footer_pages1 = \App\Models\StaticPage::whereIn('type', $static_page_ids1)->where('status', APPROVED)->get();

        $static_page_ids2 = ['help', 'faq', 'others'];

        $footer_pages2 = \App\Models\StaticPage::whereIn('type', $static_page_ids2)->where('status', APPROVED)->skip(0)->take(4)->get();

        $sample_data['footer_pages1'] = $footer_pages1;

        $sample_data['footer_pages2'] = $footer_pages2;

        // Social logins

        $social_login_keys = ['FB_CLIENT_ID', 'FB_CLIENT_SECRET', 'FB_CALL_BACK' , 'TWITTER_CLIENT_ID', 'TWITTER_CLIENT_SECRET', 'TWITTER_CALL_BACK', 'GOOGLE_CLIENT_ID', 'GOOGLE_CLIENT_SECRET', 'GOOGLE_CALL_BACK'];

        $social_logins = \App\Models\Settings::whereIn('key', $social_login_keys)->get();

        $social_login_data = [];

        foreach ($social_logins as $key => $social_login) {

            $social_login_data[$social_login->key] = $social_login->value;
        }

        $sample_data['social_logins'] = $social_login_data;

        $data['data'] = $sample_data;

        $data = json_encode($data);

        $folder_path_name = 'default-json/settings.json';

        Storage::disk('public')->put($folder_path_name, $data);
    
    }

    /**
     * @method upload_file
     */
    
    public static function storage_upload_file($input_file, $folder_path = COMMON_FILE_PATH, $name = "") {

        if(!$input_file) {

            return "";

        }

        if(Setting::get('s3_bucket') == STORAGE_TYPE_S3 ) {

            $path = $input_file->store($folder_path, 's3');

            $file_path = str_replace("//","/",$path);

            $url = Storage::disk('s3')->url($file_path);

            return $url;
        }
       
        $name = $name ?: Helper::file_name();

        $ext = $input_file->getClientOriginalExtension();

        $file_name = $name.".".$ext;

        $public_folder_path = "public/".$folder_path;

        Storage::putFileAs($public_folder_path, $input_file, $file_name);

        $storage_file_path = $folder_path.$file_name;

        $url = asset(Storage::url($storage_file_path));
    
        return $url;

    }

    /**
     * @method
     * 
     */
    public static function storage_delete_file($url, $folder_path = COMMON_FILE_PATH) {

        $file_name = basename($url);

        $storage_file_path = $folder_path.$file_name;

        if (Setting::get('s3_bucket') == STORAGE_TYPE_S3 ) {

            $s3 = Storage::disk('s3');

            $s3->delete($storage_file_path);

            return true;
        }

        if($url != '' && Storage::disk('public')->exists($storage_file_path)) {

           return Storage::disk('public')->delete($storage_file_path);

        }
    }

    /**
     * @method upload_file
     */
    
    public static function post_upload_file($input_file, $folder_path = COMMON_FILE_PATH, $name = "") {

        if(!$input_file) {

            return "";

        }

        if(Setting::get('s3_bucket') == STORAGE_TYPE_S3 ) {

            $url = self::upload_file_to_s3($input_file, $folder_path);

            return $url;

            $path = $input_file->store($folder_path, 's3');

            $file_path = str_replace("//","/",$path);

            $url = Storage::disk('s3')->url($file_path);

            return $url;
        }
       
        $name = $name ?: Helper::file_name();

        $ext = $input_file->getClientOriginalExtension();

        $file_name = $name.".".$ext;

        $public_folder_path = "public/".$folder_path;

        Storage::putFileAs($public_folder_path, $input_file, $file_name);

        $storage_file_path = $folder_path.$file_name;

        $url = asset(Storage::url($storage_file_path));
    
        return $url;

    }

    /**
     * @method upload_file
     */
    
    public static function generate_post_blur_file($url, $input_file,$user_id) {

        if(!$url) {

            return "";

        }

        if(Setting::get('s3_bucket') != STORAGE_TYPE_S3 ) {

            \File::makeDirectory(Storage::path('public/'.POST_BLUR_PATH.$user_id), 0777, true, true);

            $storage_file_path = 'public/'.POST_PATH.$user_id.'/'.basename($url);

            $output_file_path = 'public/'.POST_BLUR_PATH.$user_id.'/'.basename($url);

            \Storage::copy($storage_file_path, $output_file_path);

            // create new Intervention Image
            // $img = \Image::make(Storage::path($storage_file_path));

            // apply stronger blur
            // $img->blur(100)->save(Storage::path($output_file_path));

            // if(generate_blur_file(Storage::path($output_file_path))) {
           
                // $url = asset(Storage::url($output_file_path));
            // }

            $url = Setting::get('post_image_placeholder');

        } else {

            $extension = $input_file->getClientOriginalExtension();

            $filename = md5(time()).'_'.$input_file->getClientOriginalName();

            $blured_file = Image::make($input_file)->blur(100)->encode($extension);

            // generate_blur_file($output_file_path)

            $url = Storage::disk('s3')->put(POST_BLUR_PATH.$filename, (string)$blured_file);

            $url = Storage::disk('s3')->url(POST_BLUR_PATH.$filename);

            return $url;
        }
        
        return $url;

    }

     /**
     * @method upload_file
     */
    
    public static function generate_chat_blur_file($url, $input_file) {

        if(!$url) {

            return "";

        }

        if(Setting::get('s3_bucket') != STORAGE_TYPE_S3 ) {

            \File::makeDirectory(Storage::path('public/'.CHAT_ASSETS_PATH), 0777, true, true);

            $name = Helper::file_name();

            $ext = $input_file->getClientOriginalExtension();

            $file_name = $name.".".$ext;

            $storage_file_path = 'public/'.CHAT_ASSETS_PATH.basename($url);

            $output_file_path = 'public/'.CHAT_ASSETS_PATH.basename($file_name);

            // create new Intervention Image
            $img = \Image::make(Storage::path($storage_file_path));

            // apply stronger blur
            $img->blur(100)->save(Storage::path($output_file_path));
           
            $url = asset(Storage::url($output_file_path));

        }
        else{

            $extension = $input_file->getClientOriginalExtension();

            $filename = md5(time()).'_'.$input_file->getClientOriginalName();

            $blured_file = Image::make($input_file)->blur(100)->encode($extension);

            Storage::disk('s3')->put(CHAT_ASSETS_PATH.$filename, (string)$blured_file, 'public');

            $url = Storage::disk('s3')->url(CHAT_ASSETS_PATH.$filename);

            return $url;
        }
        
        return $url;

    }

    public static function is_you_following($logged_in_user_id, $other_user_id) {

        $check = \App\Models\Follower::where('user_id', $other_user_id)->where('follower_id', $logged_in_user_id)->where('status', YES)->count();

        return $check ? YES : NO;
    }

    public static function is_fav_user($logged_in_user_id, $other_user_id) {

        $check = \App\Models\FavUser::where('user_id', $logged_in_user_id)->where('fav_user_id', $other_user_id)->count();

        return $check ? YES : NO;
    }

    public static function is_block_user($logged_in_user_id, $other_user_id) {

        $check = \App\Models\BlockUser::where('block_by', $logged_in_user_id)->where('blocked_to', $other_user_id)->count();

        return $check ? YES : NO;
    }

    public static function ccbill_details($data) {

        $initialPrice = number_format($data->amount, 2);

        $initialPeriod = 2;

        $sub_account = Setting::get('ccbill_sub_account_number');
        
        $currencyCode = 840;

        $salt_key = Setting::get('salt_key');

        $flex_form_id = Setting::get('flex_form_id');

        $formDigest = md5($initialPrice.$initialPeriod.$currencyCode.$salt_key);
        
        $redirect_web_url = Setting::get('ccbill_url').$flex_form_id."?clientSubacc=".$sub_account."&initialPrice=".$initialPrice."&initialPeriod=".$initialPeriod."&currencyCode=".$currencyCode."&formDigest=".$formDigest."&from_user_id=".$data->from_user_id."&to_user_id=".$data->to_user_id."&status=".$data->status."&unique_id=".$data->unique_id;

        return $redirect_web_url;
    }

    public static function post_ccbill_details($data) {

        $initialPrice = number_format($data->amount, 2);

        $initialPeriod = 2;

        $sub_account = Setting::get('ccbill_sub_account_number');
        
        $currencyCode = 840;

        $salt_key = Setting::get('salt_key');

        $flex_form_id = Setting::get('flex_form_id');

        $formDigest = md5($initialPrice.$initialPeriod.$currencyCode.$salt_key);
        
        $redirect_web_url = Setting::get('ccbill_url').$flex_form_id."?clientSubacc=".$sub_account."&initialPrice=".$initialPrice."&initialPeriod=".$initialPeriod."&currencyCode=".$currencyCode."&formDigest=".$formDigest."&post_id=".$data->post_id."&user_id=".$data->user_id."&status=".$data->status."&post_unique_id=".$data->post_unique_id;

        return $redirect_web_url;
    }

    public static function subscription_ccbill_details($data) {

        $initialPrice = number_format($data->amount, 2);

        $initialPeriod = 2;

        $sub_account = Setting::get('ccbill_sub_account_number');
        
        $currencyCode = 840;

        $salt_key = Setting::get('salt_key');

        $flex_form_id = Setting::get('flex_form_id');

        $formDigest = md5($initialPrice.$initialPeriod.$currencyCode.$salt_key);
        
        $redirect_web_url = Setting::get('ccbill_url').$flex_form_id."?clientSubacc=".$sub_account."&initialPrice=".$initialPrice."&initialPeriod=".$initialPeriod."&currencyCode=".$currencyCode."&formDigest=".$formDigest."&user_unique_id=".$data->user_unique_id."&plan_type=".$data->plan_type."&user_id=".$data->user_id."&status=".$data->status;

        return $redirect_web_url;
    }

    public static function generate_story_blur_file($url, $input_file,$user_id) {

        if(!$url) {

            return "";

        }

        if(Setting::get('s3_bucket') != STORAGE_TYPE_S3 ) {

            \File::makeDirectory(Storage::path('public/'.STORY_BLUR_PATH.$user_id), 0777, true, true);

            $storage_file_path = 'public/'.STORY_PATH.$user_id.'/'.basename($url);

            $output_file_path = 'public/'.STORY_BLUR_PATH.$user_id.'/'.basename($url);

            // create new Intervention Image
            $img = \Image::make(Storage::path($storage_file_path));

            // apply stronger blur
            $img->blur(100)->save(Storage::path($output_file_path));

            if(generate_blur_file(Storage::path($output_file_path))) {
           
                $url = asset(Storage::url($output_file_path));
            }

        } else {

            $extension = $input_file->getClientOriginalExtension();

            $filename = md5(time()).'_'.$input_file->getClientOriginalName();

            $blured_file = Image::make($input_file)->blur(100)->encode($extension);

            // generate_blur_file($output_file_path)

            Storage::disk('s3')->put(STORY_BLUR_PATH.$filename, (string)$blured_file, 'public');

            $url = Storage::disk('s3')->url(STORY_BLUR_PATH.$filename);

            return $url;
        }
        
        return $url;

    }

    public static function public_upload_file($picture , $key, $folder_path = COMMON_FILE_PATH) {

        $file_path_url = "";

        $placeholder = placeholder_path_formate($key);

        $ext = $placeholder['formate'];

        $local_url = $placeholder['file_name'] . "." . $ext;

        Image::make($picture)->encode($ext, 65)->save(public_path($folder_path.$local_url));

        $file_path_url = Helper::web_url().$folder_path.$local_url;

        return $file_path_url;
    
    }

    public static function public_delete_file($picture, $path = COMMON_FILE_PATH) {

        if ( file_exists( public_path() . $path . basename($picture))) {

            File::delete( public_path() . $path . basename($picture));
      
        } else {

            return false;
        }  

        return true;    
    }

    /**
     * @method coinpayment_subscription_transaction_details()
     *
     * @uses To get all the subscription plans transaction details
     *
     * @created Arun
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public static function coinpayment_subscription_transaction_details($data, $user) {

        try {

            $transaction['order_id'] = uniqid(); // invoice number

            $transaction['amountTotal'] = (FLOAT) $data->amount;

            $transaction['note'] = $data->note;

            $transaction['buyer_name'] = $user->name;

            $transaction['buyer_email'] = $user->email;

            $transaction['redirect_url'] = route('coinpayment-success',['user_id'=>$data->user_id,'user_unique_id'=>$data->user_unique_id,'plan_type'=>$data->plan_type,'paid_amount'=>$data->amount,'status'=>$data->status]);

            $transaction['cancel_url'] = route('coinpayment-failure');

            $transaction['items'][] = [
                'itemDescription' => $data->note,
                'itemPrice' => (FLOAT) $data->amount, // USD
                'itemQty' => (INT) 1,
                'itemSubtotalAmount' => (FLOAT)$data->amount // USD
            ];

            $redirect_web_url = CoinPayment::generatelink($transaction);

            return $redirect_web_url;

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

    /**
     * @method coinpayment_tips_transaction_details()
     *
     * @uses To get all the tips transaction details
     *
     * @created Arun
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public static function coinpayment_tips_transaction_details($data,$user) {

        try {

            $transaction['order_id'] = uniqid(); // invoice number

            $transaction['amountTotal'] = (FLOAT) $data->amount;

            $transaction['note'] = $data->note;

            $transaction['buyer_name'] = $user->name;

            $transaction['buyer_email'] = $user->email;

            $transaction['redirect_url'] = route('coinpayment-success', ['from_user_id'=>$data->from_user_id, 'to_user_id'=>$data->to_user_id, 'paid_amount'=>$data->amount,'unique_id'=>$data->unique_id,'status'=>$data->status]);

            $transaction['cancel_url'] = route('coinpayment-failure');

            $transaction['items'][] = [
                'itemDescription' => $data->note,
                'itemPrice' => (FLOAT) $data->amount, // USD
                'itemQty' => (INT) 1,
                'itemSubtotalAmount' => (FLOAT)$data->amount // USD
            ];

            $redirect_web_url = CoinPayment::generatelink($transaction);

            return $redirect_web_url;

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

    /**
     * @method coinpayment_post_transaction_details()
     *
     * @uses To get all the post transaction details
     *
     * @created Arun
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public static function coinpayment_post_transaction_details($data,$user) {

        try {

            $transaction['order_id'] = uniqid(); // invoice number

            $transaction['amountTotal'] = (FLOAT) $data->amount;

            $transaction['note'] = $data->note;

            $transaction['buyer_name'] = $user->name;

            $transaction['buyer_email'] = $user->email;

            $transaction['redirect_url'] = route('coinpayment-success',['user_id'=>$data->user_id, 'post_id'=>$data->post_id, 'paid_amount'=>$data->amount,'post_unique_id'=>$data->post_unique_id,'status'=>$data->status]);

            $transaction['cancel_url'] = route('coinpayment-failure');

            $transaction['items'][] = [
                'itemDescription' => $data->note,
                'itemPrice' => (FLOAT) $data->amount, // USD
                'itemQty' => (INT) 1,
                'itemSubtotalAmount' => (FLOAT)$data->amount // USD
            ];

            $redirect_web_url = CoinPayment::generatelink($transaction);

            return $redirect_web_url;

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    }

    /**
     * @method coinpayment_live_video_transaction_details()
     *
     * @uses To get all the live video transaction details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public static function coinpayment_live_video_transaction_details($data,$user) {

        try {

            $transaction['order_id'] = uniqid(); // invoice number

            $transaction['amountTotal'] = (FLOAT) $data->amount;

            $transaction['note'] = $data->note;

            $transaction['buyer_name'] = $user->name;

            $transaction['buyer_email'] = $user->email;

            $transaction['redirect_url'] = route('coinpayment-success',['user_id'=>$data->user_id, 'live_video_id'=>$data->live_video_id, 'paid_amount'=>$data->amount,'status'=>$data->status]);

            $transaction['cancel_url'] = route('coinpayment-failure');

            $transaction['items'][] = [
                'itemDescription' => $data->note,
                'itemPrice' => (FLOAT) $data->amount, // USD
                'itemQty' => (INT) 1,
                'itemSubtotalAmount' => (FLOAT)$data->amount // USD
            ];

            $redirect_web_url = CoinPayment::generatelink($transaction);

            return $redirect_web_url;

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method get_login_session_image()
     *
     * @uses To get session_image details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request device type, browser type
     *
     * @return JSON Response
     */
    public static function get_login_session_image($device_type,$browser_type) {

        try {

            if($device_type == 'android'){

                $session_image = file_exists(public_path('images/android.png')) ? asset('images/android.png') : public_path('images/default.png');

            } else if($device_type == 'ios') {

                $session_image = file_exists(public_path('images/ios.png')) ? asset('images/ios.png') : asset('images/default.png');

            } else {

                $session_image = file_exists(public_path('images/'.$browser_type.'.png')) ? asset('images/'.$browser_type.'.png') : asset('image/default.png');

            }

            return $session_image;

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    public static function get_social_medias() {

        $links = ['website','amazon_wishlist','instagram_link','facebook_link','twitter_link','linkedin_link','pinterest_link','youtube_link','twitch_link','snapchat_link'];

        return (object) $links;
    }

    public static function dashboard_data_formatted($amount, $type, $user_id, $last_week_payments = 0, $current_week_payments = 0) {

        $data = new \stdClass;

        $amount_length = strlen(round($amount));

        $data->amount = $amount;

        $data->format = "";

        if(in_array($amount_length, [3, 4, 5, 6])) {

            $data->amount = round($amount / 1000, 1);

            $data->format = "K";

        } elseif(in_array($amount_length, [7, 8, 9])) {

            $data->amount = round($amount / 1000000, 1);

            $data->format = "M";

        } elseif($amount_length >= 10) {

            $data->amount = round($amount / 1000000000, 1);

            $data->format = "B";
        }

        $result = self::last_7_days_status($type, $user_id, $last_week_payments, $current_week_payments);

        $data->amount_type = $result->amount_type;

        $data->last_7_days_status = $result->last_7_days_status;

        $data->last_week_payments = $result->last_week_payments;

        $data->current_week_payments = $result->current_week_payments;

        return $data;
    }

    public static function content_creator_analytics_data($user_id, $year) {

        $data = new \stdClass;

        $data->year = $year;

        $yearly_revenues = [];
       
        $dates = $yearly_revenues = $yearly_month = $yearly_earning = [];

        $post_ids = Post::where('user_id', $user_id)->pluck('id');

        $order_ids = Order::where('user_id', $user_id)->pluck('id');

        $sum_value = Setting::get('is_only_wallet_payment') ? 'token' : 'user_amount';

        for($month = 1; $month < 13 ; $month++) {

            $yearly_data =  new \stdClass;

            $yearly_data->month = Carbon::createFromFormat('m Y', "$month $year")->format('M');

            $yearly_data->formatted_month = Carbon::createFromFormat('m Y', "$month $year")->format('M Y');
          
            $yearly_subscription_earnings = UserSubscriptionPayment::where('to_user_id',$user_id)->whereMonth('paid_date', $month)->whereYear('paid_date', $year)->where('status' , PAID)->sum($sum_value);

            $yearly_order_earnings = UserWalletPayment::where(['user_id' => $user_id, 'usage_type' => USAGE_TYPE_ORDER, 'payment_type' => WALLET_PAYMENT_TYPE_CREDIT, 'status' => PAID])
                                        ->whereMonth('paid_date', $month)->whereYear('paid_date', $year)->where('status' , PAID)
                                        ->sum($sum_value);

            $yearly_post_earnings = PostPayment::whereIn('post_id', $post_ids)->whereMonth('paid_date', $month)->whereYear('paid_date', $year)->where('status' , PAID)->sum($sum_value);

            $yearly_user_tips = UserTip::where('to_user_id', $user_id)->whereMonth('paid_date', $month)->whereYear('paid_date', $year)->where('status',PAID)->sum($sum_value);

            $yearly_video_call = VideoCallPayment::where('model_id', $user_id)->whereMonth('paid_date', $month)->whereYear('paid_date', $year)->where('status',PAID)->sum($sum_value);

            $yearly_audio_call = AudioCallPayment::where('model_id', $user_id)->whereMonth('paid_date', $month)->whereYear('paid_date', $year)->where('status',PAID)->sum($sum_value);

            $yearly_chat_asset = ChatAssetPayment::where('from_user_id', $user_id)->whereMonth('paid_date', $month)->whereYear('paid_date', $year)->where('status',PAID)->sum($sum_value);

            $yearly_live_video = LiveVideoPayment::where('user_id', $user_id)->whereMonth('created_at', $month)->whereYear('created_at', $year)->where('status',PAID)->sum($sum_value);
            
            $yearly_data->subscription_earnings = $yearly_subscription_earnings ?: 0.00;

            $yearly_data->order_earnings = $yearly_order_earnings ?: 0.00;

            $yearly_data->post_earnings = $yearly_post_earnings ?: 0.00;

            $yearly_data->user_tips_earnings = $yearly_user_tips ?: 0.00;

            $yearly_data->video_call_earnings = $yearly_video_call ?: 0.00;

            $yearly_data->audio_call_earnings = $yearly_audio_call ?: 0.00;

            $yearly_data->chat_asset_earnings = $yearly_chat_asset ?: 0.00;

            $total_earning = ($yearly_subscription_earnings + $yearly_order_earnings + $yearly_post_earnings + $yearly_user_tips + $yearly_video_call + $yearly_audio_call + $yearly_chat_asset + $yearly_live_video) ?? 0.00;

            array_push($yearly_revenues, $yearly_data);

            array_push($yearly_month, $yearly_data->formatted_month);

            array_push($yearly_earning, $total_earning);

        }
        
        $data->yearly_revenues = $yearly_revenues;

        $data->yearly_months = $yearly_month;

        $data->yearly_earnings = $yearly_earning;
        
        return $data;  
    }

    public static function last_7_days_status($type, $user_id, $last_week_payments = 0, $current_week_payments = 0) {

        $current_week_from = today()->subDays(6);

        $current_week_to = today();

        $last_week_from = today()->subDays(13);

        $last_week_to = today()->subDays(7);

        $sum_value = Setting::get('is_only_wallet_payment') ? 'token' : 'user_amount';

        switch($type) {

            case SUBSCRIPTION_PAYMENTS:

                $current_week_payments = UserSubscriptionPayment::where('to_user_id', $user_id)->whereBetween('paid_date', [$current_week_from, $current_week_to])->sum($sum_value);

                $last_week_payments = UserSubscriptionPayment::where('to_user_id', $user_id)->whereBetween('paid_date', [$last_week_from, $last_week_to])->sum($sum_value);

                return last_week_calculations($last_week_payments, $current_week_payments);

            case USER_TIPS:

                $current_week_payments = UserTip::where('to_user_id', $user_id)->whereBetween('paid_date', [$current_week_from, $current_week_to])->sum($sum_value);

                $last_week_payments = UserTip::where('to_user_id', $user_id)->whereBetween('paid_date', [$last_week_from, $last_week_to])->sum($sum_value);

                return last_week_calculations($last_week_payments, $current_week_payments);

            case POST_PAYMENTS:

                $post_ids = Post::where('user_id', $user_id)->pluck('id');

                $current_week_payments = PostPayment::whereIn('post_id', $post_ids)->whereBetween('paid_date', [$current_week_from, $current_week_to])->sum($sum_value);

                $last_week_payments = PostPayment::whereIn('post_id', $post_ids)->whereBetween('paid_date', [$last_week_from, $last_week_to])->sum($sum_value);

                return last_week_calculations($last_week_payments, $current_week_payments); 

            case VIDEO_CALL_PAYMENTS:

                $current_week_payments = VideoCallPayment::where('model_id', $user_id)->whereBetween('paid_date', [$current_week_from, $current_week_to])->sum($sum_value);

                $last_week_payments = VideoCallPayment::where('model_id', $user_id)->whereBetween('paid_date', [$last_week_from, $last_week_to])->sum($sum_value);

                return last_week_calculations($last_week_payments, $current_week_payments); 

            case AUDIO_CALL_PAYMENTS:

                $current_week_payments = AudioCallPayment::where('model_id', $user_id)->whereBetween('paid_date', [$current_week_from, $current_week_to])->sum($sum_value);

                $last_week_payments = AudioCallPayment::where('model_id', $user_id)->whereBetween('paid_date', [$last_week_from, $last_week_to])->sum($sum_value);

                return last_week_calculations($last_week_payments, $current_week_payments); 

            case CHAT_ASSET_PAYMENTS:

                $current_week_payments = ChatAssetPayment::where('from_user_id', $user_id)->whereBetween('paid_date', [$current_week_from, $current_week_to])->sum($sum_value);

                $last_week_payments = ChatAssetPayment::where('from_user_id', $user_id)->whereBetween('paid_date', [$last_week_from, $last_week_to])->sum($sum_value);

                return last_week_calculations($last_week_payments, $current_week_payments); 

            case ORDER_PAYMENTS:

                $order_ids = Order::where('user_id', $user_id)->pluck('id');

                $current_week_payments = OrderPayment::whereIn('order_id', $order_ids)->whereBetween('paid_date', [$current_week_from, $current_week_to])->sum(Setting::get('is_only_wallet_payment') ? 'user_token' : 'total');

                $last_week_payments = OrderPayment::whereIn('order_id', $order_ids)->whereBetween('paid_date', [$last_week_from, $last_week_to])->sum(Setting::get('is_only_wallet_payment') ? 'user_token' : 'total');

                return last_week_calculations($last_week_payments, $current_week_payments); 

            case LIVE_VIDEO_PAYMENTS:

                $current_week_payments = LiveVideoPayment::where('user_id', $user_id)->whereBetween('created_at', [$current_week_from, $current_week_to])->sum($sum_value);

                $last_week_payments = LiveVideoPayment::where('user_id', $user_id)->whereBetween('created_at', [$last_week_from, $last_week_to])->sum($sum_value);

                return last_week_calculations($last_week_payments, $current_week_payments); 

            case TOTAL_PAYMENTS:

                return last_week_calculations($last_week_payments, $current_week_payments); 
        }
    }

    /**
     * @method get_viewer_live_videos_count()
     *
     * @uses to get total for live videos list api
     *
     * @created Karthick
     *
     * @updated 
     *
     * @param request user_id
     *
     * @return live videos count
     */
    public static function get_viewer_live_videos_count($user_id) {

        $user_following_ids = get_follower_ids($user_id);

        $blocked_users = array_merge([$user_id], blocked_users($user_id));

        $live_videos = LiveVideo::CurrentLive()->whereNotIn('user_id', $blocked_users)->select(['user_id'])->get();

        foreach($live_videos as $key => $live_video) {

            $block_users = blocked_users($live_video->user_id);

            if($live_video->type == TYPE_PRIVATE && !in_array($live_video->user_id, $user_following_ids) || in_array($user_id, $block_users) ) {

                $live_videos->forget($key);
            }
        }

        return $live_videos ? $live_videos->count() : 0;
    
    }

    /**
     * Function: upload_file_to_s3
     *
     * @uses used to upload files to S3 Bucket
     *
     * @created Vidhya R
     *
     * @updated Vidhya R
     *
     * @param file $picture
     *
     * @return uploaded file URL
     */

    public static function upload_file_to_s3($picture, $folder_path = "") {

        $s3_url = "";

        $file_name = Helper::file_name();

        $extension = $picture->getClientOriginalExtension();

        $local_url = $folder_path.$file_name . "." . $extension;

        // Check S3 bucket configuration

        if(Setting::get('s3_bucket') == STORAGE_TYPE_S3) {

            $bucket = envfile('AWS_BUCKET');

            $keyname = $local_url;

            $filename = $picture;

            Log::info($bucket);

            Log::info($keyname);

            Log::info(envfile('AWS_DEFAULT_REGION'));

            $s3 = new \Aws\S3\S3Client([
                'version' => 'latest',
                'region'  => envfile('AWS_DEFAULT_REGION'),
                'credentials' => array(
                    'key' => envfile('AWS_ACCESS_KEY_ID'),
                    'secret'  => envfile('AWS_SECRET_ACCESS_KEY'),
                  )

            ]);

            $result = $s3->createMultipartUpload([
                'Bucket'       => $bucket,
                'Key'          => $keyname,
                'StorageClass' => 'REDUCED_REDUNDANCY',
                'ACL'          => 'public-read',
                'Metadata'     => [
                    'param1' => 'value 1',
                    'param2' => 'value 2',
                    'param3' => 'value 3'
                ]
            ]);

            $uploadId = $result['UploadId'];

            // Upload the file in parts.

            $parts = [];
           
            try {
                
                $file = fopen($filename, 'r');
                
                $partNumber = 1;
                
                while (!feof($file)) {
                    $result = $s3->uploadPart([
                        'Bucket'     => $bucket,
                        'Key'        => $keyname,
                        'UploadId'   => $uploadId,
                        'PartNumber' => $partNumber,
                        'Body'       => fread($file, 5 * 1024 * 1024),
                    ]);
                    
                    // $parts = [];

                    $parts['Parts'][$partNumber] = [
                        'PartNumber' => $partNumber,
                        'ETag' => $result['ETag'],
                    ];
                    
                    $partNumber++;

                    Log::info("Uploading part {$partNumber} of {$filename}." . PHP_EOL);

                }

                fclose($file);

            } catch (S3Exception $e) {

                $result = $s3->abortMultipartUpload([
                    'Bucket'   => $bucket,
                    'Key'      => $keyname,
                    'UploadId' => $uploadId
                ]);

                Log::info("Upload of {$filename} failed." . PHP_EOL);

            }

            // Complete the multipart upload.

            $result = $s3->completeMultipartUpload([
                'Bucket'   => $bucket,
                'Key'      => $keyname,
                'UploadId' => $uploadId,
               // 'MultipartUpload'    => $parts,
                'MultipartUpload' => Array('Parts' => $parts ? $parts['Parts'] : []),
            ]);

            $url = $s3_url = $result['Location'];

            Log::info("Uploaded {$filename} to {$url}." . PHP_EOL);

        } else {

            $ext = $picture->getClientOriginalExtension();

            $picture->move(public_path() . "/uploads/", $file_name . "." . $ext);

            $local_url = $file_name . "." . $ext;

            $s3_url = Helper::web_url().'/uploads/'.$local_url;
       
        }

        return urldecode($s3_url);
    
    }
}
