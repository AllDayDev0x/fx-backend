<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use Carbon\Carbon;

use App\Models\User, App\Models\Post, App\Models\UserSubscriptionPayment, App\Models\UserWallet, App\Models\PostLike, App\Models\UserTip, App\Models\PostPayment, App\Models\UserCategory, App\Models\Follower, App\Models\UserPromoCode;

use App\Models\UserLoginSession;

use App\Jobs\ReportJob;

use App\Repositories\PaymentRepository as PaymentRepo;

class CommonRepository {

    /**
     * @method check_promo_code_applicable_to_user()
     *
     * @uses To check the promo code applicable to the user or not
     *
     * @created Arun
     *
     * @updated
     *
     * @param objects $promo - promo details
     *
     * @param objects $user - User details
     *
     * @return response of success/failure message
     */
    public static function check_promo_code_applicable_to_user($user, $promo_code) {

        try {

            $no_of_times_used = UserPromoCode::where('promo_code', $promo_code->promo_code)->sum('no_of_times_used');

            $currentDate = date('Y-m-d');

            if ($currentDate < $promo_code->start_date) {

                throw new Exception(tr('promo_code_not_started'), 101);

            }

            if ($currentDate > $promo_code->expiry_date) {

                throw new Exception(tr('promo_code_expired'), 101);

            }

            if ($no_of_times_used >= $promo_code->no_of_users_limit) {

                throw new Exception(tr('total_no_of_users_maximum_limit_reached'), 101);

            }

            if ($promo_code->user_id != 0 && $user->id != $promo_code->user_id) {

                throw new Exception(tr('promo_code_not_applicable_for_you'), 101);

            }

            $user_promo_code = UserPromoCode::where('user_id', $user->id)->where('promo_code', $promo_code->promo_code)->first();

            // If user promo_code not exists, create a new row

            if (!$user_promo_code) {

                $response_array = ['success' => true, 'message' => tr('create_a_new_coupon_row'), 'code' => 2001];

                return response()->json($response_array);

            }

            if ($user_promo_code->no_of_times_used < $promo_code->per_users_limit) {

                $response_array = ['success' => true, 'message' => tr('add_no_of_times_used_coupon'), 'code'=>2002];

            } else {

                throw new Exception(tr('per_users_limit_exceed'), 101);
            }


            return response()->json($response_array);

        } catch (Exception $e) {

            $response_array = ['success' => false, 'error_messages' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array);
        }

    }

