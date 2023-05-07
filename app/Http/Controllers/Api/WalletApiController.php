<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;

use DB, Log, Hash, Validator, Exception, Setting, Helper;

use App\Models\User;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\WalletRepository as WalletRepo;

class WalletApiController extends Controller
{
    protected $loginUser, $skip, $take;

	public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));

        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

    }

    /**
     * @method user_wallets_index()
     *
     * @uses wallet details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_index(Request $request) {

        try {

            $user_wallet = \App\Models\UserWallet::firstWhere('user_id', $request->id);

            if(!$user_wallet) {

                $user_wallet = \App\Models\UserWallet::create(['user_id' => $request->id, 'total' => 0.00, 'used' => 0.00, 'remaining' => 0.00]);

            }

            $data['user_wallet'] = $user_wallet;

            $data['user_withdrawals_min_amount'] = Setting::get('user_withdrawals_min_amount', 10);

            $data['user_withdrawals_min_amount_formatted'] = formatted_amount(Setting::get('user_withdrawals_min_amount', 10));

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method user_wallets_history()
     *
     * @uses wallet details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history(Request $request) {

        try {

            $base_query = \App\Models\UserWalletPayment::CommonResponse()->where('user_id', $request->id);

            $history = $base_query->orderBy('created_at', 'desc')->skip($this->skip)->take($this->take)->get();

            $history = WalletRepo::wallets_list_response($history, $request);

            $data['history'] = $history ?? [];

            $data['total'] = \App\Models\UserWalletPayment::where('user_id', $request->id)->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

	/**
     * @method user_wallets_add_money_by_stripe()
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

    public function user_wallets_add_money_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['amount' => 'required|numeric|min:1'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $amount = $request->amount;

            if(Setting::get('is_only_wallet_payment')) {

                $amount = $request->amount * Setting::get('token_amount');

                $request->request->add([
                    'tokens' => $request->amount,
                ]);

            }

            $request->request->add(['payment_mode' => CARD]);

            $request->request->add([
                'total' => $amount,
                'user_pay_amount' => $amount,
                'paid_amount' => $amount,
            ]);

            if($request->amount > 0) {

                // Check the user have the cards

                $user_card = \App\Models\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120);

                }

                $request->request->add(['customer_id' => $user_card->customer_id,'card_token' => $user_card->card_token]);

                $card_payment_response = PaymentRepo::user_wallets_payment_by_stripe($request)->getData();

                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);

                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add([
                    'paid_amount' => $card_payment_data->paid_amount, 
                    'payment_id' => $card_payment_data->payment_id, 
                    'paid_status' => USER_WALLET_PAYMENT_PAID,
                    'usage_type' => USAGE_TYPE_RECHARGE
                ]);

            }

            $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($payment_response->success) {

                DB::commit();

                return $this->sendResponse(api_success(117), 117, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);

            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method user_wallets_add_money_by_paypal()
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

    public function user_wallets_add_money_by_paypal(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'amount' => 'required|numeric|min:1',
                'payment_id' => 'required'
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $request->request->add(['payment_mode' => PAYPAL]);

            $request->request->add([
                'total' => $request->amount,
                'user_pay_amount' => $request->amount,
                'paid_amount' => $request->amount,
                'paid_status' => USER_WALLET_PAYMENT_PAID
            ]);

            $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($payment_response->success) {

                DB::commit();

                return $this->sendResponse(api_success(117), 117, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);

            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

	/**
     * @method user_wallets_add_money_by_bank_account()
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

    public function user_wallets_add_money_by_bank_account(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
            	// 'bank_statement_picture' => 'required|mimes:jpg,png,jpeg',
                'payment_id' => 'required',
            	'amount' => 'required|numeric|min:1',
                'user_billing_account_id' => 'nullable|exists:user_billing_accounts,id'
            	];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            if(!$request->user_billing_account_id) {

                $user_billing_account = \App\Models\UserBillingAccount::where('user_id', $request->id)->firstWhere('is_default', YES);

                $request->request->add(['user_billing_account_id' => $user_billing_account->user_billing_account_id ?? 0]);
            }

            $request->request->add([
                'payment_mode' => BANK_TRANSFER,
                'total' => $request->amount,
                'user_pay_amount' => $request->amount,
                'paid_amount' => $request->amount,
                'paid_status' => USER_WALLET_PAYMENT_WAITING
            ]);

            $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($payment_response->success) {

                DB::commit();

                return $this->sendResponse(api_success(133), 133, $payment_response->data);

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);

            }


        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

	}

    /**
     * @method user_wallets_history_for_add()
     *
     * @uses wallet details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history_for_add(Request $request) {

        try {

            $base_query = $total_query = \App\Models\UserWalletPayment::CommonResponse()->where('user_id', $request->id)->where('payment_type', WALLET_PAYMENT_TYPE_ADD);

            $history = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $data['history'] = $history ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_wallets_history_for_sent()
     *
     * @uses wallet details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history_for_sent(Request $request) {

        try {

            $base_query = $total_query = \App\Models\UserWalletPayment::CommonResponse()->where('user_id', $request->id)->where('payment_type', WALLET_PAYMENT_TYPE_PAID);

            $history = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $history = WalletRepo::wallets_list_response($history, $request);

            $data['history'] = $history ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_wallets_history_for_received()
     *
     * @uses wallet details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_history_for_received(Request $request) {

        try {

            $base_query = $total_query = \App\Models\UserWalletPayment::CommonResponse()->where('user_id', $request->id)->where('payment_type', WALLET_PAYMENT_TYPE_CREDIT);

            $history = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $history = WalletRepo::wallets_list_response($history, $request);

            $data['history'] = $history ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_wallets_payment_view()
     *
     * @uses get the selected withdraw request
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_payment_view(Request $request) {

        try {

            $rules = [ 'user_wallet_payment_unique_id' => 'required|exists:user_wallet_payments,unique_id'];

            Helper::custom_validator($request->all(), $rules);

            $wallet_payment = \App\Models\UserWalletPayment::CommonResponse()->firstWhere('user_wallet_payments.unique_id', $request->user_wallet_payment_unique_id);

            $wallet_payment = \App\UserWalletPayment::CommonResponse()->firstWhere('user_wallet_payments.unique_id', $request->user_wallet_payment_unique_id);

            $data['wallet_payment'] = $wallet_payment;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /**
     * @method user_wallets_send_money()
     *
     * @uses send money to other user
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_wallets_send_money(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                        'to_user_id' => 'required|exists:users,id',
                        'amount' => 'required|numeric|min:1'
                    ];

            $custom_errors = ['to_user_id' => api_error(143)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            // Validation end

            // Check the to user is valid for this transaction

            $to_user = \App\Models\User::Approved()->firstWhere('id', $request->to_user_id);

            if(!$to_user) {

                throw new Exception(api_error(130), 130);

            }

            // Check the user has enough balance

            $user_wallet = \App\Models\UserWallet::firstWhere('user_id', $request->id);

            $remaining = $user_wallet->remaining ?? 0;

            if($remaining < $request->amount) {
                throw new Exception(api_error(131), 131);
            }

            $request->request->add([
                'payment_mode' => PAYMENT_MODE_WALLET,
                'total' => $request->amount,
                'user_pay_amount' => $request->amount,
                'paid_amount' => $request->amount,
                'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'DD-'.rand()
            ]);

            $payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();

            if($payment_response->success) {

                // Update the to user

                $to_user_inputs = [
                    'id' => $request->to_user_id,
                    'received_from_user_id' => $request->id,
                    'total' => $request->amount,
                    'user_pay_amount' => $request->amount,
                    'paid_amount' => $request->amount,
                    'payment_type' => WALLET_PAYMENT_TYPE_CREDIT,
                    'amount_type' => WALLET_AMOUNT_TYPE_ADD,
                    'payment_id' => 'CD-'.rand(),
                    'usage_type' => USAGE_TYPE_SEND_MONEY
                ];

                $to_user_request = new \Illuminate\Http\Request();

                $to_user_request->replace($to_user_inputs);

                $to_user_payment_response = PaymentRepo::user_wallets_payment_save($to_user_request)->getData();

                if($to_user_payment_response->success) {


                    $wallet_message = tr('wallet_money_send_message');

                    $wallet_message = str_replace("<%request_amount%>", formatted_amount($request->amount?? '0.00'),$wallet_message);

                    $wallet_message = str_replace("<%user_name%>", $user_wallet->user->name??'',$wallet_message);

                    $email_data['subject'] = Setting::get('site_name');

                    $email_data['page'] = "emails.users.wallet_send_money";

                    $email_data['data'] = $user_wallet;

                    $email_data['amount'] = formatted_amount($request->amount ?? '0.00');

                    $email_data['email'] = $to_user->email ?? '';

                    $email_data['name'] = $to_user->name ?? '';

                    $email_data['message'] = $wallet_message;

                    // $this->dispatch(new \App\Jobs\SendEmailJob($email_data));

                    DB::commit();

                    return $this->sendResponse(api_success(138), 138, $payment_response->data);

                } else {
                    throw new Exception($to_user_payment_response->error, $to_user_payment_response->error_code);
                }

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);

            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method admin_account_details()
     *
     * @uses get admin account details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function admin_account_details(Request $request) {

        try {

            $admin_account_keys = ['account_number', 'account_holder_name', 'ifsc_code', 'swift_code', 'bank_name', 'branch_name'];

            $data = \App\Models\Settings::whereIn('key', $admin_account_keys)->get();

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

     /**
     * @method user_withdrawals_index()
     *
     * @uses wallet details
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_withdrawals_index(Request $request) {

        try {

           $base_query = $total_query = \App\Models\UserWithdrawal::where('user_id', $request->id);

            $history = $base_query->skip($this->skip)->take($this->take)->orderBy('created_at', 'desc')->get();

            $history = $history->map(function ($value, $key) use ($request) {

                        $value->cancel_btn_status = in_array($value->status, [WITHDRAW_INITIATED, WITHDRAW_ONHOLD]) ? YES : NO;

                        $value->created = common_date($value->created_at, $this->timezone, 'd M Y');

                        return $value;
                    });

            $data['history'] = $history ?? [];

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_withdrawals_view()
     *
     * @uses get the selected withdraw request
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_withdrawals_view(Request $request) {

        try {

            $rules = [ 'user_withdrawal_id' => 'required|exists:user_withdrawals,id'];

            $custom_errors = ['user_withdrawal_id.exists' => api_error(257)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $user_withdrawal = \App\Models\UserWithdrawal::find($request->user_withdrawal_id);

            $data['user_withdrawal'] = $user_withdrawal;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_withdrawals_search()
     *
     * @uses withdrawls search
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param request id
     *
     * @return JSON Response
     */
    public function user_withdrawals_search(Request $request) {

        try {

            // Validation start

            $rules = ['search_key' => 'required|min:2'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $search_key = $request->search_key;

            $base_query = $total_query = \App\Models\UserWithdrawal::where('user_withdrawals.user_id', $request->id)
                            ->where(function($query) use($search_key) {

                                $query->orWhere('payment_id','LIKE','%'.$search_key.'%');

                                $query->orWhere('requested_amount','LIKE','%'.$search_key.'%');
                                $query->orWhere('paid_amount','LIKE','%'.$search_key.'%');

                            });

            $user_withdrawals = $base_query->skip($this->skip)->take($this->take)->get();

            $data['user_withdrawals'] = $user_withdrawals;

            $data['total'] = $total_query->count();

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_withdrawals_send_request()
     *
     * @uses send request to admin
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_withdrawals_send_request(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'requested_amount' => 'nullable|numeric|min:1',
                'user_billing_account_id' => 'required|exists:user_billing_accounts,id,user_id,'.$request->id
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            // Check the user has enough balance

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            $error_code = 142;

            // if(Setting::get('is_referral_enabled')) {

            //     $remaining = $remaining - $user_wallet->referral_amount;

            //     $error_code = 184;

            // }

            if($remaining <= 0) {

                throw new Exception(api_error($error_code), $error_code);

            }

            $requested_amount = $request->requested_amount ?: $user_wallet->remaining;

            $user_withdrawals_min_amount = Setting::get('user_withdrawals_min_amount', 10);

            if($requested_amount < $user_withdrawals_min_amount) {

                $min_amount_formatted = formatted_amount($user_withdrawals_min_amount);

                throw new Exception(api_error(143, $min_amount_formatted), 143);
            }

            $remaining = $user_wallet->remaining ?? 0;

            if($remaining < $request->requested_amount) {

                throw new Exception(api_error(170), 170);
            }

            // Create withdraw requests

            $user_withdrawal = new \App\Models\UserWithdrawal;

            $user_withdrawal->user_id = $request->id;

            $user_withdrawal->payment_id = "NO";

            $user_withdrawal->user_billing_account_id = $request->user_billing_account_id;

            $user_withdrawal->payment_mode = PAYMENT_OFFLINE;

            $amount = $request->requested_amount ?? 0;

            if(Setting::get('is_only_wallet_payment')) {

                $user_withdrawal->requested_token = $amount;

                $user_withdrawal->requested_amount = $user_withdrawal->requested_token * Setting::get('token_amount');

            } else {

                $user_withdrawal->requested_amount = $amount;

            }

            $user_withdrawal->status = USER_WALLET_PAYMENT_INITIALIZE;

            $user_withdrawal->user_wallet_payment_id = 0;

            $user_withdrawal->save();

            // Update the wallet amount

            PaymentRepo::user_wallet_update_withdraw_send($request->requested_amount, $request->id);

            // Create wallet payment history

            $withdraw_request_inputs = [
                'id' => $request->id,
                'total' => $request->requested_amount * Setting::get('token_amount'),
                'user_pay_amount' => $request->requested_amount * Setting::get('token_amount'),
                'paid_amount' => $request->requested_amount * Setting::get('token_amount'),
                'payment_type' => WALLET_PAYMENT_TYPE_WITHDRAWAL,
                'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                'payment_id' => 'WDP-'.rand(),
                'payment_mode'=>PAYMENT_MODE_WALLET,
                'usage_type' => USAGE_TYPE_WITHDRAW,
                'tokens' => $request->requested_amount,
            ];

            $withdraw_request = new \Illuminate\Http\Request();

            $withdraw_request->replace($withdraw_request_inputs);

            $withdraw_request_response = PaymentRepo::user_wallets_payment_save($withdraw_request)->getData();

            if($withdraw_request_response->success) {

                $user_wallet_payment = $withdraw_request_response->data;

                $user_withdrawal->user_wallet_payment_id = $user_wallet_payment->user_wallet_payment_id;

                $user_withdrawal->save();

                DB::commit();

                return $this->sendResponse(api_success(248), 248, $user_withdrawal);

            } else {

                throw new Exception($withdraw_request_response->error, $withdraw_request_response->error_code);
            }

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_withdrawals_cancel_request()
     *
     * @uses cancel request to admin
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_withdrawals_cancel_request(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'user_withdrawal_id' => 'required|numeric|exists:user_withdrawals,id',
                    'cancel_reason' => 'nullable'
                ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            // Check the user has enough balance

            $user_withdrawal = \App\Models\UserWithdrawal::where('user_id', $request->id)->where('user_withdrawals.id', $request->user_withdrawal_id)->first();

            if(!$user_withdrawal) {
                throw new Exception(api_error(140), 140);
            }


            // Check the cancel eligibility

            if(in_array($user_withdrawal->status, [WITHDRAW_PAID, WITHDRAW_DECLINED, WITHDRAW_CANCELLED])) {
                throw new Exception(api_error(141), 141);
            }

            $user_withdrawal->status = WITHDRAW_CANCELLED;

            $user_withdrawal->cancel_reason = $request->cancel_reason ?: "";

            $user_withdrawal->save();

            // Update the wallet amount

            PaymentRepo::user_wallet_update_withdraw_cancel($user_withdrawal->requested_amount, $request->id);

            // Update the wallet history

            $user_wallet_payment = \App\Models\UserWalletPayment::where('id', $user_withdrawal->user_wallet_payment_id)->first();

            if($user_wallet_payment) {

                $user_wallet_payment->status = USER_WALLET_PAYMENT_CANCELLED;

                $user_wallet_payment->save();
            }

            DB::commit();

            $data = [];

            return $this->sendResponse(api_success(249), 249, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method user_withdrawals_check()
     *
     * @uses cancel request to admin
     *
     * @created Bhawya N
     *
     * @updated Bhawya N
     *
     * @param object $request
     *
     * @return json with boolean output
     */

    public function user_withdrawals_check(Request $request) {

        try {

            // Check the user has enough balance

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            if(!$user_wallet) {
                $user_wallet = \App\Models\UserWallet::create(['user_id' => $request->id, 'total' => 0.00, 'used' => 0.00, 'remaining' => 0.00]);

            }

            $user_withdrawals_min_amount = Setting::get('user_withdrawals_min_amount', 10);

            if($user_wallet->remaining < $user_withdrawals_min_amount) {

                $min_amount_formatted = formatted_amount($user_withdrawals_min_amount);

                throw new Exception(api_error(143, $min_amount_formatted), 143);
            }

            return $this->sendResponse($message = "", $code = "", $user_wallet);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


}
