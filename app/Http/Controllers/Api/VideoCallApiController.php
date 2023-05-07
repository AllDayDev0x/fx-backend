<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use App\Models\User, App\Card;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Models\VideoCallRequest, App\Models\VideoCallPayment;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

use App\Repositories\VideoCallRepository as VideoCallRepo;

use App\Repositories\WalletRepository as WalletRepo;

class VideoCallApiController extends Controller
{
    //
    protected $loginUser;

    protected $skip, $take, $timezone, $currency, $device_type;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->currency = Setting::get('currency', '$');

        $this->loginUser = User::CommonResponse()->find($request->id);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

        $request->request->add(['timezone' => $this->timezone]);

        $this->device_type = $this->loginUser->device_type ?? DEVICE_WEB;

    }

    /** 
     * @method video_call_requests_view()
     *
     * @uses to list the video call requests
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

    public function video_call_requests_view(Request $request) {

       try {

            $rules = ['video_call_request_unique_id' => 'required|exists:video_call_requests,unique_id'];
           
            Helper::custom_validator($request->all(), $rules);
            
            $video_call_request = \App\Models\VideoCallRequest::where('video_call_requests.unique_id', $request->video_call_request_unique_id)->first();

            $video_call_request = VideoCallRepo::video_call_requests_single_response($video_call_request, $request,$this->timezone);

            $data['video_call_request'] = $video_call_request ?? emptyObject();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method video_call_requests()
     *
     * @uses to list the video call requests
     *
     * @created Ganesh
     *
     * @updated Ganesh, Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function video_call_requests(Request $request) {

       try {

            $block_user_ids = blocked_users($request->id);

            $base_query = $total_query = \App\Models\VideoCallRequest::where('user_id',$request->id)
                ->where('status',VIDEO_CALL_REQUEST_SENT)
                ->whereNotIn('video_call_requests.user_id', $block_user_ids)
                ->orderBy('video_call_requests.created_at', 'desc');

            $data['total'] = $total_query->count() ?? 0;

            $video_call_requests = $base_query->skip($this->skip)->take($this->take)->get();

            $video_call_requests = VideoCallRepo::video_call_requests_list_response($video_call_requests, $request,$this->timezone);

            $data['video_call_requests'] = $video_call_requests ?? emptyObject();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method user_video_call_requests()
     *
     * @uses to list the video call requests
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

    public function user_video_call_requests(Request $request) {

       try {

            $base_query = $total_query = \App\Models\VideoCallRequest::where('user_id',$request->id)
                ->where('status',VIDEO_CALL_REQUEST_SENT)
                ->orderBy('video_call_requests.created_at','desc');

            $data['total'] = $total_query->count() ?? 0;

            $video_call_requests = $base_query->skip($this->skip)->take($this->take)->get();

            $video_call_requests = VideoCallRepo::video_call_requests_list_response($video_call_requests, $request,$this->timezone);

            $data['video_call_requests'] = $video_call_requests ?? emptyObject();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method user_video_call_requests()
     *
     * @uses to list the video call requests
     *
     * @created Ganesh
     *
     * @updated Ganesh, Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function user_video_call_history(Request $request) {

       try {

            $block_user_ids = blocked_users($request->id);

            $base_query = $total_query = \App\Models\VideoCallRequest::where('user_id', $request->id)->orWhere('model_id', $request->id)
                ->whereNotIn('video_call_requests.user_id', $block_user_ids)
                ->orderBy('video_call_requests.created_at','desc');

            $data['total'] = $total_query->count() ?? 0;

            $video_call_requests = $base_query->skip($this->skip)->take($this->take)->get();

            $video_call_requests = VideoCallRepo::video_call_requests_list_response($video_call_requests, $request,$this->timezone);

            $data['video_call_requests'] = $video_call_requests ?? emptyObject();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method model_video_call_requests()
     *
     * @uses to list the video call requests
     *
     * @created Ganesh
     *
     * @updated Ganesh, Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function model_video_call_requests(Request $request) {

       try {
            
            $block_user_ids = blocked_users($request->id);

            $base_query = $total_query = VideoCallRequest::where('model_id',$request->id)
                ->where('status',VIDEO_CALL_REQUEST_SENT)
                ->whereNotIn('video_call_requests.user_id', $block_user_ids)
                ->orderBy('video_call_requests.created_at', 'desc');

            $data['total'] = $total_query->count() ?? 0;

            $video_call_requests = $base_query->skip($this->skip)->take($this->take)->get();

            $video_call_requests = VideoCallRepo::video_call_requests_list_response($video_call_requests, $request,$this->timezone);

            $data['video_call_requests'] = $video_call_requests ?? emptyObject();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method model_video_call_history()
     *
     * @uses to list the video call requests
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

    public function model_video_call_history(Request $request) {

       try {
            
            $base_query = $total_query = \App\Models\VideoCallRequest::where('model_id', $request->id)->orderBy('video_call_requests.created_at', 'desc');

            $data['total'] = $total_query->count() ?? 0;

            $video_call_requests = $base_query->skip($this->skip)->take($this->take)->get();

            $video_call_requests = VideoCallRepo::video_call_requests_list_response($video_call_requests, $request,$this->timezone);

            $data['video_call_requests'] = $video_call_requests ?? emptyObject();

            return $this->sendResponse($message = '' , $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }
    /** 
     * @method video_call_requests_save()
     *
     * @uses to save the video call requests
     *
     * @created Ganesh
     *
     * @updated Ganesh, Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function video_call_requests_save(Request $request) {

        $now = convertTimeToUSERzone(now(), $this->timezone, "Y-m-d H:i");

        try {

            $rules = [
                'video_call_request_id' => 'nullable|exists:video_call_requests,id,user_id,'.$request->id,
                'model_id'=>'required|exists:users,id',
                'start_time' => 'required|after_or_equal:'.$now,
            ];

            $custom_errors = [ 'start_time.after_or_equal' => api_error(264, $now) ];
           
            Helper::custom_validator($request->all(),$rules, $custom_errors);

            DB::begintransaction();

            // $today = Carbon::now()->format('Y-m-d H:i:s');
            
            // if(strtotime($request->start_time) < strtotime($today)){

            //     throw new Exception(api_error(206), 206);
            // }

            if($request->model_id == $request->id){

                throw new Exception(api_error(207), 207);
            }

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first() ?? new \App\Models\UserWallet;

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }            

            if($remaining < Setting::get('min_token_call_charge')) {
                throw new Exception(api_error(147), 147);    
            }

            $video_call_request = VideoCallRequest::find($request->video_call_request_id) ?? new VideoCallRequest();

            $video_call_request->user_id = $request->id;

            $video_call_request->model_id = $request->model_id;

            $video_call_request->start_time = common_server_date($request->start_time, $this->timezone, 'Y-m-d H:i:s');

            $video_call_request->call_status =  VIDEO_CALL_REQUEST_SENT;

            if($video_call_request->save()){

                WalletRepo::user_wallet_update_onhold(Setting::get('min_token_call_charge'), $request->id)->getData();

                DB::commit();

                $job_data['video_call_request'] = $video_call_request;

                $job_data['status'] = VIDEO_CALL_REQUEST_SENT;
    
                $job_data['timezone'] = $this->timezone;
    
                $this->dispatch(new \App\Jobs\VideoCallRequestSentJob($job_data));

                $code = $request->video_call_request_id ? 207 : 206;

                $data['video_call_request'] = $video_call_request;

                return $this->sendResponse(api_success($code), $code, $data);

            }

            throw new Exception(api_error(205),205);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method video_call_requests_accept()
     *
     * @uses to accept the  video call requests
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

    public function video_call_requests_accept(Request $request) {

        try {

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id,model_id,'.$request->id,
            ];
           
            Helper::custom_validator($request->all(),$rules);

            if(Setting::get('is_agora_configured')) {

                if($request->device_type != DEVICE_WEB) {

                    $rules = [
                        'virtual_id' => 'required',
                    ];

                    Helper::custom_validator($request->all(), $rules, $custom_errors = []);

                }

                $agora_app_id = Setting::get('agora_app_id');

                $appCertificate = Setting::get('agora_certificate_id');

                if(!$agora_app_id || !$appCertificate) {

                    throw new Exception(api_error(204), 204);
                    
                }


            }

            DB::begintransaction();

            $video_call_request = VideoCallRequest::where('id',$request->video_call_request_id)->first();
            
            if(!$video_call_request){

                throw new Exception(api_error(208),208);
            }

            if($video_call_request->call_status == VIDEO_CALL_REQUEST_ACCEPTED){

                throw new Exception(api_error(209),209);
            }

            $video_call_request->call_status = VIDEO_CALL_REQUEST_ACCEPTED;

            $video_call_request->virtual_id = $request->virtual_id ?? \Str::random(10);

            $token = '';

            if(Setting::get('is_agora_configured')) { 

                $uid = 0;

                $role = \RtcTokenBuilder::RoleAttendee;

                $expireTimeInSeconds = 3600;

                $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();

                $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

                $token = \RtcTokenBuilder::buildTokenWithUid($agora_app_id, $appCertificate, $video_call_request->virtual_id, $uid, $role, $privilegeExpiredTs);

            }

            $video_call_request->agora_token = $token ?? '';

            $video_call_request->save();

            DB::commit();

            $job_data['video_call_request'] = $video_call_request;

            $job_data['status'] = VIDEO_CALL_REQUEST_ACCEPTED;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\VideoCallRequestJob($job_data));

            return $this->sendResponse(api_success(208),208, $data=[]);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method video_call_requests_reject()
     *
     * @uses to accept the  video call requests
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

    public function video_call_requests_reject(Request $request) {

        try {

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id,model_id,'.$request->id,
            ];
           
            Helper::custom_validator($request->all(),$rules);

            $video_call_request = VideoCallRequest::where('id',$request->video_call_request_id)->first();
            
            if(!$video_call_request){

                throw new Exception(api_error(208),208);
            }

            if($video_call_request->call_status == VIDEO_CALL_REQUEST_REJECTED){

                throw new Exception(api_error(210),210);
            }

            $video_call_request->call_status = VIDEO_CALL_REQUEST_REJECTED;

            $video_call_request->save();

            WalletRepo::user_wallet_revert_onhold(Setting::get('min_token_call_charge'), $video_call_request->user_id)->getData();

            DB::commit();

            $job_data['video_call_request'] = $video_call_request;

            $job_data['status'] = VIDEO_CALL_REQUEST_REJECTED;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\VideoCallRequestJob($job_data));

            return $this->sendResponse(api_success(209),209, $data=[]);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method video_call_requests_join()
     *
     * @uses to join the  video call requests
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

    public function video_call_requests_join(Request $request) {

        try {

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id',
            ];
           
            Helper::custom_validator($request->all(),$rules);

            DB::begintransaction();

            $video_call_request = VideoCallRequest::where('id',$request->video_call_request_id)->first();
            
            if(!$video_call_request) {

                throw new Exception(api_error(208),208);
            }
            
            if(!in_array($request->id, [$video_call_request->model_id, $video_call_request->user_id])) {

                throw new Exception(api_error(217),217);

            }

            // Check the schedule time

            $video_call_start_plus_minus = Setting::get('video_call_start_plus_minus', 10);

            $sub_start_time = Carbon::parse($video_call_request->start_time)->subMinutes($video_call_start_plus_minus);

            if(now() < $sub_start_time) {

                throw new Exception(api_error(211, common_date($sub_start_time, $this->timezone, "d M Y h:i A")), 211);
            }
            
            if($video_call_request->call_status == VIDEO_CALL_REQUEST_REJECTED){

                throw new Exception(api_error(210),210);
            }

            $video_call_payment = \App\Models\VideoCallPayment::where('video_call_request_id',$request->video_call_request_id)->first();

            if($video_call_request->model->video_call_amount > 0 && !$video_call_payment) {

                // throw new Exception(api_error(215),215);
            }

            if($video_call_request->call_status == VIDEO_CALL_REQUEST_ENDED){

                throw new Exception(api_error(216),216);
            }
            
            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining + $user_wallet->onhold ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }            
           
            if($remaining < $user_pay_amount) {
                throw new Exception(api_error(147), 147);    
            }
            
            $video_call_request->call_status = VIDEO_CALL_REQUEST_JOINED;

            $video_call_request->save();

            DB::commit();

            $video_call_request = VideoCallRepo::video_call_requests_single_response($video_call_request, $request,$this->timezone);

            $data['video_call_request'] = $video_call_request ?? emptyObject();

            return $this->sendResponse(api_success(210),210, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method video_call_amount_update()
     *
     * @uses to update the video call amount
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

    public function video_call_amount_update(Request $request) {

        try {

            $rules = ['amount'=>'numeric|gt:0'];
           
            Helper::custom_validator($request->all(),$rules);
       
            DB::begintransaction();

            $user = User::find($request->id);

            $user->video_call_amount = $request->amount ?:0;

            if($user->save()) {

                DB::commit();

                $data['user'] = $user;

                return $this->sendResponse(api_success(211),211,$data);

            }

            throw new Exception(api_error(212),212);
   
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method video_call_payment_by_stripe()
     *
     * @uses send amount to the video call model
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

    public function video_call_payment_by_stripe(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'video_call_request_id' => 'required|exists:video_call_requests,id,user_id,'.$request->id,
                ];

            $custom_errors = ['video_call_request_id' => api_error(214)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            $video_call_request = \App\Models\VideoCallRequest::find($request->video_call_request_id);
            
            $video_call_payments = \App\Models\VideoCallPayment::PaidApproved()->firstWhere('video_call_request_id',  $request->video_call_request_id);

            if($video_call_payments) {

                throw new Exception(api_error(213), 213);
                
            }

            $request->request->add(['video_call_request_id'=>$video_call_request->id,'payment_mode' => CARD, 'model_id' => $video_call_request->model_id]);

            $model = User::find($video_call_request->model_id);

            $total = $user_pay_amount = $model->video_call_amount ?: 1;

            if($user_pay_amount > 0) {

                $user_card = \App\Models\UserCard::where('user_id', $request->id)->firstWhere('is_default', YES);

                if(!$user_card) {

                    throw new Exception(api_error(120), 120); 

                }
                
                $request->request->add([
                    'total' => $total, 
                    'customer_id' => $user_card->customer_id,
                    'card_token' => $user_card->card_token,
                    'user_card_id' => $user_card->id,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                $card_payment_response = PaymentRepo::video_call_payment_by_stripe($request, $video_call_request)->getData();
                
                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);
            
            }

            $payment_response = PaymentRepo::video_call_payments_save($request)->getData();
           
            if($payment_response->success) {
                
                DB::commit();
               
                return $this->sendResponse(api_success(212), 212, $payment_response->data);

            } else {
              
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method video_call_payment_by_wallet()
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

    public function video_call_payment_by_wallet(Request $request) {

        try {
            
            DB::beginTransaction();

            $rules = [
                    'video_call_request_id' => 'required|exists:video_call_requests,id,user_id,'.$request->id,
                ];

            $custom_errors = ['video_call_request_id' => api_error(214)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $video_call_request = \App\Models\VideoCallRequest::find($request->video_call_request_id);

            $video_call_payments = \App\Models\VideoCallPayment::PaidApproved()->firstWhere('video_call_request_id',  $request->video_call_request_id);

            if($video_call_payments) {

                throw new Exception(api_error(213), 213);
                
            }

            $request->request->add(['video_call_request_id'=>$video_call_request->id,'payment_mode' => PAYMENT_MODE_WALLET, 'model_id' => $video_call_request->model_id]);

            $model = User::find($video_call_request->model_id);

            $video_call_amount = Setting::get('is_only_wallet_payment') ? $model->video_call_token : $model->video_call_amount;

            $total = $user_pay_amount = $video_call_amount ?: 0;

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }            

            if($remaining < $user_pay_amount) {
                throw new Exception(api_error(147), 147);    
            }

            if($user_pay_amount > 0) {
                
                $request->request->add([
                    'total' => $total * Setting::get('token_amount'),
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount * Setting::get('token_amount'),
                    'payment_id' => 'AC-'.rand(),
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'usage_type' => USAGE_TYPE_VIDEO_CALL,
                    'tokens' => $user_pay_amount,
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
                
                if($wallet_payment_response->success) {

                    $payment_response = PaymentRepo::video_call_payments_save($request)->getData();

                    if(!$payment_response->success) {

                        throw new Exception($payment_response->error, $payment_response->error_code);
                    }

                    DB::commit();

                    return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

                } else {

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                    
                }
            
            }

            $payment_response = PaymentRepo::video_call_payments_save($request)->getData();
           
            if($payment_response->success) {
                
                DB::commit();
               
                return $this->sendResponse(api_success(212), 212, $payment_response->data);

            } else {
              
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }


        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }


    /** 
     * @method video_call_payment_by_paypal()
     *
     * @uses video call payment to user
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

    public function video_call_payment_by_paypal(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id,user_id,'.$request->id,
                'payment_id' => 'required'
            ];

            $custom_errors = ['video_call_request_id' => api_error(214)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            $video_call_request = \App\Models\VideoCallRequest::find($request->video_call_request_id);
            
            $video_call_payments = \App\Models\VideoCallPayment::PaidApproved()->firstWhere('video_call_request_id',  $request->video_call_request_id);
            
            if($video_call_payments) {

                throw new Exception(api_error(213), 213);
                
            }
            
            $model = User::find($video_call_request->model_id);

            $total = $user_pay_amount = $model->video_call_amount ?: 1;
            
            $request->request->add(['video_call_request_id'=>$video_call_request->id,'paid_amount' => $user_pay_amount,'payment_mode' => PAYPAL,'payment_id' => $request->payment_id,'model_id' => $video_call_request->model_id]);

            $payment_response = PaymentRepo::video_call_payments_save($request)->getData();

            if($payment_response->success) {
                
                DB::commit();
                
                return $this->sendResponse(api_success(212), 212, $payment_response->data);

            } else {
                
                throw new Exception($payment_response->error, $payment_response->error_code);
                
            }
        
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method video_call_requests_end()
     *
     * @uses to end the  video call requests
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

    public function video_call_requests_end(Request $request) {

        try {

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id',
            ];
           
            Helper::custom_validator($request->all(),$rules);

            DB::begintransaction();

            $video_call_request = VideoCallRequest::where('id',$request->video_call_request_id)->first();
            
            if(!$video_call_request){

                throw new Exception(api_error(208),208);
            }

            if($video_call_request->call_status == VIDEO_CALL_REQUEST_ENDED){

                throw new Exception(api_error(216),216);
            }

            $video_call_request->end_time = date('Y-m-d H:i:s');

            $video_call_request->call_status = VIDEO_CALL_REQUEST_ENDED;

            $video_call_request->save();

            DB::commit();

            return $this->sendResponse(api_success(213),213, $data=[]);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /**
     * @method video_call_chat()
     *
     * @uses used to get the messages for selected live video
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param object $request
     *
     * @return response of details
     */
    public function video_call_chat(Request $request) {

        try {

            // Validation start

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id',
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            // Validation end

            $base_query = $total_query = \App\Models\VcChatMessage::where('video_call_request_id', $request->video_call_request_id)
                ->whereHas('user')
                ->whereHas('modelUser')
                ->latest();

            $chat_messages = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($chat_messages as $key => $value) {

                $value->created = $value->created_at->diffForHumans() ?? "";
            }

            if($request->device_type == DEVICE_WEB) {

                $chat_messages = array_reverse($chat_messages->toArray());

            }

            $data['messages'] = $chat_messages ?? [];

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /** 
     * @method video_call_charges()
     *
     * @uses To charge amount for Audio call
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function video_call_charges(Request $request) {

        try {

            DB::begintransaction();

            $rules = [
                'video_call_request_id' => 'required|exists:video_call_requests,id,user_id,'.$request->id,
                'total_time'=>'nullable'
            ];
           
            Helper::custom_validator($request->all(),$rules);
            
            $video_call_request = VideoCallRequest::find($request->video_call_request_id);
            
            if($video_call_request->call_status == VIDEO_CALL_REQUEST_ENDED){

                throw new Exception(api_error(216),216);
            }
            
            $model = User::find($video_call_request->model_id);

            $video_call_amount = Setting::get('is_only_wallet_payment') ? $model->video_call_token : $model->video_call_amount;

            $per_min_charge = $video_call_amount ?: 0;

            if(!$video_call_request->total_time) {

                $time = explode(':',$request->total_time);

                $minutes = ($time[0]*60) + ($time[1]) + ($time[2]/60);
                //based on paid minutes calculate amount

                $user_pay_amount = $total = $per_min_charge * (int)$minutes;

            } else {

                $total_time = date('H:i:s', strtotime($request->total_time. ' +5 seconds'));
                
                $to_time = strtotime($total_time);

                $from_time = strtotime($video_call_request->total_time);

                $minutes = round(abs($to_time - $from_time) / 60,2);

                $user_pay_amount = $total = $per_min_charge * (int)$minutes;

            }

            $request->request->add(['video_call_request_id'=>$video_call_request->id,'paid_amount' => $user_pay_amount,'payment_mode' => PAYMENT_MODE_WALLET,'payment_id' => $request->payment_id,'model_id' => $video_call_request->model_id]);

            $user_wallet = \App\Models\UserWallet::where('user_id', $request->id)->first();

            $remaining = $user_wallet->remaining + $user_wallet->onhold ?? 0;

            if(Setting::get('is_referral_enabled')) {

                $remaining = $remaining + $user_wallet->referral_amount;
                
            }            
           
            if($remaining < $user_pay_amount) {
                throw new Exception(api_error(147), 147);    
            }
            

            if($user_pay_amount > 0) {
                
                $request->request->add([
                    'total' => $total * Setting::get('token_amount'),
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount * Setting::get('token_amount'),
                    'payment_id' => 'VC-'.rand(),
                    'payment_mode' => PAYMENT_MODE_WALLET,
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'usage_type' => USAGE_TYPE_AUDIO_CALL,
                    'tokens' => $user_pay_amount,
                    'wallet_status' => WALLET_ONHOLD,
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
                
                if($wallet_payment_response->success) {

                    $payment_response = PaymentRepo::audio_call_payments_save($request)->getData();

                    if(!$payment_response->success) {

                        throw new Exception($payment_response->error, $payment_response->error_code);
                    }

                    $video_call_request->total_time = $request->total_time;

                    $video_call_request->save();

                    DB::commit();

                    return $this->sendResponse(api_success(140), 140, $payment_response->data ?? []);

                } else {

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                    
                }
            
            }
   
        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


}
