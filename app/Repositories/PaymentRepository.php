<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User, App\Models\SubscriptionPayment, App\Models\VodPayment, App\Models\PromoCode;

use App\Models\LiveVideoPayment, App\Models\OrderPayment, App\Models\UserProduct, App\Models\Cart, App\Models\OrderProduct;

class PaymentRepository {

    /**
     * @method vod_payments_wallet_update
     *
     * @uses vod payment amount will update to the vod owner wallet
     *
     * @created Subham
     *
     * @updated
     *
     * @param
     *
     * @return
     */

    public static function vod_payments_wallet_update($request, $vod, $vod_payment) {

        try {

            $to_user_inputs = [
                'id' => $vod->user_id,
                'received_from_user_id' => $vod_payment->user_id,
                'total' => $vod_payment->paid_amount, 
                'user_pay_amount' => $vod_payment->user_amount,
                'paid_amount' => $vod_payment->user_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $vod_payment->payment_id,
                'admin_amount' => $vod_payment->admin_amount,
                'user_amount' => $vod_payment->user_amount,
                'usage_type' => USAGE_TYPE_VOD
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method vod_payment_by_stripe()
     *
     * @uses deduct the subscription amount when logged in user subscribe the other user plans
     *
     * @created subham
     * 
     * @updated
     *
     * @param object $user_subscription, object $request
     *
     * @return object $user_subscription
     */

    public static function vod_payment_by_stripe($request, $user_subscription) {

        try {

            // Check stripe configuration

            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];


            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method vod_amount_payments_save()
     *
     * @uses save the payment details when logged in user subscribe the other user plans
     *
     * @created Subham
     * 
     * @updated
     *
     * @param object $vod, object $request
     *
     * @return object $vod
     */

    public static function vod_amount_payments_save($request, $user_subscription) {

        try {

            $user = User::where('users.unique_id', $request->user_unique_id)->first();

            $previous_payment = VodPayment::where('from_user_id', $request->id)->where('to_user_id', $user_subscription->user_id)->first();

            $vod_payment = new VodPayment;

            $plan = 1;

            $plan_type = PLAN_TYPE_VOD;

            $plan_formatted = $plan." ".$plan_type;

            $vod_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$plan_formatted}"));

            if($previous_payment) {

                if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {
                    $vod_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$plan_formatted}", strtotime($previous_payment->expiry_date)));
                }

                $previous_payment->is_current_subscription = NO;

                $previous_payment->cancel_reason = 'Other plan subscribed';

                $previous_payment->save();
            }

            $vod_payment->from_user_id = $request->id;

            $vod_payment->to_user_id = $user->id;

            $vod_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $vod_payment->status = $request->payment_status ?? PAID_STATUS;

            $vod_payment->amount = $total = $request->paid_amount ?? 0.00;

            $vod_payment->payment_mode = $request->payment_mode ?? CARD;

            $vod_payment->paid_date = now();

            // Commission calculation & update the earnings to other user wallet

            $admin_commission_in_per = Setting::get('subscription_admin_commission', 1)/100;

            $admin_amount = $total * $admin_commission_in_per;

            $user_amount = $total - $admin_amount;

            $vod_payment->admin_amount = $admin_amount ?? 0.00;

            $vod_payment->user_amount = $user_amount ?? 0.00;

            $vod_payment->status = $request->payment_status ?? PAID;

            $vod_payment->save();
            
            // Add to post user wallet
            if($vod_payment->status == PAID_STATUS) {

                if($total > 0) {
                    self::vod_payments_wallet_update($request, $user_subscription, $vod_payment);
                }

                $request->request->add(['user_id' => $user->id]);

                \App\Repositories\CommonRepository::follow_user($request);

            }
            
            $data = ['user_type' => SUBSCRIBED_USER, 'payment_id' => $request->payment_id ?? $vod_payment->payment_id];

            $response = ['success' => true, 'message' => 'paid', 'data' => $data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method user_wallets_payment_save()
     *
     * @uses used to save user wallet payment details
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $user_wallet_payment
     */

    public static function user_wallets_payment_save($request) {

        try {

            $user_wallet_payment = new \App\Models\UserWalletPayment;
            
            $user_wallet_payment->user_id = $request->id;

            $user_wallet_payment->to_user_id = $request->to_user_id ?? 0;

            $user_wallet_payment->received_from_user_id = $request->received_from_user_id ?? 0;

            $user_wallet_payment->user_billing_account_id = $request->user_billing_account_id ?: 0;
            
            $user_wallet_payment->payment_id = $request->payment_id ?:generate_payment_id();

            $user_wallet_payment->paid_amount = $user_wallet_payment->requested_amount = $request->paid_amount ?? 0.00;

            $user_wallet_payment->admin_amount = $request->admin_amount ?? 0.00;

            $user_wallet_payment->user_amount = $request->user_pay_amount ?? 0.00;

            $user_wallet_payment->payment_type = $request->payment_type ?: WALLET_PAYMENT_TYPE_ADD;

            $user_wallet_payment->amount_type = $request->amount_type ?: WALLET_AMOUNT_TYPE_ADD;

            $user_wallet_payment->usage_type = $request->usage_type ?: "";

            $user_wallet_payment->currency = Setting::get('currency') ?? "$";

            $user_wallet_payment->payment_mode = $request->payment_mode ?? PAYMENT_MODE_WALLET;

            $user_wallet_payment->paid_date = date('Y-m-d H:i:s');

            $user_wallet_payment->token = $request->tokens ?? 0;

            $user_wallet_payment->status = $request->paid_status ?: USER_WALLET_PAYMENT_PAID;

            $user_wallet_payment->admin_token = $request->admin_token ?? 0.00;

            $user_wallet_payment->user_token = $request->user_token ?? 0.00;

            if($request->file('bank_statement_picture')) {

                $user_wallet_payment->bank_statement_picture = Helper::storage_upload_file($request->file('bank_statement_picture'));

            }

            $user_wallet_payment->message = "";

            $user_wallet_payment->save();

            $message = strtoupper($request->usage_type)." - " ?: "";

            $message .= get_wallet_message($user_wallet_payment);

            $message .= $request->message ? " - ".$request->message : "";

            $user_wallet_payment->message = $message;

            $user_wallet_payment->save();

            if($user_wallet_payment->payment_type != WALLET_PAYMENT_TYPE_WITHDRAWAL && $user_wallet_payment->status == USER_WALLET_PAYMENT_PAID) {

                self::user_wallet_update($user_wallet_payment,$request->wallet_status ?? '');
            }

            $response = ['success' => true, 'message' => 'paid', 'data' => $user_wallet_payment];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method user_wallets_payment_by_stripe()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallets_payment_by_stripe($request) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: 'USD';

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //                     'amount' => $total,
            //                     'currency' => $currency_code,
            //                     'customer' => $request->customer_id,
            //                 ];


            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,

                                'paid_status' => $stripe_payment_response->paid ?? true
                            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update($user_wallet_payment,$wallet_status = '') {

        try {

            $is_referral_applied = NO; $referral_message = '';

            $user_wallet = \App\Models\UserWallet::where('user_id', $user_wallet_payment->user_id)->first() ?: new \App\Models\UserWallet;

            $user_wallet->user_id = $user_wallet_payment->user_id;

            $user_amount = Setting::get('is_only_wallet_payment') ? $user_wallet_payment->token : $user_wallet_payment->user_amount;

            if($user_wallet_payment->amount_type == WALLET_AMOUNT_TYPE_ADD) {

                $user_wallet->total += $user_amount;

                if(Setting::get('is_referral_enabled') && $user_wallet_payment->usage_type == USAGE_TYPE_REFERRAL) {
                    
                    $user_wallet->referral_amount += $user_amount;

                    $user_wallet->referral_token += $user_wallet_payment->token;

                } else {

                    $user_wallet->remaining += $user_amount;
                }

            } else {

                if($wallet_status == WALLET_ONHOLD && $user_wallet->onhold > 0) {

                    $user_wallet->used += $user_amount;

                    $user_wallet->onhold -= $user_amount;

                } elseif($user_wallet_payment->payment_mode == PAYMENT_MODE_WALLET && Setting::get('is_referral_enabled')) {

                    if($user_wallet->referral_amount >= $user_wallet_payment->user_amount) {

                        $user_wallet->referral_amount -= $user_wallet_payment->user_amount;

                        $user_wallet->referral_token -= $user_wallet_payment->token;

                        $referral_message = tr('REFERRAL_PAYMENT_TYPE_PAID_TEXT');

                        $is_referral_applied = YES;

                    } else {

                        $referral_amount = $user_wallet->referral_amount;
                        
                        $referral_token = $user_wallet->token;

                        $user_wallet->referral_token = 0;

                        $user_wallet->referral_amount = 0;

                        $remaining_to_deduct = ($user_amount - $referral_amount);

                        $user_wallet->remaining -= $remaining_to_deduct;

                        $referral_message = tr('REFERRAL_PAYMENT_TYPE_PARTIAL_TEXT');

                        $is_referral_applied = YES;
                    }

                } else{

                    $user_wallet->used += $user_amount;

                    $user_wallet->remaining -= $user_amount;

                }

            }

            $user_wallet->save();

            if(Setting::get('is_referral_enabled') && $is_referral_applied) {

                $message = strtoupper($user_wallet_payment->usage_type)." - " ?: "";
                $message .= $referral_message;

                $user_wallet_payment->message = $message;

                $user_wallet_payment->save();
                
            }
                

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_withdraw_send()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_withdraw_send($amount, $user_id) {
        
        try {

            $user_wallet = \App\Models\UserWallet::where('user_id', $user_id)->first() ?: new \App\Models\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining -= $amount;

            $user_wallet->onhold += $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_withdraw_cancel()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_withdraw_cancel($amount, $user_id) {

        try {

            $user_wallet = \App\Models\UserWallet::where('user_id', $user_id)->first() ?: new \App\Models\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining += $amount;

            $user_wallet->onhold -= $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_withdraw_paynow()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_withdraw_paynow($amount, $user_id) {

        try {

            $user_wallet = \App\Models\UserWallet::where('user_id', $user_id)->first() ?: new \App\Models\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->onhold -= $amount;

            $user_wallet->used += $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_send()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_send($amount, $user_id) {

        try {

            $user_wallet = \App\Models\UserWallet::where('user_id', $user_id)->first() ?: new \App\Models\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining -= $amount;

            $user_wallet->onhold += $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_cancel()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_cancel($amount, $user_id) {

        try {

            $user_wallet = \App\Models\UserWallet::where('user_id', $user_id)->first() ?: new \App\Models\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining += $amount;

            $user_wallet->onhold -= $amount;

            $user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_approve()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_approve($amount, $user_id, $receiver_user_id) {

        try {

            // Winner wallet update

            $user_wallet = \App\Models\UserWallet::where('user_id', $user_id)->first() ?: new \App\Models\UserWallet;

            $user_wallet->user_id = $user_id;

            $user_wallet->remaining += $amount;

            $user_wallet->used -= $amount;

            $user_wallet->save();

            // Loser wallet update
            $receiver_user_wallet = \App\Models\UserWallet::where('user_id', $receiver_user_id)->first() ?: new \App\Models\UserWallet;

            $receiver_user_wallet->user_id = $receiver_user_id;

            $receiver_user_wallet->total -= $amount;

            $receiver_user_wallet->onhold -= $amount;

            $receiver_user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_dispute_reject()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_dispute_reject($amount, $receiver_user_id) {

        try {

            // Opposite party wallet update
            $receiver_user_wallet = \App\Models\UserWallet::where('user_id', $receiver_user_id)->first() ?: new \App\Models\UserWallet;

            $receiver_user_wallet->user_id = $receiver_user_id;

            $receiver_user_wallet->total += $remaining;

            $receiver_user_wallet->onhold -= $amount;

            $receiver_user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $user_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_wallet_update_invoice_payment()
     *
     * @uses pay for live videos using stripe
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function user_wallet_update_invoice_payment($amount, $sender_id, $to_user_id) {

        try {

            // Receiver wallet update

            $sender_wallet = \App\Models\UserWallet::where('user_id', $sender_id)->first() ?: new \App\Models\UserWallet;

            Log::info("sender_wallet".print_r($sender_wallet->toArray(), true));

            $sender_wallet->user_id = $sender_id;

            $sender_wallet->total += $amount;

            $sender_wallet->remaining += $amount;

            $sender_wallet->save();

            // Payer wallet update
            $to_user_wallet = \App\Models\UserWallet::where('user_id', $to_user_id)->first() ?: new \App\Models\UserWallet;

            Log::info("to_user_wallet".print_r($to_user_wallet->toArray(), true));

            $to_user_wallet->user_id = $to_user_id;

            $to_user_wallet->remaining -= $amount;

            $to_user_wallet->used += $amount;

            $to_user_wallet->save();

            $response = ['success' => true, 'message' => 'done', 'data' => $sender_wallet];

            return response()->json($response, 200);

        } catch(Exception $e) {

            Log::info("error".print_r($e->getMessage(), true));

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method subscriptions_payment_by_stripe()
     *
     * @uses Subscription payment - card
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $subscription, object $request
     *
     * @return object $subscription
     */

    public static function subscriptions_payment_by_stripe($request, $subscription) {

        try {

            // Check stripe configuration

            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];

            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method subscriptions_payment_save()
     *
     * @uses used to save user subscription payment details
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $subscription, object $request
     *
     * @return object $subscription
     */

    public static function subscriptions_payment_save($request, $subscription) {

        try {

            $previous_payment = SubscriptionPayment::where('user_id' , $request->id)
                ->where('status', PAID_STATUS)
                ->orderBy('created_at', 'desc')
                ->first();

            $user_subscription = new SubscriptionPayment;

            $user_subscription->expiry_date = date('Y-m-d H:i:s',strtotime("+{$subscription->plan} months"));

            if($previous_payment) {

                if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {
                    $user_subscription->expiry_date = date('Y-m-d H:i:s', strtotime("+{$subscription->plan} months", strtotime($previous_payment->expiry_date)));
                }
            }

            $user_subscription->subscription_id = $request->subscription_id;

            $user_subscription->user_id = $request->id;

            $user_subscription->payment_id = $request->payment_id ?? "NO-".rand();

            $user_subscription->status = PAID_STATUS;

            $user_subscription->amount = $request->paid_amount ?? 0.00;

            $user_subscription->payment_mode = $request->payment_mode ?? CARD;

            $user_subscription->cancel_reason = $request->cancel_reason ?? '';

            $user_subscription->save();

            // update the earnings
            self::users_account_upgrade($request->id, $request->paid_amount, $subscription->amount, $user_subscription->expiry_date);

            $response = ['success' => true, 'message' => 'paid', 'data' => ['user_type' => SUBSCRIBED_USER, 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method users_account_upgrade()
     *
     * @uses add amount to user
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param integer $user_id, float $admin_amount, $user_amount
     *
     * @return - 
     */
    
    public static function users_account_upgrade($user_id, $paid_amount = 0.00, $subscription_amount, $expiry_date) {

        if($user = User::find($user_id)) {

            $user->user_type = SUBSCRIBED_USER;

            $user->one_time_subscription = $subscription_amount <= 0 ? YES : NO;

            $user->amount_paid += $paid_amount ?? 0.00;

            $user->expiry_date = $expiry_date;

            $user->no_of_days = total_days($expiry_date);

            $user->save();
        
        }
    
    }

    /**
     * @method posts_payment_by_stripe()
     *
     * @uses post payment - card
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $post, object $request
     *
     * @return object $post_paym
     */

    public static function posts_payment_by_stripe($request, $post) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];


            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method orders_payment_by_stripe()
     *
     * @uses order payment - card
     *
     * @created Subham Kant
     * 
     * @updated 
     *
     * @param object $order, object $request
     *
     * @return object $order_paym
     */

    public static function orders_payment_by_stripe($request) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method post_payments_save()
     *
     * @uses used to save post payment details
     *
     * @created Bhawya
     * 
     * @updated Vithya
     *
     * @param object $post, object $request
     *
     * @return object $post_payment
     */

    public static function post_payments_save($request, $post, $promo_amount = 0) {

        try {

            $post_payment = new \App\Models\PostPayment;

            $post_payment->post_id = $request->post_id;

            $post_payment->user_id = $request->id;

            $post_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $post_payment->payment_mode = $request->payment_mode ?? CARD;

            $post_payment->paid_amount = $total = $request->paid_amount ?? 0.00;

            $post_payment->token = $request->tokens ?? 0.00;
            // Commission calculation & update the earnings to other user wallet


            $post_payment->admin_token = 0.00;

            $post_payment->user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_commission_in_per = Setting::get('admin_commission', 1)/100;

                $admin_token = $request->tokens * $admin_commission_in_per;

                $user_token = $request->tokens - $admin_token;

                $post_payment->admin_token = $admin_token;

                $post_payment->user_token = $user_token;

                $post_payment->admin_amount = $admin_token * Setting::get('token_amount');

                $post_payment->user_amount = $user_token * Setting::get('token_amount');

            } else {

                $admin_commission_in_per = Setting::get('admin_commission', 1)/100;

                $admin_amount = $total * $admin_commission_in_per;

                $user_amount = $total - $admin_amount;

                $post_payment->admin_amount = $admin_amount ?? 0.00;

                $post_payment->user_amount = $user_amount ?? 0.00;

            }

            $post_payment->paid_date = date('Y-m-d H:i:s');

            $post_payment->status = $request->payment_status ?? PAID;

            $post_payment->trans_token = $request->trans_token ?? '';

            if ($request->promo_code) {

                $promo_code_val = PromoCode::where('promo_code', $request->promo_code)->first();

                $post_payment->promo_code = $promo_code_val->promo_code;

                $post_payment->promo_code_amount = $promo_amount;

                $post_payment->is_promo_code_applied = PROMO_CODE_APPLIED;

            }

            $post_payment->save();

            // Add to post user wallet
            if($post_payment->status == PAID) {

                self::post_payment_wallet_update($request, $post, $post_payment);

            }
            
            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id, 'post' => $post]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method tips_payment_by_stripe()
     *
     * @uses tips payment - card
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $post, object $request
     *
     * @return object $post_paym
     */

    public static function tips_payment_by_stripe($request, $post) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];

            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method tips_payment_save()
     *
     * @uses used to save tips payment details
     *
     * @created Bhawya
     * 
     * @updated Vithya
     *
     * @param object $post, object $request
     *
     * @return object $post_payment
     */

    public static function tips_payment_save($request) {

        try {

            $user_tip = new \App\Models\UserTip;

            $user_tip->post_id = $request->post_id ?: 0;

            $user_tip->user_id = $request->id ?? 0;

            $user_tip->to_user_id = $request->to_user_id ?? 0;

            $user_tip->user_card_id = $request->user_card_id ?: 0;

            $user_tip->payment_id = $request->payment_id ?? "NO-".rand();

            $user_tip->payment_mode = $request->payment_mode ?? CARD;

            $user_tip->amount = $total = $request->paid_amount ?? 0.00;

            $user_tip->token = $request->tokens ?? 0.00;

            $user_tip->message = $request->message ?: "";

            $user_tip->tips_type = $request->tips_type ? : TIPS_TYPE_PROFILE;

            $user_tip->user_wallet_payment_id = $request->user_wallet_payment_id ?? 0;

            // Commission calculation

            $user_tip->admin_token = 0.00;

            $user_tip->user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $tips_admin_commission_in_per = Setting::get('tips_admin_commission', 1)/100;

                $admin_token = $request->tokens * $tips_admin_commission_in_per;

                $user_token = $request->tokens - $admin_token;

                $user_tip->admin_token = $admin_token;

                $user_tip->user_token = $user_token;

                $user_tip->admin_amount = $admin_token * Setting::get('token_amount');

                $user_tip->user_amount = $user_token * Setting::get('token_amount');

            } else {

                $tips_admin_commission_in_per = Setting::get('tips_admin_commission', 1)/100;

                $tips_admin_amount = $total * $tips_admin_commission_in_per;

                $user_amount = $total - $tips_admin_amount;

                $user_tip->admin_amount = $tips_admin_amount ?? 0.00;
     
                $user_tip->user_amount = $user_amount ?? 0.00;

            }

            $user_tip->paid_date = date('Y-m-d H:i:s');

            $user_tip->status = $request->payment_status ?? PAID;

            $user_tip->trans_token = $request->trans_token ?? '';

            $user_tip->save();

            // Add to post user wallet

            if($user_tip->status == PAID) {
                self::tips_payment_wallet_update($request, $user_tip);
            }
            
            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method post_payment_wallet_update
     *
     * @uses post payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function post_payment_wallet_update($request, $post, $post_payment) {

        try {

            $to_user_inputs = [
                'id' => $post->user_id,
                'received_from_user_id' => $post_payment->user_id,
                'total' => $post_payment->paid_amount, 
                'user_pay_amount' => $post_payment->user_amount,
                'paid_amount' => $post_payment->user_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $post_payment->payment_id,
                'admin_amount' => $post_payment->admin_amount,
                'user_amount' => $post_payment->user_amount,
                'usage_type' => USAGE_TYPE_PPV,
                'user_token' => $post_payment->user_token,
                'admin_token' => $post_payment->admin_token,
                'tokens' => $post_payment->user_token, 
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /** | | | DONT GET CONFUSE WITH ADMIN SUBSCRIPTION. THIS FUNCTIONS ARE USED FOR OTHER USER SUBSCRIPTION PAYMENTs */

    /**
     * @method user_subscription_payments_save()
     *
     * @uses save the payment details when logged in user subscribe the other user plans
     *
     * @created Vithya
     * 
     * @updated Vithya
     *
     * @param object $user_subscription, object $request
     *
     * @return object $user_subscription
     */

    public static function user_subscription_payments_save($request, $user_subscription, $promo_amount = 0) {

        try {

            $user = \App\Models\User::where('users.unique_id', $request->user_unique_id)->first();

            $previous_payment = \App\Models\UserSubscriptionPayment::where('from_user_id', $request->id)->where('to_user_id', $user_subscription->user_id)->where('is_current_subscription', YES)->first();

            $user_subscription_payment = new \App\Models\UserSubscriptionPayment;

            $plan = 1;

            $plan_type = $request->plan_type == PLAN_TYPE_YEAR ? 'years' : 'months';

            $plan_formatted = $plan." ".$plan_type;

            $user_subscription_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$plan_formatted}"));

            if($previous_payment) {

                if (strtotime($previous_payment->expiry_date) >= strtotime(date('Y-m-d H:i:s'))) {
                    $user_subscription_payment->expiry_date = date('Y-m-d H:i:s', strtotime("+{$plan_formatted}", strtotime($previous_payment->expiry_date)));
                }

                $previous_payment->is_current_subscription = NO;

                $previous_payment->cancel_reason = 'Other plan subscribed';

                $previous_payment->save();
            }

            $user_subscription_payment->user_subscription_id = $user_subscription->id ?? 0;

            $user_subscription_payment->from_user_id = $request->id;

            $user_subscription_payment->to_user_id = $user->id;

            $user_subscription_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $user_subscription_payment->status = $request->payment_status ?? PAID_STATUS;

            $user_subscription_payment->is_current_subscription = YES;

            $user_subscription_payment->amount = $total = $request->paid_amount ?? 0.00;

            $user_subscription_payment->payment_mode = $request->payment_mode ?? CARD;

            $user_subscription_payment->paid_date = now();

            $user_subscription_payment->plan = 1;

            $user_subscription_payment->plan_type = $request->plan_type ?: PLAN_TYPE_MONTH;

            $user_subscription_payment->cancel_reason = $request->cancel_reason ?? '';

            $user_subscription_payment->trans_token = $request->trans_token ?? '';

            $user_subscription_payment->token = $request->tokens ?? 0.00;
            // Commission calculation & update the earnings to other user wallet


            $user_subscription_payment->admin_token = 0.00;

            $user_subscription_payment->user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_commission_in_per = Setting::get('subscription_admin_commission', 1)/100;

                $admin_token = $request->tokens * $admin_commission_in_per;

                $user_token = $request->tokens - $admin_token;

                $user_subscription_payment->admin_token = $admin_token;

                $user_subscription_payment->user_token = $user_token;

                $user_subscription_payment->admin_amount = $admin_token * Setting::get('token_amount');

                $user_subscription_payment->user_amount = $user_token * Setting::get('token_amount');

            } else {

                $admin_commission_in_per = Setting::get('subscription_admin_commission', 1)/100;

                $admin_amount = $total * $admin_commission_in_per;

                $user_amount = $total - $admin_amount;

                $user_subscription_payment->admin_amount = $admin_amount ?? 0.00;

                $user_subscription_payment->user_amount = $user_amount ?? 0.00;

            }

            $user_subscription_payment->status = $request->payment_status ?? PAID;

            if ($request->promo_code) {

                $promo_code_val = PromoCode::where('promo_code', $request->promo_code)->first();

                $user_subscription_payment->promo_code = $promo_code_val->promo_code;

                $user_subscription_payment->promo_code_amount = $promo_amount;

                $user_subscription_payment->is_promo_code_applied = PROMO_CODE_APPLIED;

            }

            $user_subscription_payment->save();
            
            // Add to post user wallet
            if($user_subscription_payment->status == PAID_STATUS) {

                if($total > 0) {
                    self::user_subscription_payments_wallet_update($request, $user_subscription, $user_subscription_payment);
                }

                $request->request->add(['user_id' => $user->id]);

                \App\Repositories\CommonRepository::follow_user($request);

            }
            
            $data = ['user_type' => SUBSCRIBED_USER, 'payment_id' => $request->payment_id ?? $user_subscription_payment->payment_id];

            $data['total_followers'] = \App\Models\Follower::where('user_id', $request->id)->where('status', YES)->count();

            $data['total_followings'] = \App\Models\Follower::where('follower_id', $request->id)->where('status', YES)->count();

            $response = ['success' => true, 'message' => 'paid', 'data' => $data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method user_subscriptions_payment_by_stripe()
     *
     * @uses deduct the subscription amount when logged in user subscribe the other user plans
     *
     * @created vithya
     * 
     * @updated vithya
     *
     * @param object $user_subscription, object $request
     *
     * @return object $user_subscription
     */

    public static function user_subscriptions_payment_by_stripe($request, $user_subscription) {

        try {

            // Check stripe configuration

            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];


            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method user_subscription_payment_wallet_update
     *
     * @uses post payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function user_subscription_payments_wallet_update($request, $user_subscription, $user_subscription_payment) {

        try {

            $to_user_inputs = [
                'id' => $user_subscription_payment->to_user_id,
                'payment_mode' => $user_subscription_payment->payment_mode,
                'received_from_user_id' => $user_subscription_payment->from_user_id,
                'total' => $user_subscription_payment->amount, 
                'user_pay_amount' => $user_subscription_payment->user_amount,
                'paid_amount' => $user_subscription_payment->user_amount,
                'user_amount' => $user_subscription_payment->user_amount,
                'admin_amount' => $user_subscription_payment->admin_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $user_subscription_payment->payment_id,
                'usage_type' => USAGE_TYPE_SUBSCRIPTION,
                'user_token' => $user_subscription_payment->user_token,
                'admin_token' => $user_subscription_payment->admin_token,
                'tokens' => $user_subscription_payment->user_token, 
            ];


            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method tips_payment_wallet_update
     *
     * @uses tip payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function tips_payment_wallet_update($request, $user_tip) {

        try {

            $to_user_inputs = [
                'id' => $request->to_user_id,
                'received_from_user_id' => $request->id,
                'total' => $user_tip->amount, 
                'tokens' => $user_tip->user_token, 
                'user_pay_amount' => $user_tip->user_amount,
                'paid_amount' => $user_tip->user_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $user_tip->payment_id,
                'user_amount' => $user_tip->user_amount,
                'admin_amount' => $user_tip->admin_amount,
                'usage_type' => USAGE_TYPE_TIP,
                'message' => $request->message,
                'user_token' => $user_tip->user_token,
                'admin_token' => $user_tip->admin_token,
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method chat_assets_payment_by_stripe()
     *
     * @uses 
     *
     * @created Arun
     * 
     * @updated Arun
     *
     * @param object $chat_message, object $request
     *
     * @return object $chat_message
     */

    public static function chat_assets_payment_by_stripe($request, $chat_message) {

        try {

            // Check stripe configuration

            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method chat_assets_payment_save()
     *
     * @uses used to save chat_assets payment details
     *
     * @created Arun
     * 
     * @updated Arun
     *
     * @param object $request
     *
     * @return object $chat_asset_payment
     */

    public static function chat_assets_payment_save($request, $chat_message) {

        try {

            $chat_asset_payment = new \App\Models\ChatAssetPayment;
            
            $chat_asset_payment->from_user_id = $chat_message->from_user_id;

            $chat_asset_payment->to_user_id = $chat_message->to_user_id;

            $chat_asset_payment->chat_message_id = $chat_message->chat_message_id;

            $chat_asset_payment->user_card_id = $request->user_card_id ?? 0;
            
            $chat_asset_payment->payment_id = $request->payment_id ?:generate_payment_id();

            $chat_asset_payment->paid_amount = $request->paid_amount ?? 0.00;

            $chat_asset_payment->currency = Setting::get('currency') ?? "$";

            $chat_asset_payment->payment_mode = $request->payment_mode ?? CARD;

            $chat_asset_payment->paid_date = date('Y-m-d H:i:s');

            $chat_asset_payment->status = $request->paid_status ?: PAID;

            $chat_asset_payment->token = $request->tokens ?? 0.00;
            // Commission calculation & update the earnings to other user wallet

            $chat_asset_payment->admin_token = 0.00;

            $chat_asset_payment->user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_commission_in_per = Setting::get('admin_commission', 1)/100;

                $admin_token = $request->tokens * $admin_commission_in_per;

                $user_token = $request->tokens - $admin_token;

                $chat_asset_payment->admin_token = $admin_token;

                $chat_asset_payment->user_token = $user_token;

                $chat_asset_payment->admin_amount = $admin_token * Setting::get('token_amount');

                $chat_asset_payment->user_amount = $user_token * Setting::get('token_amount');

            } else {

                $admin_commission = Setting::get('admin_commission', 1)/100;

                $admin_amount = $request->total * $admin_commission;

                $user_amount = $request->total - $admin_amount;

                $chat_asset_payment->admin_amount = $admin_amount ?? 0.00;

                $chat_asset_payment->user_amount = $user_amount ?? 0.00;

            }
            
            $chat_asset_payment->save();

            self::chat_payment_wallet_update($request, $chat_asset_payment);

            $response = ['success' => true, 'message' => 'paid', 'data' => $chat_asset_payment];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method video_call_payments_save()
     *
     * @uses used to save video call payment details
     *
     * @created Ganesh
     * 
     * @updated Ganesh
     *
     * @param object $post, object $request
     *
     * @return object $video_call_payment
     */

    public static function video_call_payments_save($request) {

        try {

            $video_call_payment = new \App\Models\VideoCallPayment;

            $video_call_payment->model_id = $request->model_id ?: 0;

            $video_call_payment->user_id = $request->id;

            $video_call_payment->user_card_id = $request->user_card_id ?: 0;

            $video_call_payment->video_call_request_id = $request->video_call_request_id ?? 0;

            $video_call_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $video_call_payment->payment_mode = $request->payment_mode ?? CARD;

            $video_call_payment->paid_amount = $total = $request->paid_amount ?? 0.00;
            
            $video_call_payment->token = $request->tokens ?? 0.00;
            // Commission calculation & update the earnings to other user wallet

            $video_call_payment->admin_token = 0.00;

            $video_call_payment->user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_commission_in_per = Setting::get('video_call_admin_commission', 1)/100;

                $admin_token = $request->tokens * $admin_commission_in_per;

                $user_token = $request->tokens - $admin_token;

                $video_call_payment->admin_token = $admin_token;

                $video_call_payment->user_token = $user_token;

                $video_call_payment->admin_amount = $admin_token * Setting::get('token_amount');

                $video_call_payment->user_amount = $user_token * Setting::get('token_amount');

            } else {

                $video_call_admin_commission_in_per = Setting::get('video_call_admin_commission', 1)/100;
            
                $video_call_admin_amount = $total * $video_call_admin_commission_in_per;

                $user_amount = $total - $video_call_admin_amount;

                $video_call_payment->admin_amount = $video_call_admin_amount ?? 0.00;
     
                $video_call_payment->user_amount = $user_amount ?? 0.00;

            }

            $video_call_payment->paid_date = date('Y-m-d H:i:s');

            $video_call_payment->status = $request->payment_status ?? PAID;

            $video_call_payment->save();

            // Add to video call model wallet

            if($video_call_payment->status == PAID) {

                self::video_call_payment_wallet_update($request, $video_call_payment);
            }
            
            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

     /**
     * @method video_call_payment_wallet_update
     *
     * @uses video payment amount will update to the model wallet
     *
     * @created Ganesh
     *
     * @updated Ganesh
     *
     * @param
     *
     * @return
     */

    public static function video_call_payment_wallet_update($request, $video_call_payment) {

        try {

            $to_user_inputs = [
                'id' => $request->model_id,
                'received_from_user_id' => $request->id,
                'total' => $video_call_payment->amount, 
                'user_pay_amount' => $video_call_payment->user_amount,
                'paid_amount' => $video_call_payment->user_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $video_call_payment->payment_id,
                'user_amount' => $video_call_payment->user_amount,
                'admin_amount' => $video_call_payment->admin_amount,
                'message' => $request->message,
                'user_token' => $video_call_payment->user_token,
                'admin_token' => $video_call_payment->admin_token,
                'tokens' => $video_call_payment->user_token, 
                'usage_type' => USAGE_TYPE_VIDEO_CALL
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }


        /**
     * @method video_call_payment_by_stripe()
     *
     * @uses pay for video call  using stripe
     *
     * @created Ganesh
     * 
     * @updated Ganesh
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function video_call_payment_by_stripe($request) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: 'USD';

            $total = intval(round($request->user_pay_amount * 100));

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,

                                'paid_status' => $stripe_payment_response->paid ?? true
                            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method tips_payment_by_stripe()
     *
     * @uses tips payment - card
     *
     * @created Bhawya
     * 
     * @updated Bhawya
     *
     * @param object $post, object $request
     *
     * @return object $post_paym
     */

    public static function live_videos_payment_by_stripe($request, $post) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: "USD";

            $total = intval(round($request->user_pay_amount * 100));

            // $charge_array = [
            //     'amount' => $total,
            //     'currency' => $currency_code,
            //     'customer' => $request->customer_id,
            // ];

            // $stripe_payment_response =  \Stripe\Charge::create($charge_array);

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,
                'paid_status' => $stripe_payment_response->paid ?? true
            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method live_videos_payment_save()
     *
     * @uses used to format the live videos response
     *
     * @created Bhawya 
     * 
     * @updated Bhawya
     *
     * @param object $live_videos, object $request
     *
     * @return object $live_videos
     */

    public static function live_videos_payment_save($request, $live_video_details) {

        try {

            $live_video_payment = new LiveVideoPayment;

            $live_video_payment->live_video_id = $request->live_video_id;

            $live_video_payment->user_id = $live_video_details->user_id;

            $live_video_payment->live_video_viewer_id = $request->id;

            $live_video_payment->payment_id = $request->payment_id;

            $live_video_payment->payment_mode = $request->payment_mode;

            $live_video_payment->amount = $total = $request->paid_amount ?? 0.00;

            $live_video_payment->live_video_amount = $live_video_details->amount ?? NO;

            $live_video_payment->currency = Setting::get('currency', '$');

            $live_video_payment->status = PAID_STATUS;

            $live_video_payment->token = $request->tokens ?? 0.00;
            // Commission calculation & update the earnings to other user wallet

            $live_video_payment->admin_token = 0.00;

            $live_video_payment->user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_commission_in_per = Setting::get('live_streaming_admin_commission', 1)/100;

                $admin_token = $request->tokens * $admin_commission_in_per;

                $user_token = $request->tokens - $admin_token;

                $live_video_payment->admin_token = $admin_token;

                $live_video_payment->user_token = $user_token;

                $live_video_payment->admin_amount = $admin_token * Setting::get('token_amount');

                $live_video_payment->user_amount = $user_token * Setting::get('token_amount');

            } else {

                $admin_commission = Setting::get('live_streaming_admin_commission')/100;

                $admin_amount = $request->paid_amount * $admin_commission;

                $user_amount = $request->paid_amount - $admin_amount;

                $live_video_payment->admin_amount = $admin_amount;

                $live_video_payment->user_amount = $user_amount;

            }

            $live_video_payment->save();

            // Add to post user wallet
            if($live_video_payment->status == PAID_STATUS) {

                if($total > 0) {
                    self::live_video_payments_wallet_update($request,$live_video_payment);
                }

            }


            $response_array = ['success' => true, 'message' => 'paid', 'data' => ['live_video_id' => $request->live_video_id, 'live_video_unique_id' => $live_video_details->live_video_unique_id]];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            $response_array = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response_array, 200);

        }
    
    }

    /**
     * @method user_subscription_payment_wallet_update
     *
     * @uses post payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function live_video_payments_wallet_update($request, $live_video_payment) {

        try {

            $to_user_inputs = [

                'id' => $live_video_payment->user_id,
                'payment_mode' => $live_video_payment->payment_mode,
                'received_from_user_id' => $live_video_payment->live_video_viewer_id,
                'total' => $live_video_payment->amount, 
                'user_pay_amount' => $live_video_payment->user_amount,
                'paid_amount' => $live_video_payment->user_amount,
                'user_amount' => $live_video_payment->user_amount,
                'admin_amount' => $live_video_payment->admin_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $live_video_payment->payment_id,
                'usage_type' => USAGE_TYPE_LIVE_VIDEO,
                'user_token' => $live_video_payment->user_token,
                'admin_token' => $live_video_payment->admin_token,
                'tokens' => $live_video_payment->user_token, 
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method tips_payment_wallet_update
     *
     * @uses tip payment amount will update to the post owner wallet
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     *
     * @return
     */

    public static function chat_payment_wallet_update($request, $chat_asset_payment) {


        try {

            $to_user_inputs = [
                'received_from_user_id' => $request->id,
                'id' => $chat_asset_payment->from_user_id,
                'total' => $chat_asset_payment->amount, 
                'user_pay_amount' => $chat_asset_payment->paid_amount,
                'paid_amount' => $chat_asset_payment->paid_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $chat_asset_payment->payment_id,
                'user_token' => $chat_asset_payment->user_token,
                'admin_token' => $chat_asset_payment->admin_token,
                'tokens' => $chat_asset_payment->user_token, 
                'usage_type' => USAGE_TYPE_CHAT,
            ];


            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

       /**
     * @method audio_call_payment_by_stripe()
     *
     * @uses pay for audio call  using stripe
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function audio_call_payment_by_stripe($request) {

        try {

            // Check stripe configuration
        
            $stripe_secret_key = Setting::get('stripe_secret_key');

            if(!$stripe_secret_key) {

                throw new Exception(api_error(107), 107);

            } 

            \Stripe\Stripe::setApiKey($stripe_secret_key);
           
            $currency_code = Setting::get('currency_code', 'USD') ?: 'USD';

            $total = intval(round($request->user_pay_amount * 100));

            $charge_array = [
                'amount' => $total,
                'currency' => $currency_code,
                'customer' => $request->customer_id,
                "payment_method" => $request->card_token,
                'off_session' => true,
                'confirm' => true,
            ];

            $stripe_payment_response = \Stripe\PaymentIntent::create($charge_array);

            $payment_data = [
                                'payment_id' => $stripe_payment_response->id ?? 'CARD-'.rand(),
                                'paid_amount' => $stripe_payment_response->amount/100 ?? $total,

                                'paid_status' => $stripe_payment_response->paid ?? true
                            ];

            $response = ['success' => true, 'message' => 'done', 'data' => $payment_data];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method audio_call_payments_save()
     *
     * @uses used to save video call payment details
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $post, object $request
     *
     * @return object $audio_call_payment
     */

    public static function audio_call_payments_save($request) {

        try {

            $audio_call_payment = new \App\Models\AudioCallPayment;

            $audio_call_payment->model_id = $request->model_id ?: 0;

            $audio_call_payment->user_id = $request->id;

            $audio_call_payment->user_card_id = $request->user_card_id ?: 0;

            $audio_call_payment->audio_call_request_id = $request->audio_call_request_id ?? 0;

            $audio_call_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $audio_call_payment->payment_mode = $request->payment_mode ?? CARD;

            $audio_call_payment->paid_amount = $total = $request->paid_amount ?? 0.00;

            $audio_call_payment->token = $request->tokens ?? 0.00;
            // Commission calculation & update the earnings to other user wallet

            $audio_call_payment->admin_token = 0.00;

            $audio_call_payment->user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_commission_in_per = Setting::get('audio_call_admin_commission', 1)/100;

                $admin_token = $request->tokens * $admin_commission_in_per;

                $user_token = $request->tokens - $admin_token;

                $audio_call_payment->admin_token = $admin_token;

                $audio_call_payment->user_token = $user_token;

                $audio_call_payment->admin_amount = $admin_token * Setting::get('token_amount');

                $audio_call_payment->user_amount = $user_token * Setting::get('token_amount');

            } else {

                $audio_call_admin_commission_in_per = Setting::get('audio_call_admin_commission', 1)/100;
            
                $audio_call_admin_amount = $total * $audio_call_admin_commission_in_per;

                $user_amount = $total - $audio_call_admin_amount;

                $audio_call_payment->admin_amount = $audio_call_admin_amount ?? 0.00;
     
                $audio_call_payment->user_amount = $user_amount ?? 0.00;

            }

            $audio_call_payment->paid_date = date('Y-m-d H:i:s');

            $audio_call_payment->status = $request->payment_status ?? PAID;

            $audio_call_payment->save();

            // Add to video call model wallet

            if($audio_call_payment->status == PAID) {

                self::audio_call_payment_wallet_update($request, $audio_call_payment);
            }
            
            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method audio_call_payment_wallet_update
     *
     * @uses audio payment amount will update to the model wallet
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return
     */

    public static function audio_call_payment_wallet_update($request, $audio_call_payment) {

        try {

            $to_user_inputs = [
                'id' => $request->model_id,
                'received_from_user_id' => $request->id,
                'total' => $audio_call_payment->amount, 
                'user_pay_amount' => $audio_call_payment->user_amount,
                'paid_amount' => $audio_call_payment->user_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $audio_call_payment->payment_id,
                'user_amount' => $audio_call_payment->user_amount,
                'admin_amount' => $audio_call_payment->admin_amount,
                'message' => $request->message,
                'user_token' => $audio_call_payment->user_token,
                'admin_token' => $audio_call_payment->admin_token,
                'tokens' => $audio_call_payment->user_token,
                'usage_type' => USAGE_TYPE_AUDIO_CALL 
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method order_product_save()
     *
     * @uses used to save Order Product details
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $post, object $request
     *
     * @return object $order_product
     */

    public static function order_product_save($request, $order, $cart) {

        try {

            $order_product = OrderProduct::find($request->order_id) ?? new OrderProduct;

            $order_product->user_id = $request->id;

            $order_product->order_id = $order->id ?? 0;

            $order_product->user_product_id = $cart->user_product_id ?? 0;

            $order_product->quantity = $cart->quantity ?? 0;

            $order_product->per_quantity_price = $cart->per_quantity_price ?? 0.00;

            $order_product->sub_total = $cart->sub_total ?? 0.00;

            $order_product->tax_price = $cart->tax_price ?? 0.00;

            $order_product->delivery_price = $cart->delivery_price ?? 0.00;

            $order_product->total = $cart->total ?? 0.00;

            $order_product->save();
            
            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'order_product' => $order_product]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method order_payments_save()
     *
     * @uses used to save Order payment details
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $post, object $request
     *
     * @return object $order_payment
     */

    public static function order_payments_save($request, $order) {

        try {

            $order_payment = new OrderPayment;

            $order_payment->user_id = $request->id;

            $order_payment->order_id = $order->id ?: 0;

            $order_payment->payment_id = $request->payment_id ?? "NO-".rand();

            $order_payment->payment_mode = $request->payment_mode ?? CARD;

            $order_payment->delivery_price = $order->delivery_price ?? 0.00;

            $order_payment->sub_total = $order->sub_total ?? 0.00;

            $order_payment->tax_price = $order->tax_price ?? 0.00;

            $order_payment->total = $total = $order->total ?? 0.00;

            $order_payment->paid_date = date('Y-m-d H:i:s');

            $order_payment->status = $request->payment_status ?? PAID;

            $order_payment->token = $request->tokens ?? 0.00;
            // Commission calculation & update the earnings to other user wallet

            $order_payment->admin_token = 0.00;

            $order_payment->user_token = 0.00;

            $admin_commission = Setting::get('admin_commission', 1)/100;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_token = $request->tokens * $admin_commission;

                $user_token = $request->tokens - $admin_token;

                $order_payment->admin_token = $admin_token;

                $order_payment->user_token = $user_token;

            }

            $order_payment->save();

            // Add to Order model wallet

            if($order_payment->status == PAID) {

                $carts = Cart::whereIn('carts.id',$request->cart_ids)->BaseResponse()->get();

                foreach ($carts as $key => $cart) {
                    
                    self::order_payment_wallet_update($request, $order_payment, $cart);
                }
                
            }
            
            $response = ['success' => true, 'message' => 'paid', 'data' => [ 'payment_id' => $request->payment_id]];

            return response()->json($response, 200);

        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }
    
    }

    /**
     * @method order_payment_wallet_update
     *
     * @uses Order payment amount will update to the model wallet
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return
     */

    public static function order_payment_wallet_update($request, $order_payment, $cart) {

        try {

            // Commission calculation

            $total = $cart->actual_total ?? 0.00;

            $admin_token = $user_token = 0.00;

            if(Setting::get('is_only_wallet_payment')) {

                $admin_commission_in_per = Setting::get('admin_commission', 1)/100;

                $admin_token = $total * $admin_commission_in_per;

                $user_token = $total - $admin_token;

                $admin_amount = $admin_token * Setting::get('token_amount');

                $user_amount = $user_token * Setting::get('token_amount');

            } else {

                $admin_commission_in_per = Setting::get('admin_commission', 1)/100;
                
                $admin_amount = $total * $admin_commission_in_per;

                $user_amount = $total - $admin_amount;

            }

            $to_user_inputs = [
                'id' => $cart->model_id,
                'received_from_user_id' => $request->id,
                'total' => $cart->actual_total, 
                'user_pay_amount' => $user_amount,
                'paid_amount' => $user_amount,
                'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                'payment_id' => $order_payment->payment_id,
                'user_amount' => $user_amount,
                'admin_amount' => $admin_amount,
                'message' => $request->message,
                'user_token' => $user_token,
                'admin_token' => $admin_token,
                'tokens' => $user_token,
                'usage_type' => USAGE_TYPE_ORDER,
            ];

            $to_user_request = new \Illuminate\Http\Request();

            $to_user_request->replace($to_user_inputs);

            $to_user_payment_response = self::user_wallets_payment_save($to_user_request)->getData();

            if($to_user_payment_response->success) {

                DB::commit();

                return $to_user_payment_response;

            } else {

                throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
            }
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }

    /**
     * @method product_quantity_update
     *
     * @uses Update user product quantity after order
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return
     */

    public static function order_product_quantity_update($order) {

        try {

            $order_products = OrderProduct::where('order_id', $order->id)->get();

            foreach($order_products as $order_product){

                $user_product = UserProduct::find($order_product->user_product_id);

                if($user_product) { 

                    $quantity = $user_product->quantity - $order_product->quantity;

                    $user_product->quantity = $quantity;
                    
                    $user_product->is_outofstock = intval($quantity) > 0 ? IN_STOCK : OUT_OF_STOCK;

                    $user_product->save();               
                }
                
            }

            $response = ['success' => true, 'message' => tr('product_quantity_updated'), 'data' => [ 'order_products' => $order_products]];

            return response()->json($response, 200);
        
        } catch(Exception $e) {

            $response = ['success' => false, 'error' => $e->getMessage(), 'error_code' => $e->getCode()];

            return response()->json($response, 200);

        }

    }


}