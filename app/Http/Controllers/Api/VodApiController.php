<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\VodVideo, App\Models\User, App\Models\VodCategory, App\Models\UserSubscription, App\Models\VodPayment, App\Models\UserWallet;

use App\Helpers\Helper;

use App\Repositories\VodRepository as VodRepo;

use App\Repositories\PaymentRepository as PaymentRepo;

use Srmklive\PayPal\Services\ExpressCheckout;

class VodApiController extends Controller
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
     * @method vod_videos_for_owner()
     *
     * @uses To display all the posts
     *
     * @created Subham
     *
     * @updated 
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function vod_videos_for_owner(Request $request) {

        try {

            $base_query = $total_query = VodVideo::where('user_id', $request->id)->orderBy('vod_videos.created_at', 'desc');

            $vods = $base_query->skip($this->skip)->take($this->take)->get();

            $vods = VodRepo::vods_list_response($vods, $request);

            $data['vods'] = $vods ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method vod_videos_save_for_owner()
     *
     * @uses get the selected post details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function vod_videos_save_for_owner(Request $request) {

        try {
          
            DB::begintransaction();

            
            $rules = [
                'description' => 'required',
                'publish_time' => 'nullable',
                'amount' => 'nullable|numeric|min:1',
                'vod_files' => 'nullable'
            ];

            Helper::custom_validator($request->all(),$rules);

            $vod = VodVideo::find($request->vod_id) ?? new VodVideo;

            $success_code = $vod->id ? 222 : 221;

            $vod->user_id = $request->id;

            $vod->description = $request->description ?: $vod->description;

            $publish_time = $request->publish_time ?: date('Y-m-d H:i:s');

            $vod->publish_time = date('Y-m-d H:i:s', strtotime($publish_time));
            

            if(!$vod->description){

                throw new Exception(api_error(225), 225);  
            }

            if($vod->save()) {

                if($request->vod_files) {

                    $files = explode(',', $request->vod_files);

                    foreach ($files as $key => $vod_file_id) {

                        // $file_input = ['post_id' => $post->id, 'file' => $file];

                        $vod_file = VodVideo::find($vod_file_id);

                        $vod_file->preview_file = $request->hasFile('preview_file') ? Helper::storage_upload_file($request->file('preview_file'), VOD_PATH) : "";

                        $vod_file->vod_id = $vod->id;

                        $vod_file->save();


                    }

                    $amount = $request->amount ?: ($vod->amount ?? 0);

                    $vod->amount = $amount;

                    $vod->is_paid_post = $amount > 0 ? YES : NO;

                    $vod->save();
                }

                if($request->post_category_ids) {
                    
                    $post_category_ids = $request->post_category_ids;
                    
                    if(!is_array($post_category_ids)) {

                        $post_category_ids = explode(',', $post_category_ids);
                        
                    }

                    if($request->user_id) {
                    
                        VodCategory::where('vod_video_id', $request->vod_id)->whereNotIn('post_category_id', $post_category_ids)->delete();
                    }                    


                    foreach ($post_category_ids as $key => $value) {

                        $vod_category = new VodCategory;

                        $vod_category->vod_video_id = $vod->id;
                        
                        $vod_category->post_category_id = $value;

                        $vod_category->status = APPROVED;
                        
                        $vod_category->save();

                    } 
                }

                DB::commit(); 

                $data = $vod;

                return $this->sendResponse(api_success($success_code), $success_code, $data);

            } 

            throw new Exception(api_error(128), 128);
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method vod_videos_view_for_owner()
     *
     * @uses get the selected post details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param integer $vod_id
     *
     * @return JSON Response
     */
    public function vod_videos_view_for_owner(Request $request) {

        try {

            $rules = [
                'vod_id' => 'required|exists:vod_videos,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            $vod = VodVideo::with('vodFiles')->find($request->vod_id);

            if(!$vod) {
                throw new Exception(api_error(226), 226);   
            }

            $data['vod'] = $vod;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method vod_videos_delete_for_owner()
     *
     * @uses To delete content creators vod
     *
     * @created Subham
     *
     * @updated  
     *
     * @param
     * 
     * @return response of details
     *
     */
    public function vod_videos_delete_for_owner(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'vod_id' => 'required|exists:vod_videos,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules,$custom_errors = []);

            $vod = VodVideo::find($request->vod_id);

            if(!$vod) {
                throw new Exception(api_error(226), 226); 
            }

            $vod = VodVideo::destroy($request->vod_id);

            DB::commit();

            $data['vod_id'] = $request->vod_id;

            return $this->sendResponse(api_success(223), $success_code = 223, $data);
            
        } catch(Exception $e){

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }       
         
    }

    /**
     * @method vod_videos_files_upload()
     *
     * @uses get the selected vod details
     *
     * @created Subham
     *
     * @updated 
     *
     * @param integer $vod_videos_id
     *
     * @return JSON Response
     */
    public function vod_videos_files_upload(Request $request) {

        try {
            
            $rules = [
                'file' => 'required|file',
                'vod_id' => 'required|exists:vod_videos,id,user_id,'.$request->id
            ];

            $request->file_type = 'video';

            Helper::custom_validator($request->all(),$rules);

            $filename = rand(1,1000000).'-vod-'.$request->file_type;

            $folder_path = VOD_PATH.$request->id.'/';

            $vod_file_url = Helper::post_upload_file($request->file, $folder_path, $filename);

            if($vod_file_url) {

                $vod_file = VodVideo::find($request->vod_id) ?? new VodVideo;

                $vod_file->user_id = $request->id;

                $vod_file->file = $vod_file_url;

                $vod_file->blur_file = $request->file_type == "image" ? Helper::generate_post_blur_file($vod_file->file, $request->file, $request->id) : Setting::get('post_video_placeholder');

                if($request->preview_file){

                    $request->file_type = 'image';

                    $preview_filename = rand(1,1000000).'-vod-pre-'.$request->file_type;

                    $vod_preview_file_url = Helper::post_upload_file($request->preview_file, $folder_path, $preview_filename);

                    $vod_file->preview_file = $request->file_type == "image" ? $vod_preview_file_url : Setting::get('post_video_placeholder');
                    
                }

                $vod_file->save();

            }

            $data['file'] = $vod_file_url;

            $data['vod_file'] = $vod_file;

            return $this->sendResponse(api_success(151), 151, $data);

            
        } catch(Exception $e){ 

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method vod_videos_files_remove()
     *
     * @uses remove the selected file
     *
     * @created Subham
     *
     * @updated
     *
     * @param integer $vod_videos_file_id
     *
     * @return JSON Response
     */
    public function vod_videos_files_remove(Request $request) {

        try {
            
            DB::begintransaction();

            $rules = [
                'file' => 'required',
                'vod_id' => 'required|exists:vod_videos,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(),$rules);

            if($request->vod_id) {

                VodVideo::where('id', $request->vod_id)->delete();

                $folder_path = VOD_PATH.$request->id.'/';

                Helper::storage_delete_file($request->file, $folder_path);

            } else {

                VodFile::where('file', $request->file)->delete();

                $folder_path = VOD_PATH.$request->id.'/';

                Helper::storage_delete_file($request->file, $folder_path);

            }

            DB::commit(); 

            return $this->sendResponse(api_success(152), 152, $data = []);
           
            
        } catch(Exception $e){ 

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        } 
    
    }

    /**
     * @method vod_videos_payment_by_wallet()
     * 
     * @uses send money to other user
     *
     * @created Subham
     *
     * @updated
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function vod_videos_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('users.unique_id', $request->user_unique_id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                throw new Exception(api_error(155), 155);   
            }

            $check_vod_payment = VodPayment::VodPaid($request->id, $user->id)->first();

            if($check_vod_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $user_subscription->vod_amount;

            // Check the user has enough balance 

            $user_wallet = UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if($remaining < $subscription_amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $subscription_amount, 
                'user_pay_amount' => $subscription_amount,
                'paid_amount' => $subscription_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'to_user_id' => $user_subscription->user_id,
                'payment_id' => 'VOD-'.rand(),
                'usage_type' => USAGE_TYPE_SUBSCRIPTION
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $payment_response = PaymentRepo::vod_amount_payments_save($request, $user_subscription)->getData();

                if(!$payment_response->success) {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }

                DB::commit();

                return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method vod_videos_payment_by_stripe()
     *
     * @uses pay for vod using stripes
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function vod_videos_payment_by_stripe(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('users.unique_id', $request->user_unique_id)->first();
            

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;
            
            if(!$user_subscription) {
                throw new Exception(api_error(155), 155);   
            }
           
            $check_vod_payment = VodPayment::VodPaid($request->id, $user->id)->first();

            if($check_vod_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $user_subscription->vod_amount;

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


                $card_payment_response = PaymentRepo::vod_payment_by_stripe($request, $user_subscription)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }


            $payment_response = PaymentRepo::vod_amount_payments_save($request, $user_subscription)->getData();
               

            if(!$payment_response->success) {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

            DB::commit();

                return $this->sendResponse(api_success(140), 140, $payment_response->data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method vod_videos_payment_by_paypal()
     *
     * @uses pay for vod using paypal
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function vod_videos_payment_by_paypal(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'payment_id' => 'required',
                'user_unique_id' => 'required|exists:users,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('users.unique_id', $request->user_unique_id)->first();
            
            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                throw new Exception(api_error(155), 155);   
            }
           
            $check_vod_payment = VodPayment::VodPaid($request->id, $user->id)->first();

            if($check_vod_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $user_subscription->vod_amount;

            $user_pay_amount = $subscription_amount ?: 0.00;

            $request->request->add(['payment_mode'=> PAYPAL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

            // $store_wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $subscription_amount, 
                'user_pay_amount' => $subscription_amount,
                'paid_amount' => $subscription_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'VOD-'.rand(),
                'usage_type' => USAGE_TYPE_VOD
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add([
                    
                    'payment_mode' => PAYMENT_MODE_WALLET,
                ]); 
                
                $payment_response = PaymentRepo::vod_amount_payments_save($request, $user_subscription)->getData();
                 
            }

            if(!$payment_response->success) {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

            DB::commit();

            return $this->sendResponse(api_success(140), 140, $payment_response->data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method vod_videos_payment_by_paypal_direct()
     *
     * @uses pay for vod using paypal
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function vod_videos_payment_by_paypal_direct(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('users.unique_id', $request->user_unique_id)->first();
            
            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription;

            if(!$user_subscription) {
                throw new Exception(api_error(155), 155);   
            }
           
            $check_vod_payment = VodPayment::VodPaid($request->id, $user->id)->first();

            if($check_vod_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $user_subscription->vod_amount;

            $user_pay_amount = $subscription_amount ?: 0.00;
              
            $request->request->add([
                'payment_mode' => PAYPAL,
                'paid_amount'=>$user_pay_amount
            ]);

            if($user_pay_amount > 0) {

                $data = [];

                $data['items'] = [
                    [
                        'name' => Setting::get('site_name'),
                        'price' => $user_pay_amount,
                        'desc'  => 'Subscription Payment for '.$user->username,
                        'qty' => 1
                    ]
                ];
          
                $data['invoice_id'] = $user->id;

                $data['invoice_description'] = $user->username;

                $data['return_url'] = route('user.user_subscriptions_payment.success');

                $data['cancel_url'] = route('user.user_subscriptions_payment.cancel');

                $data['total'] = $user_pay_amount;
          
                $provider = new ExpressCheckout;
          
                $response = $provider->setExpressCheckout($data);
          
                $response = $provider->setExpressCheckout($data, true);
                
                if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
                    
                    /*$request->request->add([
                        'payment_status' => UNPAID,
                        'trans_token' => $response['TOKEN'],
                    ]);*/

                    $request->request->add(['payment_mode'=> PAYPAL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

                    // $store_wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
                    
                    $request->request->add([
                        'payment_mode' => PAYMENT_MODE_WALLET,
                        'total' => $subscription_amount, 
                        'user_pay_amount' => $subscription_amount,
                        'paid_amount' => $subscription_amount,
                        'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                        'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                        'payment_id' => 'VOD-'.rand(),
                        'usage_type' => USAGE_TYPE_VOD
                    ]);

                    $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

                    if($wallet_payment_response->success) {

                        $request->request->add([
                            
                            'payment_mode' => PAYMENT_MODE_WALLET,
                        ]); 
                        
                         $payment_response = PaymentRepo::vod_amount_payments_save($request, $user_subscription)->getData();
                         
                    }

                    $return['redirect_url'] = $response['paypal_link'];

                    if(!$payment_response->success) {
                
                        throw new Exception($payment_response->error, $payment_response->error_code);
                
                    }

                    DB::commit();

                    return $this->sendResponse($message = api_success(162), $code = 162, $return);

                } else {

                    throw new Exception(api_error(113), 113);
                        
                }

            } else {

                $payment_response = PaymentRepo::vod_amount_payments_save($request, $user_subscription)->getData();

            }
            

            if(!$payment_response->success) {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

            DB::commit();

            return $this->sendResponse(api_success(140), 140, $payment_response->data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method vod_videos_payment_by_ccbill()
     *
     * @uses pay for vod using ccbill
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function vod_videos_payment_by_ccbill(Request $request) {

        try {

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('users.unique_id', $request->user_unique_id)->first();
            

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription ?? new \App\Models\UserSubscription;
            
            if(!$user_subscription) {
                throw new Exception(api_error(155), 155);   
            }
           
            $check_vod_payment = VodPayment::VodPaid($request->id, $user->id)->first();

            if($check_vod_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $subscription_amount = $user_subscription->vod_amount;

            $total = $user_pay_amount = $subscription_amount ?: 0.00;

            $request->request->add(['payment_mode'=> CCBILL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

            // $store_wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $subscription_amount, 
                'user_pay_amount' => $subscription_amount,
                'paid_amount' => $subscription_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'VOD-'.rand(),
                'usage_type' => USAGE_TYPE_VOD
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add([
                    
                    'payment_mode' => PAYMENT_MODE_WALLET,
                ]); 
                
                $payment_response = PaymentRepo::vod_amount_payments_save($request, $user_subscription)->getData();
                 
            }

            $data = new \stdClass;

            $data->amount = $total;

            $data->user_id = $request->id;

            $data->user_unique_id = $request->user_unique_id;

            $data->plan_type = $request->plan_type;

            $data->status = SUBSCRIPTION_PAYMENT;

            $ccbill_redirect_link = Helper::subscription_ccbill_details($data);

            $url['redirect_web_url'] = $ccbill_redirect_link;

            $code = 143;

            return $this->sendResponse(api_success($code), $code, $url);
        
        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method vod_videos_home()
     *
     * @uses To display all the posts
     *
     * @created Subham
     *
     * @updated
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function vod_videos_home(Request $request) {

        try {

            $user = User::where('id', $request->id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $blocked_users = blocked_users($request->id);

            $subcribed_id = VodPayment::where('from_user_id',$request->id)->pluck('to_user_id');

            $base_query = $total_query = VodVideo::Approved()->whereNotIn('vod_videos.user_id',$blocked_users)->whereIn('user_id',$subcribed_id)->whereHas('user')->orderBy('vod_videos.created_at', 'desc');

            if($request->search_key) {

                $base_query = $base_query->where('vod_videos.description','LIKE','%'.$request->search_key.'%');
                                   
            }

            if($request->post_category_id) {

                $vod_ids = VodCategory::where('post_category_id',$request->post_category_id)->pluck('vod_video_id');

                $base_query = $base_query->whereIn('id',$vod_ids);
                                   
            }

            $vods = $base_query->skip($this->skip)->take($this->take)->get();

            $vods = VodRepo::vods_list_response($vods, $request);

            $data['vods'] = $vods ?? [];

            $data['total'] = $total_query->count() ?? 0;

            $data['user'] = $this->loginUser;

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method vod_videos_view_for_others()
     *
     * @uses get the selected vod details
     *
     * @created Subham
     *
     * @updated
     *
     * @param
     *
     * @return JSON Response
     */
    public function vod_videos_view_for_others(Request $request) {

        try {

            $user = User::where('id', $request->id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $rules = ['vod_unique_id' => 'required|exists:vod_videos,unique_id'];

            Helper::custom_validator($request->all(),$rules);
            
            $blocked_users = blocked_users($request->id);
            
            $vod = VodVideo::with('vodFiles')->Approved()
                ->whereNotIn('vod_videos.user_id',$blocked_users)
                ->where('vod_videos.unique_id', $request->vod_unique_id)->first();

            if(!$vod) {
                throw new Exception(api_error(226), 226);  
            }

            $vod = VodRepo::vods_single_response($vod, $request);

            $data['vod'] = $vod;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method vod_videos_transaction_users()
     *
     * @uses get the selected vod details
     *
     * @created Subham
     *
     * @updated
     *
     * @param
     *
     * @return JSON Response user vod transaction list
     */
    public function vod_videos_transaction_users(Request $request) {

        try {

            $user = User::where('id', $request->id)->first();

            if(!$user) {
                throw new Exception(api_error(135), 135);
            }
            
            $base_query = $total_query = VodPayment::where('from_user_id', $request->id);

            $vod = $base_query->get();

            if(!$vod) {
                throw new Exception(api_error(228), 228); 
            }

            $data['total'] = $total_query->count() ?? 0;

            $data['vod'] = $vod;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method vod_videos_transaction_content_creator()
     *
     * @uses get the selected vod details
     *
     * @created Subham
     *
     * @updated
     *
     * @param
     *
     * @return JSON Response Content Creator vod transaction list
     */
    public function vod_videos_transaction_content_creator(Request $request) {

        try {



            $user = User::where('id', $request->id)->where('is_content_creator',CONTENT_CREATOR)->first();

            if(!$user) {
                throw new Exception(api_error(229), 229);
            }
            
            $base_query = $total_query = VodPayment::where('to_user_id', $request->id);

            $vod = $base_query->get();

            if(!$vod) {
                throw new Exception(api_error(228), 228); 
            }

            $vods = VodRepo::vods_list_response($vods, $request);

            $data['total'] = $total_query->count() ?? 0;

            $data['vod'] = $vod;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method vod_videos_transaction_content_creator()
     *
     * @uses get the selected vod details
     *
     * @created Subham
     *
     * @updated
     *
     * @param  $payment ID
     *
     * @return JSON Response vod transaction view
     */
    public function vod_videos_transaction_view(Request $request) {

        try {

            $rules = [
                'payment_id' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $user = User::where('id', $request->id)->first();

            if(!$user) {
                throw new Exception(api_error(229), 229);
            }
            
            $vod_videos_transaction = $total_query = VodPayment::where('payment_id', $request->payment_id)->first();

            if(!$vod_videos_transaction) {

                throw new Exception(api_error(214), 214); 
            }

            $data['vod_videos_transaction'] = $vod_videos_transaction;

            return $this->sendResponse($message = "", $success_code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }



}
