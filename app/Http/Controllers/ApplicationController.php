<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

use App\Models\StaticPage;

use App\Models\SubscriptionPayment;

use App\Models\User;

use App\Models\Subscription;

use App\Repositories\PaymentRepository as PaymentRepo;

class ApplicationController extends Controller
{

    protected $loginUser;

    public function __construct(Request $request) {
        
        $this->loginUser = User::find($request->id);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method static_pages_api()
     *
     * @uses to get the pages
     *
     * @created Vidhya R 
     *
     * @edited Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_api(Request $request) {

        $base_query = \App\Models\StaticPage::where('status', APPROVED)->orderBy('title', 'asc');
                                
        if($request->page_type) {

            $static_pages = $base_query->where('type' , $request->page_type)->first();

        } elseif($request->page_id) {

            $static_pages = $base_query->where('id' , $request->page_id)->first();

        } elseif($request->unique_id) {

            $static_pages = $base_query->where('unique_id' , $request->unique_id)->first();

        } else {

            $static_pages = $base_query->get();

        }

        $response_array = ['success' => true , 'data' => $static_pages ? $static_pages->toArray(): []];

        return response()->json($response_array , 200);

    }

    /**
     * @method static_pages_api()
     *
     * @uses to get the pages
     *
     * @created Bhawya
     *
     * @updated Bhawya, Karthick
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function static_pages_web(Request $request) {

        try {

            $rules = [ 'unique_id' => 'required|exists:static_pages,unique_id' ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $static_page = StaticPage::Approved()->firstWhere('unique_id' , $request->unique_id);

            $response_array = ['success' => true , 'data' => $static_page];

            return response()->json($response_array);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method subscription_payments_autorenewal()
     *
     * @uses to get the pages
     *
     * @created Arun
     *
     * @updated Arun
     *
     * @param - 
     *
     * @return JSON Response
     */


    public function subscription_payments_autorenewal(Request $request){

        try {

            $current_timestamp = \Carbon\Carbon::now()->toDateTimeString();

            $subscription_payments = SubscriptionPayment::where('is_current_subscription',1)->where('expiry_date','<', $current_timestamp)->get();

            if($subscription_payments->isEmpty()) {

                throw new Exception(api_error(129), 129);

            }
            DB::beginTransaction();
            foreach ($subscription_payments as $subscription_payment){

                $user = User::where('id',  $subscription_payment->user_id)->first();

                if ($user){
                    
                    // Check the subscription is available

                    $subscription = Subscription::Approved()->firstWhere('id',  $subscription_payment->subscription_id);

                    if(!$subscription) {

                        throw new Exception(api_error(129), 129);

                     }

                    
                    $is_user_subscribed_free_plan = $this->loginUser->one_time_subscription ?? NO;

                    if($subscription->amount <= 0 && $is_user_subscribed_free_plan) {

                        throw new Exception(api_error(130), 130);

                    }

                    $payment['payment_mode'] = CARD;

                    $total = $user_pay_amount = $subscription->amount;

                    $card = \App\Models\UserCard::where('user_id', $subscription->id)->firstWhere('is_default', YES);

                    if(!$card) {

                          throw new Exception(api_error(120), 120);

                     }

                    $request->request->add([
                    'total' => $total, 
                    'customer_id' => $card->customer_id,
                    'card_token' => $card->card_token,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                ]);

                     $card_payment_response = PaymentRepo::subscriptions_payment_by_stripe($request, $subscription)->getData();

                    if($card_payment_response->success == false) {

                          throw new Exception($card_payment_response->error, $card_payment_response->error_code);

                     }

                     $card_payment_data = $card_payment_response->data;

                     $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'subscription_id' => $subscription->id, 'paid_status' => $card_payment_data->paid_status]);


                    $payment_response = PaymentRepo::subscriptions_payment_save($request, $subscription)->getData();

                    if($payment_response->success) {

                        // Change old status to expired

                        SubscriptionPayment::where('id', $subscription_payment->id)->update(['is_current_subscription' => 0]);

                        // Change new is_current_subscription to 1 

                        SubscriptionPayment::where('payment_id', $payment_response->data->payment_id)->update(['is_current_subscription' => 1]);

                        $code = 118;

                        return $this->sendResponse(api_success($code), $code, $payment_response->data);

                    } else {

                        throw new Exception($payment_response->error, $payment_response->error_code);

                    }
                }else{

                throw new Exception(api_error(135), 135);

            }
            }

            DB::commit();


        }catch (Exception $e){
            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function chat_messages_save(Request $request) {

        try {

            Log::info("message_save".print_r($request->all() , true));

            DB::beginTransaction();
            
            $rules = [
                'from_user_id' => 'required|exists:users,id',
                'to_user_id' => 'required|exists:users,id',
                'message' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            $message = $request->message;

            // $from_chat_user_inputs = ['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id];

            \App\Repositories\CommonRepository::chat_user_update($request->from_user_id,$request->to_user_id);

            \App\Repositories\CommonRepository::chat_user_update($request->to_user_id,$request->from_user_id);

            // $from_chat_user = \App\Models\ChatUser::updateOrCreate($from_chat_user_inputs);

            // $to_chat_user_inputs = ['from_user_id' => $request->to_user_id, 'to_user_id' => $request->from_user_id];

            // $to_chat_user = \App\Models\ChatUser::updateOrCreate($to_chat_user_inputs);

            // $from_chat_user = \App\Models\ChatUser::where('from_user_id', $request->from_user_id)->where('to_user_id', $request->to_user_id)->first();

            // if(!$from_chat_user) {

            //     $chat_user = \App\Models\ChatUser::createor(['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id]);

            // }

            // $to_chat_user = \App\Models\ChatUser::where('from_user_id', $request->to_user_id)->where('to_user_id', $request->from_user_id)->first();

            // if(!$to_chat_user) {

            //     $chat_user = \App\Models\ChatUser::create(['from_user_id' => $request->from_user_id, 'to_user_id' => $request->to_user_id]);

            // }

            if($request->chat_message_id) {

                $chat_message = \App\Models\ChatMessage::find($request->chat_message_id) ?? new \App\Models\ChatMessage;

            } else {

               $chat_message = new \App\Models\ChatMessage;
 
            }
            
            $chat_message->from_user_id = $request->from_user_id;

            $chat_message->to_user_id = $request->to_user_id;

            $chat_message->message = $request->message;

            $chat_message->save();

            DB::commit();

            $job_data['chat_message'] = $chat_message;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new \App\Jobs\ChatMessageJob($job_data));

            return $this->sendResponse("", "", $chat_message);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method settings_generate_json()
     * 
     * @uses
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function settings_generate_json(Request $request) {

        try {

            Helper::settings_generate_json();

            return $this->sendResponse("", "", $data = []);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }


    /**
     * @method chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function get_notifications_count(Request $request) {

        try {

            Log::info("Notification".print_r($request->all(),true));

            $rules = [
                'user_id' => 'required|exists:users,id',
            ];

            Helper::custom_validator($request->all(),$rules);

            $chat_message = \App\Models\ChatMessage::where('to_user_id', $request->user_id)->where('status',NO);

            $bell_notification = \App\Models\BellNotification::where('to_user_id', $request->user_id)->where('is_read',BELL_NOTIFICATION_STATUS_UNREAD);

            $data['chat_notification'] = $chat_message->count() ?: 0;

            $data['bell_notification'] = $bell_notification->count() ?: 0;

            return $this->sendResponse("", "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function vc_chat_messages_save(Request $request) {

        try {

            Log::info("message_save".print_r($request->all() , true));

            $rules = [
                'user_id' => 'required|exists:users,id',
                'model_id' => 'required|exists:users,id',
                'message' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            $video_call_request = \App\Models\VideoCallRequest::where('video_call_requests.id', $request->video_call_request_id)->first();

            if($request->loggedin_user_id == $video_call_request->user_id) {

                $user_id = $video_call_request->user_id;

                $model_id = $video_call_request->model_id;

            } else {

                $user_id = $video_call_request->model_id;

                $model_id = $video_call_request->user_id;

            }

            $message = $request->message;

            $chat_message = new \App\Models\VcChatMessage;

            $chat_message->user_id = $user_id;

            $chat_message->model_id = $model_id;

            $chat_message->message = $request->message;

            $chat_message->video_call_request_id = $request->video_call_request_id;

            $chat_message->save();

            DB::commit();

            return $this->sendResponse("", "", $chat_message);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method ac_chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function ac_chat_messages_save(Request $request) {

        try {

            Log::info("message_save".print_r($request->all() , true));

            $rules = [
                'user_id' => 'required|exists:users,id',
                'model_id' => 'required|exists:users,id',
                'message' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            $audio_call_request = \App\Models\AudioCallRequest::where('id', $request->audio_call_request_id)->first();

            if($request->loggedin_user_id == $audio_call_request->user_id) {

                $user_id = $audio_call_request->user_id;

                $model_id = $audio_call_request->model_id;

            } else {

                $user_id = $audio_call_request->model_id;

                $model_id = $audio_call_request->user_id;

            }

            $message = $request->message;

            $chat_message = new \App\Models\AudioChatMessage;

            $chat_message->user_id = $user_id;

            $chat_message->model_id = $model_id;

            $chat_message->message = $request->message;

            $chat_message->audio_call_request_id = $request->audio_call_request_id;

            $chat_message->save();

            DB::commit();

            return $this->sendResponse("", "", $chat_message);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }

    /**
     * @method lv_chat_messages_save()
     * 
     * @uses - To save the chat message.
     *
     * @created vidhya R
     *
     * @updated vidhya R
     * 
     * @param 
     *
     * @return No return response.
     *
     */

    public function lv_chat_messages_save(Request $request) {

        try {

            Log::info("message_save".print_r($request->all() , true));

            $rules = [
                'user_id' => 'required|exists:users,id',
                'live_video_id' => 'required|exists:live_videos,id',
                'message' => 'required',
            ];

            Helper::custom_validator($request->all(),$rules);
            
            $message = $request->message;

            $chat_message = new \App\Models\LiveVideoChatMessage;

            $chat_message->from_user_id = $request->user_id;

            $chat_message->live_video_id = $request->live_video_id;

            $chat_message->message = $request->message;

            $chat_message->save();

            DB::commit();

            return $this->sendResponse("", "", $chat_message);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        }
    
    }
    
    /**
     * @method configuration_mobile()
     *
     * @uses used to get the configurations for base products
     *
     * @created Vidhya R 
     *
     * @edited Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function configuration_site(Request $request) {

        try {

            $data['is_only_wallet_payment'] = Setting::get('is_only_wallet_payment') ?? 0;

            $data['token_amount'] = Setting::get('token_amount');

            $response_array = ['success' => true , 'data' => $data];

            return response()->json($response_array , 200);

        } catch(Exception $e) {

            $error_message = $e->getMessage();

            $response_array = ['success' => false,'error' => $error_message,'error_code' => 101];

            return response()->json($response_array , 200);

        }
   
    }

    /**
     * 
     */

    public function lv_viewer_update(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['live_video_id' => 'required|exists:live_videos,id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $live_video = \App\Models\LiveVideo::where('live_videos.id', $request->live_video_id)->first();

            if(!$live_video) {

                throw new Exception(api_error(201), 201);
                
            }
            
            if($live_video->is_streaming == IS_STREAMING_NO || $live_video->status == VIDEO_STREAMING_STOPPED) {

                throw new Exception(api_error(203), 203);
                
            }

            if ($live_video->user_id == $request->id) {
                
                throw new Exception(api_error(259), 259);

            }

            $viewer = \App\Models\Viewer::where('live_video_id', $request->live_video_id)->where('user_id', $request->viewer_id)->first();

            if(!$viewer) {

                $live_video->viewer_cnt += 1;

                $live_video->save();

                $viewer = new \App\Models\Viewer;

                $viewer->user_id = $request->viewer_id;

                $viewer->live_video_id = $request->live_video_id;

                $viewer->count += 1;

                $viewer->save();

            }

            $total_earnings = \App\Models\LiveVideoPayment::where("live_video_id", $request->live_video_id)->sum("amount") ?? 0;

            DB::commit();

            $data = ['live_video_id' => $request->live_video_id, 'viewer_cnt' => $live_video->viewer_cnt, "total_earnings" => 0, "total_earnings_formatted" => formatted_amount(0)];

            return $this->sendResponse(api_success(203), $code = 203, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


}
