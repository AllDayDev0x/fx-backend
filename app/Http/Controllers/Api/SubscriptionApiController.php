<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User, App\Models\Subscription, App\Models\SubscriptionPayment;

use App\Repositories\PaymentRepository as PaymentRepo;

class SubscriptionApiController extends Controller
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
     * @method subscriptions_index()
     *
     * @uses To display all the subscription plans
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function subscriptions_index(Request $request) {

        try {

            $base_query = Subscription::where('subscriptions.status' , APPROVED);

            $is_user_subscribed_free_plan = $this->loginUser->one_time_subscription ?? NO;

            if ($is_user_subscribed_free_plan) {

               $base_query->where('subscriptions.amount','>', 0);

            }

            $subscriptions = $base_query->orderBy('amount', 'asc')->get();

            return $this->sendResponse($message = '' , $code = '', $subscriptions);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_view()
     *
     * @uses get the selected subscription details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_view(Request $request) {

        try {

            $subscription = Subscription::where('subscriptions.status' , APPROVED)->firstWhere('subscriptions.id', $request->subscription_id);

            if(!$subscription) {
                throw new Exception(api_error(129), 129);   
            }

            return $this->sendResponse($message = '' , $code = '', $subscription);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /** 
     * @method subscriptions_payment_by_card()
     *
     * @uses pay for subscription using paypal
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function subscriptions_payment_by_card(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'subscription_id' => 'required|exists:subscriptions,id',
            ];

            $custom_errors = ['subscription_id' => api_error(129)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $subscription = Subscription::Approved()->firstWhere('id',  $request->subscription_id);

            if(!$subscription) {

                throw new Exception(api_error(129), 129);
                
            }

            $is_user_subscribed_free_plan = $this->loginUser->one_time_subscription ?? NO;

            if($subscription->amount <= 0 && $is_user_subscribed_free_plan) {

                throw new Exception(api_error(130), 130);
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $subscription->amount ?? 0.00;

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


                $card_payment_response = PaymentRepo::subscriptions_payment_by_stripe($request, $subscription)->getData();
               	
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $payment_response = PaymentRepo::subscriptions_payment_save($request, $subscription)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $code = 118;

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
     * @method subscriptions_history()
     *
     * @uses get the selected subscription details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param integer $subscription_id
     *
     * @return JSON Response
     */
    public function subscriptions_history(Request $request) {

        try {

            $subscription_payments = SubscriptionPayment::BaseResponse()->where('user_id' , $request->id)->skip($this->skip)->take($this->take)->orderBy('subscription_payments.id', 'desc')->get();

            foreach ($subscription_payments as $key => $value) {

                $value->plan_text = formatted_plan($value->plan ?? 0);

                $value->expiry_date = common_date($value->expiry_date, $this->timezone, 'M, d Y');

                $value->show_autorenewal_options = 
                $value->show_autorenewal_pause_btn = 
                $value->show_autorenewal_enable_btn = HIDE;

                if($key == 0) {

                    $value->show_autorenewal_options = ($value->status && $value->subscription_amount > 0)? SHOW : HIDE;

                    if($value->show_autorenewal_options == SHOW) {

                        $value->show_autorenewal_pause_btn = $value->is_cancelled == AUTORENEWAL_ENABLED ? HIDE : SHOW;

                        $value->show_autorenewal_enable_btn = $value->show_autorenewal_pause_btn ? NO : YES;
                    }

                }
            
            }

            return $this->sendResponse($message = '' , $code = '', $subscription_payments);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }
    
    }

    /**
     * @method subscriptions_autorenewal_status
     *
     * @uses To prevent automatic subscriptioon, user have option to cancel subscription
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param 
     *
     * @return json reponse
     */
    public function subscriptions_autorenewal_status(Request $request) {

        try {

            DB::beginTransaction();

            $user_subscription = SubscriptionPayment::where('subscription_payments.id', $request->user_subscription_id)->where('status', DEFAULT_TRUE)->firstWhere('user_id', $request->id);

            if(!$user_subscription) {

                throw new Exception(api_error(152), 152);   

            }

            // Check the subscription is already cancelled

            if($user_subscription->is_cancelled == AUTORENEWAL_CANCELLED) {

                $user_subscription->is_cancelled = AUTORENEWAL_ENABLED;

                $user_subscription->cancel_reason = $request->cancel_reason ?? '';

            } else {

                $user_subscription->is_cancelled = AUTORENEWAL_CANCELLED;

                $user_subscription->cancel_reason = $request->cancel_reason ?? '';

            }

            $user_subscription->save();

            DB::commit();

            $data['user_subscription_id'] = $request->user_subscription_id;

            $data['is_autorenewal_status'] = $user_subscription->is_cancelled;

            $code = $user_subscription->is_cancelled == AUTORENEWAL_CANCELLED ? 120 : 119;

            return $this->sendResponse(api_success($code) , $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

}