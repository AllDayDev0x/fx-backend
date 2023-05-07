<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;


use App\Repositories\PaymentRepository as PaymentRepo;

use Log, Validator, Exception, DB, Setting;

use Illuminate\Http\Request;

class SubscriptionPaymentJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(array $request)
    {
        //
        $this->request = $request;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(Request $request)
    {
        //

        try {

            $current_timestamp = \Carbon\Carbon::now()->toDateTimeString();

            $subscription_payments = \App\Models\UserSubscriptionPayment::where('is_current_subscription',YES)->where('expiry_date','<', $current_timestamp)->get();

            if($subscription_payments->isEmpty()) {

                throw new Exception(api_error(129), 129);

            }

            DB::beginTransaction();

            foreach ($subscription_payments as $subscription_payment){

                $user = \App\Models\User::where('id',  $subscription_payment->from_user_id)->first();

                if ($user){
                    
                    // Check the subscription is available

                    $subscription = \App\Models\UserSubscription::Approved()->firstWhere('id',  $subscription_payment->user_subscription_id);

                    if(!$subscription) {

                        throw new Exception(api_error(129), 129);

                     }


                    $is_user_subscribed_free_plan = $user->one_time_subscription ?? NO;

                    if($subscription->amount <= 0 && $is_user_subscribed_free_plan) {

                        throw new Exception(api_error(130), 130);

                    }

                    $subscription_amount = $subscription_payment->plan_type == PLAN_TYPE_MONTH ? $subscription->monthly_amount : $subscription->yearly_amount;

                    $total = $user_pay_amount = $subscription_amount;

                    $card = \App\Models\UserCard::where('user_id', $subscription->user_id)->firstWhere('is_default', YES);

                    if(!$card) {

                          throw new Exception(api_error(120), 120);

                     }


                     $payment = new \stdClass();

                     $payment->total = $total;

                     $payment->customer_id = $card->customer_id;

                     $payment->card_token = $card->card_token;

                     $payment->user_pay_amount = $user_pay_amount;

                     $payment->paid_amount = $card->paid_amount;

                   
                    $card_payment_response = PaymentRepo::user_subscriptions_payment_by_stripe($payment, $subscription)->getData();

                    if($card_payment_response->success == false) {

                          throw new Exception($card_payment_response->error, $card_payment_response->error_code);

                     }

                     $card_payment_data = $card_payment_response->data;

                     $payment = new \Illuminate\Http\Request();

                     $payment->id = $subscription_payment->from_user_id;

                     $payment->user_id = $subscription_payment->to_user_id;

                     $payment->paid_amount = $card_payment_data->paid_amount;

                     $payment->payment_id = $card_payment_data->payment_id;

                     $payment->subscription_id = $subscription->id;

                     $payment->paid_status = $card_payment_data->paid_status;

                     $payment->plan_type = $subscription_payment->plan_type;



                    $payment_response = PaymentRepo::user_subscription_payments_save($payment, $subscription)->getData();

                    if($payment_response->success) {

                        // Change old status to expired

                        \App\Models\UserSubscriptionPayment::where('id', $subscription_payment->id)->update(['is_current_subscription' => 0]);

                        // Change new is_current_subscription to 1 

                        \App\Models\UserSubscriptionPayment::where('payment_id', $payment_response->data->payment_id)->update(['is_current_subscription' => 1]);

                        Log::info("Subscription Payment Success");

                    } else {

                        throw new Exception($payment_response->error, $payment_response->error_code);

                    }
                } else{

                throw new Exception(api_error(135), 135);

                }
            }

            DB::commit();


        }catch (Exception $e){

            DB::rollback();

            Log::info("SubscriptionPaymentJob Error".print_r($e->getMessage(), true));
        }
    }
}