	/**
     *
     * @method user_premium_account_check()
     *
     * @uses premium account user 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     *
     * @return
     */
    public static function user_premium_account_check($user) {

        try {

            if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED) {

                throw new Exception(api_error(157), 157);
                
            }

            if($user->is_document_verified != USER_DOCUMENT_APPROVED) {

                $code = $user->userDocuments->count() ? 158 : 160;

                if($user->is_document_verified == USER_DOCUMENT_DECLINED) {

                    $code = 159;

                }

                throw new Exception(api_error($code), $code);
                
            }

            $check_billing_accounts = $user->userBillingAccounts->where('user_billing_accounts.is_default', YES)->first();

            if($check_billing_accounts) {

                throw new Exception(api_error(161), 161);
            }

            $response = ['success' => true, 'message' => api_success('')];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     *
     * @method follow_user()
     *
     * @uses Follow the user
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param 
     *
     * @return
     */
    
    public static function follow_user($request, $user = []) {

        try {

            DB::beginTransaction();
            
            // Validation start
            // Follower id
            $rules = [
                'user_id' => 'required|exists:users,id'
            ];

            $custom_errors = ['user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            // Validation end
            if($request->id == $request->user_id) {

                throw new Exception(api_error(136), 136);

            }

            $follow_user = \App\Models\User::where('id', $request->user_id)->first();

            if(!$follow_user) {

                throw new Exception(api_error(135), 135);
            }


            // Check the user already following the selected users
            $follower = \App\Models\Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $request->user_id)->first();

            if($follower) {

                throw new Exception(api_error(137), 137);

            }

            $follower = \App\Models\Follower::where('follower_id', $request->id)->where('user_id', $request->user_id)->first() ?? new \App\Models\Follower;

            $follower->user_id = $request->user_id;

            $follower->follower_id = $request->id;

            $follower->status = DEFAULT_TRUE;

            $follower->save();

            DB::commit();

            $job_data['follower'] = $follower;

            $job_data['timezone'] = $request->timezone ?? '';

            dispatch(new \App\Jobs\FollowUserJob($job_data));

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = NO;

            $response = ['success' => true, 'message' => api_success(128,$follow_user->username ?? 'user'), 'code' => 128, 'data' => $data];

            Log::info("Follow User".print_r($data, true));

            return (object) $response;

        } catch(Exception $e) {

            DB::rollback();

            Log::info("error message".print_r($e->getMessage(), true));

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return (object) $response;
        
        }

    }

    /**
     * @method subscriptions_user_payment_check()
     *
     * @uses Check the post payment status for each post
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function subscriptions_user_payment_check($other_user, $request) {

        $data['is_user_needs_pay'] = $data['is_free_account'] = NO;

        $data['payment_text'] = "";

        $data['unsubscribe_btn_status'] = NO;

        $login_user = \App\Models\User::find($request->id);

        // Check the user already following
        $follower = \App\Models\Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $other_user->user_id)->first();

        if(!$follower) {

            $data['is_user_needs_pay'] = YES;

            $data['is_free_account'] =  NO;

            $data['payment_text'] = tr('subscribe_for_free');
 
        } else {

            $data['unsubscribe_btn_status'] = YES;
        }

        // Check the user has subscribed for this user plans

        // $user_subscription = \App\Models\UserSubscription::where('user_id', $other_user->id)->first();

        $is_only_wallet_payment = Setting::get('is_only_wallet_payment');

        $user_subscription = \App\Models\UserSubscription::where('user_id', $other_user->id)
            ->when($is_only_wallet_payment == NO, function ($q) use ($is_only_wallet_payment) {
                return $q->OriginalResponse();
            })
            ->when($is_only_wallet_payment == YES, function($q) use ($is_only_wallet_payment) {
                return $q->TokenResponse();
            })->first();

        $data['subscription_info'] = emptyObject();

        if($user_subscription) {

            $data['subscription_info'] = $user_subscription ?? emptyObject();

            if($user_subscription->monthly_amount <= 0 && $user_subscription->yearly_amount <= 0) {

                $data['is_free_account'] = YES;

            } else {

                $current_date = Carbon::now()->format('Y-m-d');

                $check_user_subscription_payment = \App\Models\UserSubscriptionPayment::where('user_subscription_id', $user_subscription->id)->where('from_user_id', $request->id)
                    ->where('is_current_subscription',YES)
                    ->whereDate('expiry_date','>=',$current_date)
                    ->where('to_user_id', $other_user->id)->count();
                
                if(!$check_user_subscription_payment) {

                    $data['is_user_needs_pay'] = YES;

                    $data['payment_text'] = tr('unlock_subscription_text', $user_subscription->monthly_amount_formatted);

                    $data['unsubscribe_btn_status'] = NO;

                }
            
            }

        } else {
            
            $data['is_free_account'] = YES;
        }

        return (object)$data;
    
    }

    /**
     * @method followings_list_response()
     *
     * @uses Format the follow user response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function followers_list_response($followers, $request) {
        
        $followers = $followers->map(function ($follower, $key) use ($request) {

                        $other_user = \App\Models\User::OtherResponse()->find($follower->follower_id) ?? new \stdClass; 

                        $other_user->is_block_user = Helper::is_block_user($request->id, $follower->follower_id);

                        $other_user->is_owner = $request->id == $follower->follower_id ? YES : NO;

                        $is_you_following = Helper::is_you_following($request->id, $follower->follower_id);

                        $other_user->show_follow = $is_you_following ? HIDE : SHOW;

                        $other_user->show_unfollow = $is_you_following ? SHOW : HIDE;

                        $other_user->is_fav_user = Helper::is_fav_user($request->id, $follower->follower_id);

                        $follower->otherUser = $other_user ?? [];

                        return $follower;
                    });


        return $followers;

    }

    /**
     * @method followings_list_response()
     *
     * @uses Format the follow user response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function followings_list_response($followers, $request) {
        
        $followers = $followers->map(function ($follower, $key) use ($request) {

                        $other_user = \App\Models\User::OtherResponse()->find($follower->user_id) ?? new \stdClass; 

                        $other_user->is_block_user = Helper::is_block_user($request->id, $follower->user_id);

                        $other_user->is_owner = $request->id == $follower->user_id ? YES : NO;

                        $is_you_following = Helper::is_you_following($request->id, $follower->user_id);

                        $other_user->show_follow = $is_you_following ? HIDE : SHOW;

                        $other_user->show_unfollow = $is_you_following ? SHOW : HIDE;

                        $other_user->is_fav_user = Helper::is_fav_user($request->id, $follower->user_id);

                        $follower->otherUser = $other_user ?? [];

                        return $follower;
                    });


        return $followers;

    }

    /**
     * @method followings_list_response()
     *
     * @uses Format the follow user response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */
    public static function favorites_list_response($fav_users, $request) {
        
         $fav_users = $fav_users->map(function ($data, $key) use ($request) {

                $fav_user = \App\Models\User::OtherResponse()->find($data->fav_user_id) ?? new \stdClass; 

                $fav_user->is_fav_user = Helper::is_fav_user($request->id, $data->fav_user_id);

                $fav_user->is_block_user = Helper::is_block_user($request->id, $data->fav_user_id);

                $fav_user->is_owner = $request->id == $data->fav_user_id ? YES : NO;

                $is_you_following = Helper::is_you_following($request->id, $data->fav_user_id);

                $fav_user->show_follow = $is_you_following ? HIDE : SHOW;

                $fav_user->show_unfollow = $is_you_following ? SHOW : HIDE;

                $data->fav_user = $fav_user ?? [];

                return $data;
        });

        return $fav_users;
    }

    /**
     * @method chat_user_update()
     *
     * @uses 
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param boolean
     *
     * @return boolean response
     */
    public static function chat_user_update($from_user_id,$to_user_id) {

        try {

            DB::beginTransaction();

            $chat_user = \App\Models\ChatUser::where('from_user_id', $from_user_id)->where('to_user_id', $to_user_id)->first() ?? new \App\Models\ChatUser();

            $chat_user->from_user_id = $from_user_id;

            $chat_user->to_user_id = $to_user_id;

            $chat_user->status = $chat_user->status ? NO : YES;
            
            $chat_user->save();
            
            DB::commit();

        } catch(Exception $e) {

            DB::rollback();

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }


    /**
     * @method add_watermark_to_image()
     *
     * @uses add watermark to image
     * 
     * @created Ganesh
     *
     * @updated Ganesh
     * 
     */
    public static function add_watermark_to_image($storage_file_path){
     
        $watermark_image_path =  public_path("storage/".FILE_PATH_SITE.get_video_end(Setting::get('watermark_logo')));
        
        $fileType = pathinfo($storage_file_path,PATHINFO_EXTENSION); 

        $watermark_file_type = pathinfo($watermark_image_path,PATHINFO_EXTENSION); 

        if($watermark_file_type == 'jpg' || $watermark_file_type == 'jpeg'){

           $watermarkImg = imagecreatefromjpeg($watermark_image_path); 

        } else{

           $watermarkImg = imagecreatefrompng($watermark_image_path); 

        }

        $watermark_size = getimagesize($watermark_image_path);

        $width = 150; $height =100;

        if($watermark_size[0] >=1000 || $watermark_size[1] >=1000){

            $width = $watermark_size[0]/7;
            $height = $watermark_size[1]/8;
        }
   
        $watermark_new = imagecreatetruecolor($width ,$height);

        imagealphablending($watermark_new, false);

        imagesavealpha($watermark_new, true);

        imagecopyresampled($watermark_new, $watermarkImg, 0, 0, 0, 0, $width, $height, imagesx($watermarkImg),imagesy($watermarkImg));

        // Allow certain file formats 
        $allowTypes = array('jpg','png','jpeg'); 
 
        if(in_array($fileType,$allowTypes)) {

            switch($fileType){ 
                case 'jpg': 
                    $im = imagecreatefromjpeg($storage_file_path); 
                    break; 
                case 'jpeg': 
                    $im = imagecreatefromjpeg($storage_file_path); 
                    break; 
                case 'png': 
                    $im = imagecreatefrompng($storage_file_path); 
                    break; 
                default: 
                    $im = imagecreatefromjpeg($storage_file_path); 
            } 
        
            $sx = imagesx($watermark_new); 
            $sy = imagesy($watermark_new); 

            if(Setting::get('watermark_position') == WATERMARK_TOP_LEFT){

                imagecopy($im, $watermark_new, -25, -5, 0, 0, imagesx($watermark_new), imagesy($watermark_new));

            } else if(Setting::get('watermark_position') == WATERMARK_TOP_RIGHT){

                imagecopy($im, $watermark_new, imagesx($im) - $sx, -5, 0, 0, imagesx($watermark_new), imagesy($watermark_new));

            } else if(Setting::get('watermark_position') == WATERMARK_BOTTOM_LEFT){
                
                imagecopy($im, $watermark_new, -25, imagesy($im) - $sy + 5, 0, 0, imagesx($watermark_new), imagesy($watermark_new));
            
            } else if(Setting::get('watermark_position') == WATERMARK_BOTTOM_RIGHT){
                
                imagecopy($im, $watermark_new, imagesx($im) - $sx , imagesy($im) - $sy + 5, 0, 0, imagesx($watermark_new), imagesy($watermark_new));
            } else {
                
                imagecopy($im, $watermark_new, (imagesx($im) - $sx)/2, (imagesy($im) - $sy)/2, 0, 0, imagesx($watermark_new), imagesy($watermark_new));
            }
    
            if($fileType == 'jpeg' || $fileType == 'jpg'){

                imagejpeg($im, $storage_file_path); 

            } else{

                imagepng($im, $storage_file_path); 
            }

            imagedestroy($im); 

        }

    }

    /**
     * @method referral_register()
     *
     * @uses Used to Register Referral users
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param Referral code
     *
     * @return boolean
     */
    public static function referral_register($referral_code, $user_details) {

        $referral_code = \App\Models\ReferralCode::firstWhere('referral_code', $referral_code);

        if($referral_code) {

            $user = \App\Models\User::where('id', $referral_code->user_id)->firstWhere('status', USER_APPROVED);

            if($user) {

                $user_referrals =  \App\Models\UserReferral::where('user_id', $referral_code->id)->firstWhere('referral_code', $referral_code) ?? new \App\Models\UserReferral;

                $user_referrals->user_id = $user_details->id;

                $user_referrals->parent_user_id = $referral_code->user_id;

                $user_referrals->referral_code_id = $referral_code->id;

                $user_referrals->referral_code = $referral_code->referral_code;

                $user_referrals->device_type = $user_details->device_type;

                $referral_earnings = Setting::get('referral_earnings') ?: 0;

                $referrer_earnings = Setting::get('referrer_earnings') ?: 0;

                if($user_referrals->save()) {

                    $referral_code->total_referrals = $referral_code->total_referrals + 1;

                    $referral_code->referral_earnings += $referral_earnings;
                    
                    $referral_code->save();

                }


                if($referral_earnings > 0) {
                    
                    $from_user_inputs = [
                        'id' => $referral_code->user_id,
                        'total' => $referral_earnings * Setting::get('token_amount'), 
                        'user_pay_amount' => $referral_earnings * Setting::get('token_amount'),
                        'paid_amount' => $referral_earnings * Setting::get('token_amount'),
                        'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                        'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                        'payment_id' => 'CD-'.rand(),
                        'usage_type' => USAGE_TYPE_REFERRAL,
                        'tokens' => $referral_earnings,
                        'user_token' => $referral_earnings,
                        'user_amount' => $referral_earnings * Setting::get('token_amount')
                    ];

                    $from_user_request = new \Illuminate\Http\Request();

                    $from_user_request->replace($from_user_inputs);

                    PaymentRepo::user_wallets_payment_save($from_user_request);

                }

                if($referrer_earnings > 0) {

                    $referral_codes = \App\Models\ReferralCode::where('user_id', $user_details->id)->first();

                    if(!$referral_codes) {

                        $referral_codes = self::user_referral_code($user_details->id);

                    }

                    $referral_codes->referee_earnings += $referrer_earnings;

                    $referral_codes->save();

                    $to_user_inputs = [
                        'id' => $user_details->id,
                        'total' => $referrer_earnings * Setting::get('token_amount'), 
                        'user_pay_amount' => $referrer_earnings * Setting::get('token_amount'),
                        'paid_amount' => $referrer_earnings * Setting::get('token_amount'),
                        'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                        'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                        'payment_id' => 'CD-'.rand(),
                        'usage_type' => USAGE_TYPE_REFERRAL,
                        'tokens' => $referrer_earnings,
                        'user_token' => $referrer_earnings,
                        'user_amount' => $referrer_earnings * Setting::get('token_amount')
                    ];

                    $to_user_request = new \Illuminate\Http\Request();

                    $to_user_request->replace($to_user_inputs);

                    PaymentRepo::user_wallets_payment_save($to_user_request);
                }

            }
        }

    }

    /**
     * @method user_referral_code()
     *
     * @uses Used to Generate user Referral codes
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param Referral code
     *
     * @return boolean
     */
    public static function user_referral_code($user_id) {

        $referral_codes = new \App\Models\ReferralCode;

        $referral_codes->user_id = $user_id;

        $referral_codes->referral_code = uniqid();

        $referral_codes->total_referrals = $referral_codes->referral_earnings = $referral_codes->referee_earnings = 0;

        $referral_codes->save();

        return $referral_codes;
    }

    /**
     * @method chat_messages_list_response()
     *
     * @uses Format the chat response
     *
     * @created Bhawya N
     * 
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function chat_messages_list_response($chat_messages, $request) {
        
        $chat_messages = $chat_messages->map(function ($chat_message, $key) use ($request) {

            $chat_message->created = $chat_message->created_at->diffForHumans() ?? "";

            $chat_message->chat_asset_url = $chat_message->file_type = '';

            if($chat_message->is_file_uploaded) {
                

                $payment_info = self::chat_user_payment_check($chat_message, $request);

                $is_user_needs_pay = $payment_info->is_user_needs_pay ?? NO; 

                $chat_assets = \App\Models\ChatAsset::where('chat_message_id', $chat_message->id)
                ->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                    return $q->OriginalResponse();
                })
                ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {
                    return $q->BlurResponse();
                })->first();

                $chat_message->chat_asset_url = $chat_assets ? $chat_assets->asset_file : '';

                $chat_message->file_type = $chat_assets ? $chat_assets->file_type : '';

                $chat_message->is_user_needs_pay = $is_user_needs_pay;

                $chat_message->payment_text = $payment_info->payment_text ?? '';
            }

            $chat_message->unsetRelation('chatAssets');

            return $chat_message;

        });

        return $chat_messages;

    }
    /**
    * @method posts_single_response()
     *
     * @uses Format the post response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function chat_messages_single_response($chat_message, $request) {
        
        $chat_message->is_paid = $chat_message->is_paid;

        $chat_message->created = $chat_message->created_at->diffForHumans() ?? "";

        $chat_message->chat_asset_url = $chat_message->file_type = '';

        if($chat_message->is_file_uploaded) {
            
            $payment_info = self::chat_user_payment_check($chat_message, $request);

            $is_user_needs_pay = $payment_info->is_user_needs_pay ?? NO; 

            $chat_assets = \App\Models\ChatAsset::where('chat_message_id', $chat_message->id)
            ->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                return $q->OriginalResponse();
            })
            ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {
                return $q->BlurResponse();
            })->first();

            $chat_message->chat_asset_url = $chat_assets ? $chat_assets->asset_file : '';

            $chat_message->file_type = $chat_assets ? $chat_assets->file_type : '';

            $chat_message->is_user_needs_pay = $is_user_needs_pay;

            $chat_message->payment_text = $payment_info->payment_text ?? '';
        }

        $chat_message->unsetRelation('chatAssets');

        return $chat_message;
    
    }

    /**
     * @method chat_user_payment_check()
     *
     * @uses Check the chat payment status for each post
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function chat_user_payment_check($chat_message, $request) {

        $chat_from_user = $chat_message->from_user_id ?? [];
        
        $data['is_user_needs_pay'] = NO;

        $data['payment_text'] = "";

        if(!$chat_from_user) {

            goto chat_end;

        }

        if($chat_from_user == $request->id) {

            goto chat_end;
        }

        if(!$chat_message->is_paid) {

            goto chat_end;
        }
        
        $chat_asset_payment = \App\Models\ChatAssetPayment::where('from_user_id', $chat_message->from_user_id)->where('to_user_id',$request->id)->where('chat_message_id', $chat_message->id)->where('status', PAID)->count();

        if(!$chat_asset_payment) {

            $data['is_user_needs_pay'] = YES;

            $data['payment_text'] = tr('unlock_message_text', $chat_message->amount_formatted);
        }
        
        chat_end:

        return (object)$data;
    
    }


    /**
     * @method video_call_payment_check()
     *
     * @uses to check for the video call payment
     * 
     * @created Ganesh
     *
     * @updated Ganesh
     * 
     */

    public static function video_call_payment_check($video_call_request) {

        $is_user_needs_to_pay = NO;

        if($video_call_request->amount > 0) {

            $is_user_needs_to_pay = \App\Models\VideoCallPayment::where('video_call_request_id', $video_call_request->id)->where('user_id',$video_call_request->user_id)->where('status', PAID_STATUS)->count() ? NO : YES;

        }

        return $is_user_needs_to_pay;
    }


    /**
     * @method send_report()
     *
     * @uses Send the email report to content creator
     *
     * @created Subham
     * 
     * @updated 
     *
     * @param object $user_id
     *
     * @return object $email_data
     */

    public static function send_report($StartDate,$EndDate,$user_id,$type) {
        
        $post = Post::where('user_id',$user_id)->get();

        $data=[];

        $data['posts'] = $post->whereBetween('publish_time', [$StartDate, $EndDate])->count();

        $post = $post->pluck('id');

        $data['subscription_payment'] = UserSubscriptionPayment::where('to_user_id',$user_id)->whereBetween('paid_date', [$StartDate, $EndDate])->sum('amount');

        $data['post_payment'] = PostPayment::whereIn('post_id',$post)->whereBetween('paid_date', [$StartDate, $EndDate])->sum('paid_amount');

        $data['tip_payment'] = UserTip::whereIn('post_id',$post)->whereBetween('paid_date', [$StartDate, $EndDate])->count();

        $data['likes'] = PostLike::whereIn('post_id',$post)->whereBetween('created_at', [$StartDate, $EndDate])->count();

        $data['followers'] = Follower::where('user_id',$user_id)->whereBetween('created_at', [$StartDate, $EndDate])->count();

        $data['followings'] = Follower::where('follower_id',$user_id)->whereBetween('created_at', [$StartDate, $EndDate])->count();

        $data['user_id'] = $user_id;

        $data['type'] = $type;

        dispatch(new ReportJob($data));

    }

    /**
     * @method audio_call_payment_check()
     *
     * @uses to check for the audio call payment
     * 
     * @created Arun
     *
     * @updated 
     * 
     */

    public static function audio_call_payment_check($audio_call_request) {

        $is_user_needs_to_pay = NO;

        if($audio_call_request->amount > 0) {

            $is_user_needs_to_pay = \App\Models\AudioCallPayment::where('audio_call_request_id', $audio_call_request->id)->where('user_id',$audio_call_request->user_id)->where('status', PAID_STATUS)->count() ? NO : YES;

        }

        return $is_user_needs_to_pay;
    }

    /**
     * @method create_default_user_login_session()
     *
     * @uses to create a default login session if no device_model found in $request
     * 
     * @created Karthick
     *
     * @updated 
     * 
     */

    public static function create_default_user_login_session($request, $user_id) {

        $user_login_session = UserLoginSession::firstWhere(['user_id' => $user_id, 'device_model' => $user_id]) ? : new UserLoginSession;

        $user_login_session->user_id = $user_id;

        $user_login_session->device_type = DEVICE_WEB;

        $user_login_session->device_model = $user_id;

        $session->device_unique_id = $user_id;

        $user_login_session->device_token = $request->device_token ? : $user_login_session->device_token;

        $user_login_session->browser_type = $request->browser_type ? : $user_login_session->browser_type;

        $user_login_session->ip_address = $request->ip() ? : $user_login_session->ip_address;

        $user_login_session->is_current_session = IS_CURRENT_SESSION;

        $user_login_session->last_session = Carbon::now();

        $user_login_session->token = Helper::generate_token();

        $user_login_session->token_expiry = Helper::generate_token_expiry();

        $user_login_session->status = APPROVED;

        $user_login_session->save();
        
    }

}