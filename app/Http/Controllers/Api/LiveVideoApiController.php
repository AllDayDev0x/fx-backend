<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Support\Collection;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Repositories\LiveVideoRepository as LiveVideoRepo;

use App\Repositories\PaymentRepository as PaymentRepo;

use App\Repositories\PostRepository as PostRepo;

use App\Helpers\Helper;

use App\Models\User, App\Models\CategoryDetail;

use App\Models\LiveVideo, App\Models\LiveVideoPayment, App\Models\Viewer;

use App\Models\{ UserProduct, Follower, Post, PostFile };

use Illuminate\Validation\Rule;

use App\Http\Resources\{ UserPreviewResource };

use App\Repositories\ProductRepository;

class LiveVideoApiController extends Controller
{
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
     * @method live_videos()
     *
     * @uses get the current live streaming videos
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

    public function live_videos(Request $request) {

        try {

            $public_videos = LiveVideo::CurrentLive()->where('type',TYPE_PUBLIC)->orderBy('live_videos.id', 'desc');

            // this function has all common check conditions for videos
            $public_videos = LiveVideoRepo::live_videos_common_query($request, $public_videos,TYPE_PUBLIC);

            $total_public_videos = $public_videos->count() ?? 0;

            $public_live_videos = $public_videos->skip($this->skip)->take($this->take)->get();

            $public_live_videos = LiveVideoRepo::live_videos_list_response($public_live_videos, $request);

            $private_videos = LiveVideo::CurrentLive()->where('type',TYPE_PRIVATE)->orderBy('live_videos.id', 'desc');

            // this function has all common check conditions for videos
            $private_videos = LiveVideoRepo::live_videos_common_query($request, $private_videos,TYPE_PRIVATE);

            $total_private_videos = $private_videos->count() ?? 0;

            $private_live_videos = $private_videos->skip($this->skip)->take($this->take)->get();

            $private_live_videos = LiveVideoRepo::live_videos_list_response($private_live_videos, $request);
            
            // Create a new collection instance.
            $collection = new Collection($public_live_videos);

            $custom_collection = $collection->merge($private_live_videos);

            $shuffled = $custom_collection->sortByDesc('updated', SORT_REGULAR)->values();

            $feed = $shuffled->values()->all();

            $data['live_videos'] = $feed ?? emptyObject();

            $data['total'] = $total_public_videos + $total_private_videos;

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_view()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_view(Request $request) {

        try {

            // Validation start

            $rules = ['live_video_unique_id' => 'required|exists:live_videos,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $base_query = LiveVideo::CurrentLive()->where('unique_id',$request->live_video_unique_id);

            // this function has all common check conditions for videos
            $base_query = LiveVideoRepo::live_videos_common_query($request, $base_query);

            $live_video_details = $base_query->first();

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
                
            }
            if($request->id == $live_video_details->user_id) {

                $live_video_details->is_user_needs_to_pay =  NO;

                $live_video_details->is_owner = YES;

            } else {

                $live_video_details->is_owner = NO;

                $live_video_details->is_user_needs_to_pay = LiveVideoRepo::live_videos_check_payment($live_video_details, $request->id);

                if ($live_video_details->is_user_needs_to_pay == NO) {
                    
                    $live_video_payment = LiveVideoPayment::where('live_video_id', $live_video_details->id)
                                            ->where('live_video_viewer_id', $request->id)->first();

                    $live_video_details->live_video_paid_amount = $live_video_payment->amount ?? 0;

                    $live_video_details->live_video_paid_amount_formatted = formatted_amount($live_video_payment->amount ?? 0);
                }

            }

            $live_video_details->start_date = common_date($live_video_details->created_at , $this->timezone);

            $recent_viewer_ids = Viewer::where('live_video_id', $live_video_details->id)->orderBy('created_at', 'desc')->take(3)->pluck('user_id');

            $live_video_details->recent_viewers = User::whereIn('id', $recent_viewer_ids)->get();

            $live_video_revenue = LiveVideoPayment::where('live_video_id', $live_video_details->id)
                                                    ->where('status', PAID_STATUS)->sum('amount');

            $live_video_details->live_video_revenue = $live_video_revenue;

            $live_video_details->live_video_revenue_formatted = formatted_amount($live_video_revenue);

            $request->request->add(['broadcast_type' => $live_video_details->broadcast_type, 'virtual_id' => $live_video_details->virtual_id, 'live_video_id' => $live_video_details->live_video_id]);

            return $this->sendResponse($message = '', $code = '', $live_video_details);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_search()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_search(Request $request) {

        try {

            $base_query = LiveVideo::CurrentLive();

            // this function has all common check conditions for videos
            $base_query = LiveVideoRepo::live_videos_common_query($request, $base_query);

            // search query

            $base_query = $total_query = $base_query->where('title', 'like', "%".$request->key."%")->orderBy('live_videos.id', 'desc');

            $live_videos = $base_query->skip($this->skip)->take($this->take)->get();

            $live_videos = LiveVideoRepo::live_videos_list_response($live_videos, $request);

            $data['live_videos'] = $live_videos ?? emptyObject();

            $data['total'] = $total_query->count() ?? [];

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

   

   
    /** 
     * @method live_videos_payment_by_card()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_payment_by_card(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                    'live_video_id' => 'required|exists:live_videos,id',
                    'coupon_code' => 'nullable|exists:coupons,coupon_code',
                    ];

            $custom_errors = ['live_video_id' => api_error(150)];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            // Check the live video is streaming

            $live_video_details = LiveVideo::where('id',  $request->live_video_id)
                                    ->CurrentLive()
                                    ->first();

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
                
            }

            $live_video_payment = LiveVideoPayment::where('live_video_viewer_id', $request->id)->where('live_video_id', $request->live_video_id)->where('status', DEFAULT_TRUE)->count();

            // check the live video payment status || whether user already paid

            if($live_video_details->payment_status == NO || $live_video_payment) {

                $code = 140;

                goto successReponse;
                
            }

            $request->request->add(['payment_mode' => CARD]);

            $total = $user_pay_amount = $live_video_details->amount ?? 0.00;

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
                
                $card_payment_response = PaymentRepo::live_videos_payment_by_stripe($request, $live_video_details)->getData();

                if($card_payment_response->success == false) {

                    throw new Exception($card_payment_response->error, $card_payment_response->error_code);
                    
                }

                $card_payment_data = $card_payment_response->data;

                $request->request->add(['paid_amount' => $card_payment_data->paid_amount, 'payment_id' => $card_payment_data->payment_id, 'paid_status' => $card_payment_data->paid_status]);

            }

            $payment_response = PaymentRepo::live_videos_payment_save($request, $live_video_details)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $code = 118;

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
            }          

            successReponse:

            $data['live_video_id'] = $request->live_video_id;

            $data['live_video_unique_id'] = $live_video_details->unique_id;

            $data['payment_mode'] = CARD;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_payment_by_paypal()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_payment_by_paypal(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'live_video_id' => 'required|exists:live_videos,id',
                'payment_id' => 'required',
            ];

            $custom_errors = ['live_video_id' => api_error(150)];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            // Check the live video is streaming

            $live_video_details = LiveVideo::where('id',  $request->live_video_id)
                                    ->CurrentLive()
                                    ->first();

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
                
            }

            $live_video_payment = LiveVideoPayment::where('live_video_viewer_id', $request->id)->where('live_video_id', $request->live_video_id)->where('status', DEFAULT_TRUE)->count();

            // check the live video payment status || whether user already paid

            if($live_video_details->payment_status == NO || $live_video_payment) {

                $code = 140;

                goto successReponse;
                
            }

            $total = $user_pay_amount = $live_video_details->amount ?? 0.00;

            $request->request->add(['payment_mode' => PAYPAL,'paid_amount' => $total]);

            $payment_response = PaymentRepo::live_videos_payment_save($request, $live_video_details)->getData();

            if($payment_response->success) {
                
                DB::commit();

                $code = 118;

            } else {

                throw new Exception($payment_response->error, $payment_response->error_code);
            }          

            successReponse:

            $data['live_video_id'] = $request->live_video_id;

            $data['live_video_unique_id'] = $live_video_details->unique_id;

            $data['payment_mode'] = CARD;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_payment_by_wallet()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_payment_by_wallet(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = [
                'live_video_id' => 'required|exists:live_videos,id',
            ];

            $custom_errors = ['live_video_id' => api_error(150)];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            // Check the live video is streaming

            $live_video = LiveVideo::where('id',  $request->live_video_id)
                                    ->CurrentLive()
                                    ->first();

            if(!$live_video) {

                throw new Exception(api_error(201), 201);
                
            }

            $live_video_payment = LiveVideoPayment::where('live_video_viewer_id', $request->id)->where('live_video_id', $request->live_video_id)->where('status', DEFAULT_TRUE)->count();

            if($live_video->payment_status == NO || $live_video_payment) {

                $code = 140;

                goto successReponse;
                
            }

            $model = User::find($live_video->user_id);

            $amount = Setting::get('is_only_wallet_payment') ? $live_video->token : $live_video->amount;

            $total = $user_pay_amount = $amount ?? 0.00;

            $request->request->add(['payment_mode' => PAYMENT_MODE_WALLET,'paid_amount' => $total]);

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
                    'total' => $user_pay_amount,
                    'user_pay_amount' => $user_pay_amount,
                    'paid_amount' => $user_pay_amount,
                    'payment_id' => 'LC-'.rand(),
                    'usage_type' => USAGE_TYPE_LIVE_VIDEO,
                    'payment_type' => WALLET_PAYMENT_TYPE_PAID,
                    'amount_type' => WALLET_AMOUNT_TYPE_MINUS,
                    'tokens' => $user_pay_amount,
                ]);

                $wallet_payment_response = PaymentRepo::user_wallets_payment_save($request)->getData();
                
                if($wallet_payment_response->success) {

                    $payment_response = PaymentRepo::live_videos_payment_save($request,$live_video)->getData();

                    if(!$payment_response->success) {

                        throw new Exception($payment_response->error, $payment_response->error_code);
                    }

                    DB::commit();

                    return $this->sendResponse(api_success(118), 118, $payment_response->data ?? []);

                } else {

                    throw new Exception($wallet_payment_response->error, $wallet_payment_response->error_code);
                    
                }
            
            }

            successReponse:

            $data['live_video_id'] = $request->live_video_id;

            $data['live_video_unique_id'] = $live_video->unique_id;

            return $this->sendResponse(api_success($code), $code, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_payment_history()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_payment_history(Request $request) {

        try {

            $base_query = $total_query = LiveVideoPayment::where('live_video_viewer_id', $request->id)->orderBy('live_video_payments.id', 'desc');

            $live_video_payments = $base_query->skip($this->skip)->take($this->take)->get();

            foreach ($live_video_payments as $key => $live_video_payment) {

                $live_video_details = $live_video_payment->getVideo ?? [];

                $live_video_payment->title = $live_video_details->title ?? "-";

                $live_video_payment->description = $live_video_details->description ?? "-";

                $live_video_payment->snapshot = $live_video_details->snapshot ?? asset('images/live-streaming.jpeg');

                $user_details = $live_video_payment->getUser ?? [];

                $live_video_payment->user_name = $user_details->name ?? "user-deleted";

                $live_video_payment->user_picture = $user_details->picture ?? asset('placeholder.jpg');

                unset($live_video_payment->getUser);

                unset($live_video_payment->getVideo);
            }

            $data['live_video_payments'] = $live_video_payments ?? emptyObject();

            $data['total'] = $total_query->count() ?? [];

            return $this->sendResponse($message = '', $code = '', $data);

            return $this->sendResponse($message = '', $code = '', $live_video_payments);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_broadcast_start()
     *
     * @uses get the current live streaming videos
     *
     * @created Bhawya N
     *
     * @updated Bhawya N, Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function live_videos_broadcast_start(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start
            $rules = [
                'title' => 'required',
                // 'description' => 'required|max:255',
                'picture' => 'nullable|mimes:jpeg,jpg,bmp,png',
                'payment_status'=>'required|numeric',
                'amount' => $request->payment_status ? 'required|numeric|min:0.01|max:100000' : '',
                'type' => 'required|in:'.TYPE_PRIVATE.','.TYPE_PUBLIC,
            ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $agora_app_id = Setting::get('agora_app_id');

            $appCertificate = Setting::get('agora_certificate_id');
            
            if(Setting::get('is_agora_configured')) {

                if($request->device_type != DEVICE_WEB) {

                    $rules = ['virtual_id' => 'required'];

                    Helper::custom_validator($request->all(), $rules, $custom_errors = []);

                }

                if(!$agora_app_id || !$appCertificate) {

                    throw new Exception(api_error(204), 204);
                    
                }


            }
            
            // Validation end

            $user = $this->loginUser;

            // check the user paid and content creator

            if(!$user->is_subscription_enabled) {

                throw new Exception(api_error(131), 131);
            }

            // Check the user have any ongoing streaming

            $check_ongoing_streaming = LiveVideo::where('user_id', $request->id)->where('status', VIDEO_STREAMING_ONGOING)->count();

            if($check_ongoing_streaming) {
                // throw new Exception(api_error(200), 200);
            }

            $live_video = new LiveVideo;

            $live_video->user_id = $request->id;

            $live_video->title = $request->title;

            $live_video->description = $request->description ?? "";

            $live_video->snapshot = Setting::get('live_streaming_placeholder_img') ?? '';

            // Upload picture
            if($request->hasFile('snapshot') != "") {

                $live_video->snapshot = Helper::storage_upload_file($request->file('snapshot'));
            }

            $live_video->type = $request->type ?? TYPE_PUBLIC;
            
            $live_video->broadcast_type = $request->broadcast_type ?? BROADCAST_TYPE_BROADCAST;

            $live_video->payment_status = $request->payment_status ?? FREE_VIDEO;

            $amount = $request->amount ?? 0;

            if(Setting::get('is_only_wallet_payment')) {

                $live_video->token = $amount;

                $live_video->amount = $live_video->token * Setting::get('token_amount');

            } else {

                $live_video->amount = $amount;

            }

            $live_video->status = VIDEO_STREAMING_ONGOING;

            $live_video->is_streaming = IS_STREAMING_YES;

            $live_video->virtual_id = $request->virtual_id ?? md5(time());

            $live_video->unique_id = $live_video->title ?? "";

            $live_video->browser_name = $request->browser ?? '';

            $live_video->start_time = now();

            $token = '';

            if(Setting::get('is_agora_configured')) { 

                $uid = 0;

                $role = \RtcTokenBuilder::RoleAttendee;

                $expireTimeInSeconds = 3600;

                $currentTimestamp = (new \DateTime("now", new \DateTimeZone('UTC')))->getTimestamp();

                $privilegeExpiredTs = $currentTimestamp + $expireTimeInSeconds;

                $token = \RtcTokenBuilder::buildTokenWithUid($agora_app_id, $appCertificate, $live_video->virtual_id, $uid, $role, $privilegeExpiredTs);

            }

            $live_video->agora_token = $token ?? '';

            $live_video->save();

            DB::commit();

            $this->dispatch(new \App\Jobs\LiveVideoNotificationToFollower($request->id, $live_video, $request->live_group_id));

            $data = LiveVideo::where('live_videos.id', $live_video->id)->first();

            return $this->sendResponse(api_success(204), $code = 204, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_viewer_update()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_viewer_update(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['live_video_id' => 'required|exists:live_videos,id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $live_video = LiveVideo::where('live_videos.id', $request->live_video_id)->first();

            if(!$live_video) {

                throw new Exception(api_error(201), 201);
                
            }
            
            if($live_video->is_streaming == IS_STREAMING_NO || $live_video->status == VIDEO_STREAMING_STOPPED) {

                throw new Exception(api_error(203), 203);
                
            }

            if ($live_video->user_id == $request->id) {
                
                throw new Exception(api_error(259), 259);

            }

            $viewer = Viewer::where('live_video_id', $request->live_video_id)->where('user_id', $request->id)->first();

            if(!$viewer) {

                $live_video->viewer_cnt += 1;

                $live_video->save();

                $viewer = new Viewer;

                $viewer->user_id = $request->id;

                $viewer->live_video_id = $request->live_video_id;

                $viewer->count += 1;

                $viewer->save();

            }

            DB::commit();

            $data = ['live_video_id' => $request->live_video_id, 'viewer_cnt' => $live_video->viewer_cnt];

            return $this->sendResponse(api_success(203), $code = 203, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_snapshot_save()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_snapshot_save(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['live_video_id' => 'required|exists:live_videos,id', 'snapshot' => 'required'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $live_video_details = LiveVideo::where('live_videos.id', $request->live_video_id)->first();

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
                
            }

            if($live_video_details->is_streaming == IS_STREAMING_NO || $live_video_details->status == VIDEO_STREAMING_STOPPED) {

                throw new Exception(api_error(171), 171);
                
            }

            if ($request->device_type == DEVICE_IOS) {

                $picture = $request->file('snapshot');
                
                $ext = $picture->getClientOriginalExtension();

                $picture->move(public_path().'/uploads/rooms/', $request->live_video_id . "." . $ext);

                $live_video_details->snapshot = url('/').'/uploads/rooms/'.$request->live_video_id . '.png';

            } else {

                $data = explode(',', $request->get('snapshot'));

                file_put_contents(join(DIRECTORY_SEPARATOR, [public_path(), 'uploads', 'rooms', $request->live_video_id . '.png']), base64_decode($data[1]));

                $live_video_details->snapshot = url('/').'/uploads/rooms/'.$request->live_video_id . '.png';
            }  

            $live_video_details->save();

            // @todo Wowza stop 

            DB::commit();

            $data = ['live_video_id' => $request->live_video_id];

            return $this->sendResponse(api_success(139), $code = 139, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_broadcast_stop()
     *
     * @uses get the current live streaming videos
     *
     * @created Bhawya N
     *
     * @updated Bhawya N, Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function live_videos_broadcast_stop(Request $request) {

        try {
            
            DB::beginTransaction();

            // Validation start
            $rules = ['live_video_id' => 'required|exists:live_videos,id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $live_video_details = LiveVideo::where('live_videos.id', $request->live_video_id)->first();

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
                
            }
            
            if($live_video_details->is_streaming == IS_STREAMING_NO || $live_video_details->status == VIDEO_STREAMING_STOPPED) {

                throw new Exception(api_error(202), 202);
                
            }
            
            $live_video_details->is_streaming = IS_STREAMING_NO;

            $live_video_details->status = VIDEO_STREAMING_STOPPED;

            $live_video_details->save();

            $live_video_details->end_time = now();
            
            $live_video_details->no_of_minutes = getMinutesBetweenTime($live_video_details->start_time, $live_video_details->end_time);

            $live_video_details->save();

            DB::commit();

            $data = ['live_video_id' => $request->live_video_id];

            return $this->sendResponse(api_success(201), $code = 201, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_check_streaming()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_check_streaming(Request $request) {

        try {

            // Validation start

            $rules = ['live_video_id' => 'required|exists:live_videos,id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $live_video_details = LiveVideo::where('live_videos.id', $request->live_video_id)->first();

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
                
            }

            if($live_video_details->is_streaming == IS_STREAMING_NO) {

                throw new Exception(api_error(203), 203);
            
            }

            if($live_video_details->status == VIDEO_STREAMING_STOPPED) {

                throw new Exception(api_error(203), 203);
                
            }

            $data = ['viewer_cnt' => $live_video_details->viewer_cnt];

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_erase_old_streamings()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_erase_old_streamings(Request $request) {

        try {

            DB::beginTransaction();

            LiveVideo::where('user_id', $request->id)->where('status', VIDEO_STREAMING_ONGOING)->where('is_streaming', IS_STREAMING_NO)->delete();

            $live_videos = LiveVideo::where('user_id', $request->id)->where('status', VIDEO_STREAMING_ONGOING)->where('is_streaming', IS_STREAMING_YES)->get();

            foreach($live_videos as $key => $live_video) {

                $live_video->status = DEFAULT_TRUE;

                $live_video->end_time = getUserTime(date('H:i:s'), $this->timezone, 'H:i:s');

                $live_video->no_of_minutes = getMinutesBetweenTime($live_video->start_time, $live_video->end_time);

                $live_video->save();

            }

            DB::commit();

            return $this->sendResponse(api_success(205), $code = 205, $data = []);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method live_videos_owner_list()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_owner_list(Request $request) {

        try {

            $base_query = $total_query = LiveVideo::where('live_videos.user_id', $request->id)->orderBy('live_videos.id', 'desc');

            $live_videos = $base_query->skip($this->skip)->take($this->take)->get();
            
            $data['live_videos'] = $live_videos ?? emptyObject();

            $data['total'] = LiveVideo::where('live_videos.user_id', $request->id)->count() ?? [];

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_owner_view()
     *
     * @uses get the current live streaming videos
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

    public function live_videos_owner_view(Request $request) {

        try {

            // Validation start

            $rules = [
                    'live_video_id' => 'required|exists:live_videos,id'
                    ];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $live_video_details = LiveVideo::where('live_videos.id', $request->live_video_id)->where('live_videos.user_id', $request->id)->first();

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
                
            }

            return $this->sendResponse($message = '', $code = '', $live_video_details);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_video_chat_messages()
     *
     * @uses
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
    public function live_video_chat_messages(Request $request) {

        try {

            $rules = [
                'search_key' => 'nullable',
                'live_video_id'=>'required|exists:live_videos,id'
            ];

            Helper::custom_validator($request->all(), $rules);

            $search_key = $request->search_key;

            $base_query = $total_query = \App\Models\LiveVideoChatMessage::where('live_video_chat_messages.message', 'like', "%".$search_key."%")
                    ->where('live_video_id',$request->live_video_id);
                    // ->orderBy('live_video_chat_messages.updated_at', 'asc');

            $base_query = $base_query->latest();

            $live_video_chat_messages = $base_query->skip($this->skip)->take($this->take)->get();
        
            if($request->device_type == DEVICE_WEB) {

                $live_video_chat_messages = array_reverse($live_video_chat_messages->toArray());

            }

            $data['messages'] = $live_video_chat_messages ?? [];

            $data['total_messages'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_viewers_list()
     *
     * @uses get the current live streaming viewers list
     *
     * @created Arun
     *
     * @updated Karthick
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function live_videos_viewers_list(Request $request) {

        try {

            // Validation start

            $rules = ['live_video_unique_id' => 'required|exists:live_videos,unique_id'];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);
            
            // Validation end

            $live_video = LiveVideo::CurrentLive()->where('unique_id', $request->live_video_unique_id)->first();

            if(!$live_video) {

                throw new Exception(api_error(201), 201);
                
            }

            $viewer_ids = Viewer::where('live_video_id', $live_video->id)->pluck('user_id');

            $base_query = $total_query = User::whereIn('id', $viewer_ids);

            $data['is_owner'] = $request->id == $live_video->user_id ? YES : NO;

            $data['is_paid_post'] = $live_video->payment_status == YES ? YES : NO;

            $data['live_video_amount'] = formatted_amount(Setting::get('is_only_wallet_payment') ? $live_video->token : $live_video->amount);

            $data['total_revenue'] = formatted_amount(LiveVideoPayment::where(['live_video_id' => $live_video->id, 'user_id' => $live_video->user_id, 'status' => PAID])->sum('amount'));

            $data['total_viewers'] = $total_query->count();

            $viewers = UserPreviewResource::collection($base_query->skip($this->skip)->take($this->take)->get());

            $data['viewers'] = $viewers ?? [];

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method popular_live_videos()
     *
     * @uses get the polpular live streaming videos
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

    public function popular_live_videos(Request $request) {

        try {

            $popular_live_video_ids = LiveVideoRepo::popular_live_videos($request);

            $base_query = $total_query = LiveVideo::whereIn('id', $popular_live_video_ids);

            $data['total'] = $total_query->count();

            $live_videos = $base_query->skip($this->skip)->take($this->take)->get();

            $live_videos = LiveVideoRepo::live_videos_list_response($live_videos, $request);

            $data['live_videos'] = $live_videos ?? [];

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method recommended_live_videos()
     *
     * @uses get the recommended live streaming videos
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

    public function recommended_live_videos(Request $request) {

        try {

            $base_query = $total_query = LiveVideo::CurrentLive()
                                        ->where('user_id', '!=',$request->id);

            if (!$request->filled('search_key') && !$request->filled('category_id')) {

                $popular_live_video_ids = LiveVideoRepo::popular_live_videos($request);
                
                $base_query = $base_query->whereNotIn('id', $popular_live_video_ids);

            }

            if ($request->filled('category_id')) {
                
                $user_ids = CategoryDetail::where('category_id', $request->category_id)->where('post_id', 0)->pluck('user_id');

                $base_query = $base_query->whereIn('user_id', $user_ids);
            }

            if ($request->filled('search_key')) {

                $search_key = $request->search_key;

                $base_query = $base_query->whereHas('user',function($query) use($search_key){

                                    return $query->where('users.name','LIKE','%'.$search_key.'%');
                                    
                                })
                                ->orWhere('live_videos.title','LIKE','%'.$search_key.'%')
                                ->orWhere('live_videos.description','LIKE','%'.$search_key.'%');
            }

            $data['total'] = $total_query->count();

            $live_videos = $base_query->skip($this->skip)->take($this->take)->get();

            $live_videos = LiveVideoRepo::live_videos_list_response($live_videos, $request);

            $data['live_videos'] = $live_videos ?? [];

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method live_videos_list()
     *
     * @uses to get all the ongoing live video details
     *
     * @created Karthick
     *
     * @updated
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function live_videos_list(Request $request) {

        try {

            $rules = ['type' => ['nullable', Rule::in(TYPE_PRIVATE, TYPE_PUBLIC)]];

            Helper::custom_validator($request->all(), $rules, $custom_errors = []);

            $blocked_user_ids = array_merge([$request->id], blocked_users($request->id));

            $live_video_user_ids = LiveVideo::CurrentLive()->whereNotIn('user_id', $blocked_user_ids)->distinct('user_id')->pluck('user_id');

            $model_blocked_user_ids = [];

            foreach($live_video_user_ids as $live_video_user_id) {

                $blocked_ids =  blocked_users($live_video_user_id);

                if(in_array($request->id, $blocked_ids)) {

                    $model_blocked_user_ids = array_merge($blocked_ids, [$live_video_user_id]);
                }

            }

            $blocked_users = array_unique(array_merge($blocked_user_ids, $model_blocked_user_ids));

            $user_following_ids = get_follower_ids($request->id);

            $data['public_videos'] = $data['private_videos'] = $data['videos'] =  [];

            $data['total_videos'] = 0;

            if($request->type) {

                if($request->type == TYPE_PRIVATE) {

                    $base_query = LiveVideo::CurrentLive()->PrivateVideos()
                                ->whereNotIn('user_id', $blocked_users)->whereIn('user_id', $user_following_ids)
                                ->orderBy('created_at', 'desc');

                    $base_query = LiveVideoRepo::apply_filters($base_query, $request);

                    $data['total_videos'] = $base_query->count();

                    $private_videos = $base_query->skip($this->skip)->take($this->take)->get();

                    $data['videos'] = LiveVideoRepo::live_videos_index_list_response($private_videos, $request->id, $this->timezone);
                }

                if($request->type == TYPE_PUBLIC) {

                    $base_query = LiveVideo::CurrentLive()->PublicVideos()
                                ->whereNotIn('user_id', $blocked_users)->orderBy('created_at', 'desc');

                    $base_query = LiveVideoRepo::apply_filters($base_query, $request);

                    $data['total_videos'] = $base_query->count();

                    $public_videos = $base_query->skip($this->skip)->take($this->take)->get();

                    $data['videos'] = LiveVideoRepo::live_videos_index_list_response($public_videos, $request->id, $this->timezone);
                }

            } else {

                $private_videos = LiveVideo::CurrentLive()->PrivateVideos()
                                ->whereNotIn('user_id', $blocked_users)->whereIn('user_id', $user_following_ids)
                                ->orderBy('created_at', 'desc')->take(8)->get();

                $public_videos = LiveVideo::CurrentLive()->PublicVideos()
                                ->whereNotIn('user_id', $blocked_users)
                                ->orderBy('created_at', 'desc')->take(8)->get();

                $data['private_videos'] = LiveVideoRepo::live_videos_index_list_response($private_videos, $request->id, $this->timezone);

                $data['public_videos'] = LiveVideoRepo::live_videos_index_list_response($public_videos, $request->id, $this->timezone);
            }

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method single_live_video_view()
     *
     * @uses get the current live streaming videos
     *
     * @created Karthick
     *
     * @updated
     *
     * @param
     * 
     * @return JSON response
     *
     */

    public function single_live_video_view(Request $request) {

        try {

            $rules = ['live_video_unique_id' => 'required|exists:live_videos,unique_id'];

            $custom_errors = ['exists' => api_error(201)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $live_video_details = LiveVideo::CurrentLive()->firstWhere(['unique_id' => $request->live_video_unique_id]);

            if(!$live_video_details) {

                throw new Exception(api_error(201), 201);
            }

            $user_block_user_ids = blocked_users($request->id);

            $model_block_user_ids = blocked_users($live_video_details->user_id);

            $is_owner = $request->id == $live_video_details->user_id ? YES : NO;

            $is_follower = Follower::where(['user_id' => $live_video_details->user_id, 'follower_id' => $request->id, 'status' => YES])->exists();

            if(in_array($live_video_details->user_id, $user_block_user_ids)) {

                throw new Exception(api_error(260), 260);
            }

            if(in_array($request->id, $model_block_user_ids)) {

                throw new Exception(api_error(261), 261);
            }

            if($live_video_details->type == TYPE_PRIVATE && !$is_follower && !$is_owner) {

                throw new Exception(api_error(262), 262);
            }

            if($live_video_details->payment_status == YES && $request->id != $live_video_details->user_id) {

                $live_video_paid_amount = LiveVideoPayment::where(['live_video_id' => $live_video_details->id, 'live_video_viewer_id' => $request->id])->sum('amount');

                if(!$live_video_paid_amount) {

                    throw new Exception(api_error(263), 263);

                }
            }

            if($request->id == $live_video_details->user_id) {

                $live_video_details->is_owner = YES;

                $live_video_details->is_user_needs_to_pay =  NO;

                $live_video_revenue = LiveVideoPayment::where(['live_video_id' => $live_video_details->id, 'status' => PAID_STATUS])->sum('amount');

                $live_video_details->live_video_paid_amount_formatted = formatted_amount($live_video_revenue);

            } else {

                $live_video_details->is_following = $is_follower ? YES : NO;

                $live_video_details->is_owner = NO;

                $live_video_details->is_user_needs_to_pay = LiveVideoRepo::live_videos_check_payment($live_video_details, $request->id);

                $live_video_details->live_video_paid_amount_formatted = formatted_amount(isset($live_video_paid_amount) ? : 0);

                $user_products = UserProduct::where(['user_id' => $live_video_details->user_id])->orderBy('created_at', 'desc')->take(4)->get();

                $user_products = ProductRepository::user_products_list_response($user_products, $request);

                $user_posts = Post::where(['user_id' => $live_video_details->user_id])->orderBy('created_at', 'desc')->take(4)->get();

                $user_posts = $user_posts->map(function ($user_post) use ($request) {

                            $user_post->is_user_needs_pay = $user_post->is_paid_post;

                            $user_post->payment_info = PostRepo::posts_user_payment_check($user_post, $request);

                            $is_user_needs_pay = $user_post->payment_info->is_user_needs_pay ?? NO; 

                            $user_post->post_file = PostFile::where('post_id', $user_post->post_id)
                                                    ->when($is_user_needs_pay == NO, function ($query) use ($is_user_needs_pay) {
                                                        return $query->OriginalResponse();
                                                    })
                                                    ->when($is_user_needs_pay == YES, function($query) use ($is_user_needs_pay) {
                                                        return $query->BlurResponse();
                                                    })->first();

                          return collect($user_post)->only(['post_id', 'post_unique_id', 'is_user_needs_pay', 'is_paid_post', 'amount_formatted', 'post_file']);
                });

                $user_category_id = CategoryDetail::where(['user_id' => $live_video_details->user_id, 'type' => CATEGORY_TYPE_PROFILE])->pluck('category_id');

                if($user_category_id->isNotEmpty()) {

                    $suggested_user_ids = CategoryDetail::where(['category_id' => $user_category_id])->pluck('user_id');

                    $following_user_ids = array_merge([$request->id], following_users($request->id));

                    $suggested_users = UserPreviewResource::collection(User::whereIn('id', $suggested_user_ids)->whereNotIn('id', $following_user_ids)->take(7)->get());
                }

                $data['user_products'] = $user_products;

                $data['user_posts'] = $user_posts;

                $data['suggested_users'] = $suggested_users ?? [];

            }

            $live_video_details->started_at = common_date($live_video_details->created_at , $this->timezone, 'd M Y h:i A');

            $data['live_video_details'] = $live_video_details;

            return $this->sendResponse($message = '', $code = '', $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


}
