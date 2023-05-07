<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting, Session;

use App\Models\User, App\Models\PromoCode;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\CommonRepository as CommonRepo;

use Carbon\Carbon;

use Srmklive\PayPal\Services\ExpressCheckout;

class PaypalPaymentController extends Controller
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
     * @method user_subscriptions_payment_by_paypal_direct()
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

    public function user_subscriptions_payment_by_paypal_direct(Request $request) {

        try {
            
            DB::beginTransaction();

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

                            $return['redirect_url'] = $response['paypal_link'];

                            DB::commit();

                            return $this->sendResponse($message = api_success(162), $code = 162, $return);
                        } else {

                            throw new Exception($payment_response->error, $payment_response->error_code);
                        }
                         
                    } else {

                        throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);    
                    }  

                } else {

                	throw new Exception(api_error(113), 113);
	                    
                }

            } else {

                $payment_response = PaymentRepo::user_subscription_payments_save($request, $user_subscription, $promo_code)->getData();

            }
            

            if(!$payment_response->success) {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }

            DB::commit();

            $code = $subscription_amount > 0 ? 140 : 235;

            return $this->sendResponse(api_success($code), $code, $payment_response->data);
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /**
     * @method user_subscriptions_payment_success()     
     *
     * @uses subscription payment success
     *
     * cretaed Bhawya
     *   
     * updated Bhawya
     *
     * @param 
     *
     * @return success/failure message
     */
    public function user_subscriptions_payment_success(Request $request)
    {   
        
        $provider = new ExpressCheckout;

        $response = $provider->getExpressCheckoutDetails($request->token);

        $sub_user_id = explode('_', $response['INVNUM'])[0];

        $user_subscription_payment = \App\Models\UserSubscriptionPayment::where('trans_token', $response['TOKEN'])->first();
        
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {

        	$user_subscription_payment->status = PAID;

            $user_subscription_payment->save();

        	$user = \App\Models\User::where('id', $sub_user_id)->first();

            PaymentRepo::user_subscription_payments_wallet_update($request, '', $user_subscription_payment);

            $request->request->add(['user_id' => $sub_user_id,'id' => $user_subscription_payment->from_user_id]);

            \App\Repositories\CommonRepository::follow_user($request);

            $response_array = [ 'success' => true, 'message' => tr('payment_success'), 'amount' => $user_subscription_payment->amount];

            return view('paypal.paypal-response')
                    ->with('data', $response_array);

        } else {

        	$user_subscription_payment->status = UNPAID;

        	$user_subscription_payment->save();

            $response_array = ['success'=>false, 'error_messages'=>tr('payment_failure')];

           	return view('paypal.paypal-response')
                ->with('data', $response_array);

        }
  
    }


    /**
     * @method user_subscriptions_payment_cancel()     
     *
     * @uses
     *
     * cretaed Bhawya
     *   
     * updated Bhawya  
     *
     * @param
     *
     * @return
     */
    public function user_subscriptions_payment_cancel(Request $request)
    {
 	
        $user_subscription_payment = \App\Models\UserSubscriptionPayment::where('trans_token', $request->token)->first();

        $response_array = ['success'=>false, 'error_messages'=>tr('payment_failure')];

        return view('paypal.paypal-response')
            ->with('data', $response_array);

    }
  

    /** 
     * @method tips_payment_by_paypal()
     *
     * @uses tip payment to user
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

    public function tips_payment_by_paypal_direct(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'post_id' => 'nullable|exists:posts,id',
                'user_id' => 'required|exists:users,id',
                'amount' => 'required|min:0'
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


            $user_pay_amount = $request->amount ?: 1;

            $request->request->add(['payment_mode' => PAYPAL, 'paid_amount'=>$user_pay_amount]);

            if($user_pay_amount > 0) {

                $data = [];

                $data['items'] = [
                    [
                        'name' => Setting::get('site_name'),
                        'price' => $user_pay_amount,
                        'desc'  => 'Tip Payment for '.$user->username,
                        'qty' => 1
                    ]
                ];
          
                $data['invoice_id'] = $user->id;

                $data['invoice_description'] = $user->username;

                $data['return_url'] = route('user.tips_payment.success');

                $data['cancel_url'] = route('user.tips_payment.cancel');

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

                            $return['redirect_url'] = $response['paypal_link'];

                            DB::commit();

                            return $this->sendResponse($message = api_success(162), $code = 162, $return);

                        } else {

                            throw new Exception($payment_response->error, $payment_response->error_code);
                        }

                    } else {

                        throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                    }  		

                } else {

                	throw new Exception(api_error(113), 113);
	                    
                }

            } else {

                $payment_response = PaymentRepo::tips_payment_save($request)->getData();

                if($payment_response->success) {
                
	                DB::commit();
	                
	                $job_data['user_tips'] = $request->all();

	                $job_data['timezone'] = $this->timezone;
	    
	                $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

	                return $this->sendResponse(api_success(146), 146, $payment_response->data);

	            } else {
	              
	                throw new Exception($payment_response->error, $payment_response->error_code);
	                
	            }

            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

     /**
     * @method tips_payment_success()     
     *
     * @uses subscription payment success
     *
     * cretaed Bhawya
     *   
     * updated Bhawya
     *
     * @param 
     *
     * @return success/failure message
     */
    public function tips_payment_success(Request $request)
    {   
        
        $provider = new ExpressCheckout;

        $response = $provider->getExpressCheckoutDetails($request->token);

        $sub_user_id = explode('_', $response['INVNUM'])[0];

        $user_tip = \App\Models\UserTip::where('trans_token', $response['TOKEN'])->first();
        
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {

        	$user_tip->status = PAID;

            $user_tip->save();
            	
        	$request->request->add(['id' => $user_tip->user_id, 'to_user_id' => $user_tip->to_user_id,'message' => $user_tip->message]);

            PaymentRepo::tips_payment_wallet_update($request, $user_tip);

        	$user = \App\Models\User::where('id', $sub_user_id)->first();
                
            $job_data['user_tips'] = $request->all();

            $this->dispatch(new \App\Jobs\TipPaymentJob($job_data));

            $response_array = [ 'success' => true, 'message' => tr('payment_success'), 'amount' => $user_tip->amount];

            return view('paypal.paypal-response')
                    ->with('data', $response_array);

        } else {

        	$user_tip->status = UNPAID;

        	$user_tip->save();

            $response_array = ['success'=>false, 'error_messages'=>tr('payment_failure')];

           	return view('paypal.paypal-response')
                ->with('data', $response_array);

        }
  
    }


    /**
     * @method tips_payment_cancel()     
     *
     * @uses
     *
     * cretaed Bhawya
     *   
     * updated Bhawya  
     *
     * @param
     *
     * @return
     */
    public function tips_payment_cancel(Request $request)
    {
 	
        $user_tip = \App\Models\UserTip::where('trans_token', $request->token)->first();

        $response_array = ['success'=>false, 'error_messages'=>tr('payment_failure')];

        return view('paypal.paypal-response')
            ->with('data', $response_array);

    }

    /** 
     * @method posts_payment_by_paypal_direct()
     *
     * @uses pay for subscription using paypal
     *
     * @created Ganesh
     *
     * @updated Subham
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function posts_payment_by_paypal_direct(Request $request) {

        try {

            DB::beginTransaction();

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

            $user_pay_amount = $post_amount ?: 0.00;

            $request->request->add(['payment_mode'=> PAYPAL,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id]);

            if($user_pay_amount > 0) {

                $data = [];

                $data['items'] = [
                    [
                        'name' => Setting::get('site_name'),
                        'price' => $user_pay_amount,
                        'desc'  => 'PPV Payment for '.$post->unique_id,
                        'qty' => 1
                    ]
                ];
          
                $data['invoice_id'] = $post->id;

                $data['invoice_description'] = $post->unique_id;

                $data['return_url'] = route('user.posts_payment.success');

                $data['cancel_url'] = route('user.posts_payment.cancel');

                $data['total'] = $user_pay_amount;
          
                $provider = new ExpressCheckout;
          
                $response = $provider->setExpressCheckout($data);
          
                $response = $provider->setExpressCheckout($data, true);
                
                if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
                    
           //          $request->request->add([
		         //    	'payment_status' => UNPAID,
		         //    	'trans_token' => $response['TOKEN'],
		        	// ]);

                    $request->request->add(['payment_mode'=> PAYPAL,'user_pay_amount' => $user_pay_amount,'paid_amount' => $user_pay_amount, 'payment_id' => $request->payment_id, 'paid_status' => PAID_STATUS]);

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

                            $return['redirect_url'] = $response['paypal_link'];

                            DB::commit();

                            return $this->sendResponse($message = api_success(162), $code = 162, $return);
                        } else {

                            throw new Exception($payment_response->error, $payment_response->error_code);
                        }
                    } else {

                        throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                    }   

                } else {

                	throw new Exception(api_error(113), 113);
	                    
                }

            } else {

	            $payment_response = PaymentRepo::post_payments_save($request, $post, $promo_amount)->getData();

	            if($payment_response->success) {
	                
	                $job_data['post_payments'] = $request->all();

	                $job_data['timezone'] = $this->timezone;

	                $this->dispatch(new \App\Jobs\PostPaymentJob($job_data));
	                
	                DB::commit();

	                return $this->sendResponse(api_success(140), 140, $payment_response->data);

	            } else {

	                throw new Exception($payment_response->error, $payment_response->error_code);
	            }  
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method posts_payment_success()     
     *
     * @uses subscription payment success
     *
     * cretaed Bhawya
     *   
     * updated Bhawya
     *
     * @param 
     *
     * @return success/failure message
     */
    public function posts_payment_success(Request $request)
    {   
        
        $provider = new ExpressCheckout;

        $response = $provider->getExpressCheckoutDetails($request->token);

        $post_id = explode('_', $response['INVNUM'])[0];

        $post_payment = \App\Models\PostPayment::where('trans_token', $response['TOKEN'])->first();
        
        if (in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {

        	$post_payment->status = PAID;

            $post_payment->save();
            
            $post = \App\Models\Post::PaidApproved()->firstWhere('posts.id',  $post_id);

        	$request->request->add(['post_id' => $post->id, 'user_id' => $post_payment->user_id]);

            PaymentRepo::post_payment_wallet_update($request, $post,$post_payment);

        	$job_data['post_payments'] = $request->all();

	        $job_data['timezone'] = $this->timezone;

	       	$this->dispatch(new \App\Jobs\PostPaymentJob($job_data));

            $response_array = [ 'success' => true, 'message' => tr('payment_success'), 'amount' => $post_payment->paid_amount];

            return view('paypal.paypal-response')
                    ->with('data', $response_array);

        } else {

        	$post_payment->status = UNPAID;

        	$post_payment->save();

            $response_array = ['success'=>false, 'error_messages'=>tr('payment_failure')];

           	return view('paypal.paypal-response')
                ->with('data', $response_array);

        }
  
    }


    /**
     * @method posts_payment_cancel()     
     *
     * @uses
     *
     * cretaed Bhawya
     *   
     * updated Bhawya  
     *
     * @param
     *
     * @return
     */
    public function posts_payment_cancel(Request $request)
    {
 	
        $user_tip = \App\Models\PostPayment::where('trans_token', $request->token)->first();

        $response_array = ['success'=>false, 'error_messages'=>tr('payment_failure')];

        return view('paypal.paypal-response')
            ->with('data', $response_array);

    }
}