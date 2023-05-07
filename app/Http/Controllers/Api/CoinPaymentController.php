<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Models\User, App\Models\LiveVideo, App\Models\LiveVideoPayment;

use App\Helpers\EnvEditorHelper;

class CoinPaymentController extends Controller
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
     * 
     * @method live_videos_payment_by_coinpayment() 
     *
     * @uses Live Video Payment By coinpayment
     *
     * @created Subham
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */     

    public function live_videos_payment_by_coinpayment(Request $request) {

        try {

            // Validation start

            $rules = [
                'live_video_id' => 'required|exists:live_videos,id',
            ];

            $custom_errors = ['live_video_id' => api_error(150)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

           // Check the subscription is available

            $live_video = LiveVideo::where('id',  $request->live_video_id)
                                    ->CurrentLive()
                                    ->first();

            if(!$live_video) {

                throw new Exception(api_error(201), 201);
                
            }

            $live_video_payment = LiveVideoPayment::where('live_video_viewer_id', $request->id)->where('live_video_id', $request->live_video_id)->where('status', DEFAULT_TRUE)->count();

            if($live_video_payment) {

                throw new Exception(api_error(239), 239);
                
            }

            $model = User::find($live_video->user_id);

            $total = $user_pay_amount = $live_video->amount ?? 0.00;

            $total = $live_video->amount ?: 0.00;

            $data = new \stdClass;

            $data->amount = $total;

            $data->live_video_id = $live_video->id;

            $data->user_id = $request->id;

            $data->status = LIVE_VIDEO_PAYMENT;

            $data->note = tr('live_video_payments');

            $redirect_link = Helper::coinpayment_live_video_transaction_details($data,$this->loginUser);

            $url['redirect_web_url'] = $redirect_link;

            return $this->sendResponse($message = api_success(162), $code = 162, $url);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method tips_payment_by_coinpayment() 
     *
     * @uses Tip Payment By coinpayment
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */     

    public function tips_payment_by_coinpayment(Request $request) {

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

            $data = new \stdClass;

            $data->amount = $request->amount;

            $data->from_user_id = $request->id;

            $data->to_user_id = $request->user_id;

            $data->unique_id = $user->unique_id;

            $data->status = TIP_PAYMENT;

            $data->note = tr('tips_payment');

            $redirect_link = Helper::coinpayment_tips_transaction_details($data,$this->loginUser);

            $url['redirect_web_url'] = $redirect_link;

            return $this->sendResponse($message = api_success(162), $code = 162, $url);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method posts_payment_by_coinpayment() 
     *
     * @uses Post Payment By coinpayment
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */     

    public function posts_payment_by_coinpayment(Request $request) {

        try {

            // Validation start

            $rules = ['post_id' => 'required|exists:posts,id'];

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

            $total = $post->amount ?: 0.00;

            $data = new \stdClass;

            $data->amount = $total;

            $data->user_id = $request->id;

            $data->post_id = $request->post_id;

            $data->post_unique_id = $post->unique_id;

            $data->status = POST_PAYMENT;

            $data->note = tr('post_payments');

            $redirect_link = Helper::coinpayment_post_transaction_details($data,$this->loginUser);

            $url['redirect_web_url'] = $redirect_link;

            return $this->sendResponse($message = api_success(162), $code = 162, $url);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /** 
     * @method subscriptions_payment_by_coinpayment()
     *
     * @uses pay for subscription using coinpayment
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

    public function subscriptions_payment_by_coinpayment(Request $request) {

        try {

            $rules = [
                'user_unique_id' => 'required|exists:users,unique_id',
                'plan_type' => 'required',
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

            $total = $user_pay_amount = $subscription_amount ?: 0.00;

            $data = new \stdClass;

            $data->amount = $total;

            $data->user_id = $request->id;

            $data->user_unique_id = $request->user_unique_id;

            $data->plan_type = $request->plan_type;

            $data->status = SUBSCRIPTION_PAYMENT;

            $data->note = tr('subscription_payments');

            $redirect_link = Helper::coinpayment_subscription_transaction_details($data,$this->loginUser);

            $url['redirect_web_url'] = $redirect_link;

            return $this->sendResponse($message = api_success(162), $code = 162, $url);
        
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

            return redirect(route('tips_coinpayment_success', ['from_user_id'=>$request->from_user_id, 'to_user_id'=>$request->to_user_id, 'paid_amount'=>$request->paid_amount,'unique_id'=>$request->unique_id]));

        } else if ($request->status == POST_PAYMENT) { 

            return redirect(route('posts_coinpayment_success', ['user_id'=>$request->user_id, 'post_id'=>$request->post_id, 'paid_amount'=>$request->paid_amount,'post_unique_id'=>$request->post_unique_id]));

        } else if ($request->status == SUBSCRIPTION_PAYMENT) {

            return redirect(route('subscription_coinpayment_success', ['user_id'=>$request->user_id, 'user_unique_id'=>$request->user_unique_id,'plan_type'=>$request->plan_type, 'paid_amount'=>$request->paid_amount]));

        } else if ($request->status == LIVE_VIDEO_PAYMENT) {

            return redirect(route('live_video_coinpayment_success', ['user_id'=>$request->user_id, 'live_video_id'=>$request->live_video_id, 'paid_amount'=>$request->paid_amount]));

        }

    }

    /**
     * @method tips_coinpayment_success() 
     *
     * @uses Tip Payment By
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */    
    public function tips_coinpayment_success(Request $request) {
        try {

            DB::beginTransaction();

            $request->request->add([
                'payment_mode' => COINPAYMENT,
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
     * @method payment_failure() 
     *
     * @uses Payment Failure
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */   
    public function payment_failure(Request $request) {

    	return redirect()->away(Setting::get('frontend_url'));
    	
    }

    /**
     * @method posts_coinpayment_success() 
     *
     * @uses Tip Payment By COINPAYMENT
     *
     * @created Arun
     *
     * @updated 
     *
     * @param
     *
     * @return json repsonse
     */    
    public function posts_coinpayment_success(Request $request) {
        try {

            DB::beginTransaction();

            $request->request->add(['payment_mode'=> COINPAYMENT,'id' => $request->user_id,]);

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
     * @method subscription_coinpayment_success()
     *
     * @uses Subscription Payment By COINPAYMENT
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

    public function subscription_coinpayment_success(Request $request) {

        try {

            DB::beginTransaction();
            
            $request->request->add([
                'payment_mode' => COINPAYMENT, 
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

    /** 
     * @method live_video_coinpayment_success()
     *
     * @uses Subscription Payment By COINPAYMENT
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

    public function live_video_coinpayment_success(Request $request) {

        try {

            DB::beginTransaction();
            
            $request->request->add([
                'payment_mode' => COINPAYMENT, 
                'id' => $request->user_id,
                'payment_id' => 'LC-'.rand(),
                'usage_type' => LIVE_VIDEO
            ]);

            Log::info("Request Data".print_r($request->all(), true));

            $live_video = LiveVideo::where('id',  $request->live_video_id)
                                    ->CurrentLive()
                                    ->first();

            $payment_response = PaymentRepo::live_videos_payment_save($request,$live_video)->getData();
            
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
