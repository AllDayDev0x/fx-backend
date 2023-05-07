<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\CommonRepository as CommonRepo;

use App\Models\User, App\Models\PromoCode;

use App\Helpers\EnvEditorHelper;

class CCBillPaymentController extends Controller
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
     * @method tips_payment_by_ccbill() 
     *
     * @uses Tip Payment By ccbill
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */     

    public function tips_payment_by_ccbill(Request $request) {

        try {

            $rules = [
                'post_id' => 'nullable|exists:posts,id',
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|numeric|min:1'
            ];

            $custom_errors = ['post_id' => api_error(139), 'user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {
                throw new Exception(api_error(154), 154);
                
            }

            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            $user = \App\Models\User::Approved()->firstWhere('users.id',  $request->user_id);

            if(!$user) {

                throw new Exception(api_error(135), 135);
                
            }

            $total = $user_pay_amount = $request->amount ?: 1;

             $request->request->add(['payment_mode'=> CCBILL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

            // $store_wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $user_pay_amount, 
                'user_pay_amount' => $user_pay_amount,
                'paid_amount' => $user_pay_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-'.rand(),
                'usage_type' => USAGE_TYPE_PPV
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add([
                    
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'from_user_id' => $request->id,
                    'to_user_id' => $request->user_id
                ]); 
                
                $payment_response = PaymentRepo::tips_payment_save($request, $post)->getData();

                if($payment_response->success) {

                    $data = new \stdClass;

                    $data->amount = $request->amount;

                    $data->from_user_id = $request->id;

                    $data->to_user_id = $request->user_id;

                    $data->unique_id = $user->unique_id;

                    $data->status = TIP_PAYMENT;

                    $ccbill_redirect_link = Helper::ccbill_details($data);

                    $url['redirect_web_url'] = $ccbill_redirect_link;

                    return $this->sendResponse($message = api_success(162), $code = 162, $url);
                 
                } else {
                      
                    throw new Exception($payment_response->error, $payment_response->error_code);
                        
                }

             } else {
                  
                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);       
            }
            
        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method posts_payment_by_ccbill() 
     *
     * @uses Post Payment By ccbill
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */     

    public function posts_payment_by_ccbill(Request $request) {

        try {

            // Validation start

            $rules = [
                'post_id' => 'required|exists:posts,id',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',

            ];

            $custom_errors = ['post_id' => api_error(139)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            if(!$post) {

                throw new Exception(api_error(146), 146);
                
            }

            if($request->id == $post->user_id) {

                throw new Exception(api_error(171), 171);
                
            }

            $check_post_payment = \App\Models\PostPayment::UserPaid($request->id, $request->post_id)->first();

            if($check_post_payment) {

                throw new Exception(api_error(145), 145);
                
            }

            $post_amount = $post->amount;

            $user_details = $this->loginUser;

            $promo_amount = 0;

            if ($request->promo_code) {

                $promo_code = PromoCode::where('promo_code', $request->promo_code)->first();
 
                $check_promo_code = CommonRepo::check_promo_code_applicable_to_user($user_details,$promo_code)->getData();

                if ($check_promo_code->success == false) {

                    throw new Exception($check_promo_code->error_messages, $check_promo_code->error_code);
                }else{

                    $promo_amount = promo_calculation($post_amount,$request);

                    $post_amount = $post_amount - $promo_amount;
                }

            }

            $total = $user_pay_amount = $post_amount ?: 0.00;

            $request->request->add(['payment_mode'=> CCBILL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

            // $store_wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
            
            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $post_amount, 
                'user_pay_amount' => $post_amount,
                'paid_amount' => $post_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WPP-'.rand(),
                'usage_type' => USAGE_TYPE_PPV
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add([
                    
                    'payment_mode' => PAYMENT_MODE_WALLET,
                ]); 
                $payment_response = PaymentRepo::post_payments_save($request, $post, $promo_amount)->getData();

                if($payment_response->success) {

                    $data = new \stdClass;

                    $data->amount = $total;

                    $data->user_id = $request->id;

                    $data->post_id = $request->post_id;

                    $data->post_unique_id = $post->unique_id;

                    $data->status = POST_PAYMENT;

                    $ccbill_redirect_link = Helper::post_ccbill_details($data);

                    $url['redirect_web_url'] = $ccbill_redirect_link;

                    return $this->sendResponse($message = api_success(162), $code = 162, $url);

                } else {

                    throw new Exception($payment_response->error, $payment_response->error_code);
                }
                 
            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
            }    

        } catch(Exception $e) {

            // Something else happened, completely unrelated to Stripe
            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method subscriptions_payment_by_ccbill()
     *
     * @uses pay for subscription using ccbill
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function subscriptions_payment_by_ccbill(Request $request) {

        try {

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
                'promo_code'=>'nullable|exists:promo_codes,promo_code',
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
                'payment_id' => 'WPP-'.rand(),
                'usage_type' => USAGE_TYPE_PPV
            ]);

            $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($wallet_payment_response->success) {

                $request->request->add([
                    
                    'payment_mode' => PAYMENT_MODE_WALLET,
                ]); 
                
                $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription, $promo_code)->getData();

                if($payment_response->success) {

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
                } else {

                    throw new Exception($payment_response->error, $payment_response->error_code);

                } 

            } else {

                throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
            }

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method payment_success() 
     *
     * @uses
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */    
    public function payment_success(Request $request) {

        if ($request->status == TIP_PAYMENT) {

            return redirect(route('tips_payment_success', ['from_user_id'=>$request->from_user_id, 'to_user_id'=>$request->to_user_id, 'paid_amount'=>$request->initialPrice,'unique_id'=>$request->unique_id]));

        } else if ($request->status == POST_PAYMENT) { 

            return redirect(route('posts_payment_success', ['user_id'=>$request->user_id, 'post_id'=>$request->post_id, 'paid_amount'=>$request->initialPrice,'post_unique_id'=>$request->post_unique_id]));

        } else if ($request->status == SUBSCRIPTION_PAYMENT) {

            return redirect(route('subscription_payment_success', ['user_id'=>$request->user_id, 'user_unique_id'=>$request->user_unique_id,'plan_type'=>$request->plan_type, 'paid_amount'=>$request->initialPrice]));

        }

    }

    /**
     * @method tips_payment_success() 
     *
     * @uses Tip Payment By CCBill
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */    
    public function tips_payment_success(Request $request) {
        try {

            DB::beginTransaction();

            $request->request->add([
                'payment_mode' => CCBILL,
                'id' => $request->from_user_id,
            ]);

            $payment_response = PaymentRepo::tips_payment_save($request)->getData();

            if($payment_response->success) {
                
                DB::commit();
                
                $job_data['user_tips'] = $request->all();

                $job_data['timezone'] = $this->timezone;
    
                $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

                $response_array = ['success'=>true, 'message' => tr('payment_success') , 'data' => []];

                return redirect()->away(Setting::get('frontend_url').'/'.$request->unique_id);

            }

            return redirect()->away(Setting::get('frontend_url').'/'.$request->unique_id);

        } catch(Exception $e) {

            DB::rollback();

            $response_array = ['success'=>false, 'error_messages'=>$e->getMessage(), 'error_code'=>$e->getCode()];

            return redirect()->away(Setting::get('frontend_url'));

        }

        return redirect()->away(Setting::get('frontend_url'));

    }

    /**
     * @method tips_payment_failure() 
     *
     * @uses Tip Payment By CCBill
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */   
    public function tips_payment_failure(Request $request) {

    	return redirect()->away(Setting::get('frontend_url'));
    	
    }

    /**
     * @method tips_payment_success() 
     *
     * @uses Tip Payment By CCBill
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */    
    public function posts_payment_success(Request $request) {
        try {

            DB::beginTransaction();

            $request->request->add(['payment_mode'=> CCBILL,'id' => $request->user_id,]);

            Log::info("Request Data".print_r($request->all(), true));

            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $request->post_id);

            $payment_response = PaymentRepo::post_payments_save($request, $post)->getData();

            Log::info("Request Data".print_r($payment_response, true));

            if($payment_response->success) {
                
                DB::commit();

                $response_array = ['success'=>true, 'message' => tr('payment_success') , 'data' => []];

                return redirect()->away(Setting::get('frontend_url').'/post/'.$request->post_unique_id);

            }

            return redirect()->away(Setting::get('frontend_url').'/post/'.$request->post_unique_id);

        } catch(Exception $e) {

            DB::rollback();

            $response_array = ['success'=>false, 'error_messages'=>$e->getMessage(), 'error_code'=>$e->getCode()];

            return redirect()->away(Setting::get('frontend_url'));

        }
    }

    /** 
     * @method subscription_payment_success()
     *
     * @uses Subscription Payment By CCBill
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function subscription_payment_success(Request $request) {

        try {

            DB::beginTransaction();
            
            $request->request->add([
                'payment_mode' => CCBILL, 
                'id' => $request->user_id,
            ]);

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();
            
            if(!$user) {
                throw new Exception(api_error(135), 135);
            }

            $user_subscription = $user->userSubscription ?? new \App\Models\UserSubscription;

            $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription)->getData();
            
            if($payment_response->success) {

                DB::commit();

                $response_array = ['success'=>true, 'message' => tr('payment_success') , 'data' => []];

                return redirect()->away(Setting::get('frontend_url').'/'.$request->user_unique_id);
            }

            return redirect()->away(Setting::get('frontend_url').'/'.$request->user_unique_id);

        } catch(Exception $e) {

            DB::rollback();

            $response_array = ['success'=>false, 'error_messages'=>$e->getMessage(), 'error_code'=>$e->getCode()];

            return redirect()->away(Setting::get('frontend_url'));

        }

    }

}
