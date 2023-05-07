<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use App\Jobs\FollowUserJob;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User, App\Models\Follower;

class FollowersApiController extends Controller
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
     * @method user_suggestions()
     *
     * @uses Follow users & content creators
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
    public function user_suggestions(Request $request) {

        try {

            $following_user_ids = Follower::where('follower_id', $request->id)->where('status', YES)->pluck('user_id')->toArray() ?? [];

            $blocked_user_ids = blocked_users($request->id);
            
            array_push($following_user_ids, $request->id);

            $base_query = $total_query = User::DocumentVerified()->whereNotIn('users.id',$blocked_user_ids)->Approved()->OtherResponse()->whereNotIn('users.id', $following_user_ids)->orderByRaw('RAND()');

            $users = $base_query->whereHas('posts')->skip($this->skip)->take($this->take)->get();

            $data['users'] = $users;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method users_search()
     *
     * @uses Follow users & content creators
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
    public function users_search(Request $request) {

        try {

            // validation start

            $rules = ['key' => 'required'];
            
            $custom_errors = ['key.required' => 'Please enter the username'];

            Helper::custom_validator($request->all(), $rules, $custom_errors);

            $blocked_user_ids = blocked_users($request->id); // the user can see the blocked user to unblock

            $exclude_ids = [$request->id];

            $base_query = $total_query = User::Approved()->OtherResponse()->whereNotIn('users.id', $exclude_ids)->where('users.name', 'like', "%".$request->key."%")->inRandomOrder();

            $users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['users'] = $users;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method follow_users()
     *
     * @uses Follow users & content creators
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
    public function follow_users(Request $request) {

        try {

            DB::beginTransaction();
            
            // Validation start
            // Follower id
            $rules = [
                'user_id' => 'required|exists:users,id'
            ];

            $custom_errors = ['user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end
            if($request->id == $request->user_id) {

                throw new Exception(api_error(136), 136);

            }

            $follow_user = User::where('id', $request->user_id)->first();

            if(!$follow_user) {

                throw new Exception(api_error(135), 135);
            }

            $blocked_user_ids = blocked_users($request->id);

            if(in_array($request->user_id,$blocked_user_ids)) {

                throw new Exception(api_error(165), 165);
            }
           

            // Check the user already following the selected users
            $follower = Follower::where('follower_id', $request->id)->where('user_id', $request->user_id)->first() ??  new Follower;

            if($follower->status == YES) {

                throw new Exception(api_error(137), 137);

            }

            $follower->user_id = $request->user_id;

            $follower->follower_id = $request->id;

            $follower->status = DEFAULT_TRUE;

            $follower->save();

            DB::commit();

            $job_data['follower'] = $follower;

            $job_data['timezone'] = $this->timezone;

            $this->dispatch(new FollowUserJob($job_data));

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = NO;

            $data['total_followers'] = \App\Models\Follower::where('user_id', $request->id)->where('status', YES)->count();

            $data['total_followings'] = \App\Models\Follower::where('follower_id', $request->id)->where('status', YES)->count();

            return $this->sendResponse(api_success(128,$follow_user->username ?? 'user'), $code = 128, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method unfollow_users()
     *
     * @uses Unfollow users/content creators
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
    public function unfollow_users(Request $request) {

        try {

            DB::beginTransaction();

            // Validation start

            $rules = ['user_id' => 'required|exists:users,id'];

            $custom_errors = ['user_id' => api_error(135)];

            Helper::custom_validator($request->all(), $rules, $custom_errors);
            
            // Validation end

            if($request->id == $request->user_id) {

                throw new Exception(api_error(136), 136);

            }

            // Check the user already following the selected users

            $follower = Follower::where('user_id', $request->user_id)->where('follower_id', $request->id)->where('status', YES)->first();

            if(!$follower) {

                throw new Exception(api_error(258), 258);

            }

            $follower->status = FOLLOWER_EXPIRED;

            $follower->save();

            $user_subscription_payment = \App\Models\UserSubscriptionPayment::where('to_user_id', $request->user_id)->where('from_user_id', $request->id)->where('is_current_subscription', YES)->first();

            if($user_subscription_payment) {

                $user_subscription_payment->is_current_subscription = NO;

                $user_subscription_payment->cancel_reason = 'unfollowed';

                $user_subscription_payment->save();
            }

            DB::commit();

            $data['user_id'] = $request->user_id;

            $data['is_follow'] = YES;

            $data['total_followers'] = \App\Models\Follower::where('user_id', $request->id)->where('status', YES)->count();

            $data['total_followings'] = \App\Models\Follower::where('follower_id', $request->id)->where('status', YES)->count();

            return $this->sendResponse(api_success(129), $code = 129, $data);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method followers()
     *
     * @uses Followers List
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
    public function followers(Request $request) {

        try {

            $blocked_user_ids = blocked_users($request->id);

            $base_query = $total_query = Follower::CommonResponse()->whereNotIn('follower_id',$blocked_user_ids)->whereHas('follower')->where('user_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            $followers = \App\Repositories\CommonRepository::followers_list_response($followers, $request);

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method followings()
     *
     * @uses Followings list
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

    public function followings(Request $request) {

        try {

            $blocked_user_ids = blocked_users($request->id);

            $base_query = $total_query = Follower::CommonResponse()->whereNotIn('user_id',$blocked_user_ids)->whereHas('user')->where('follower_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            $followers = \App\Repositories\CommonRepository::followings_list_response($followers, $request);

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method chat_users()
     *
     * @uses chat_users List
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function chat_users(Request $request) {

        try {

            $base_query = $total_query = \App\Models\ChatUser::where('from_user_id', $request->id)->orderBy('chat_users.updated_at', 'desc');

            if ($request->search_key) {
                
                $base_query = $base_query->whereHas('toUser',function($query) use($request) {
                        return $query->where('users.name','LIKE','%'.$request->search_key.'%');
                    });
            }

            $chat_users = $base_query->skip($this->skip)->take($this->take)
                    ->get();

            foreach ($chat_users as $key => $chat_user) {

                $chat_user->message = ".....";

                $chat_user->time_formatted = common_date($chat_user->created_at, $this->timezone, 'd M Y');
            }

            $data['users'] = $chat_users ?? [];

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method chat_messages()
     *
     * @uses chat_messages List
     *
     * @created vithya R
     *
     * @updated vithya R
     *
     * @param
     * 
     * @return JSON response
     *
     */
    public function chat_messages(Request $request) {

        try {

            $base_query = $total_query = \App\Models\ChatMessage::where(function($query) use ($request){
                        $query->where('chat_messages.from_user_id', $request->from_user_id);
                        $query->where('chat_messages.to_user_id', $request->to_user_id);
                    })->orWhere(function($query) use ($request){
                        $query->where('chat_messages.from_user_id', $request->to_user_id);
                        $query->where('chat_messages.to_user_id', $request->from_user_id);
                    });

            $data['total'] = $total_query->count() ?: 0;

            $base_query = $base_query->latest();

            $chat_message = \App\Models\ChatMessage::where('chat_messages.to_user_id', $request->from_user_id)->where('status', NO)->update(['status' => YES]);

            $chat_messages = $base_query->with('chatAssets')->skip($this->skip)->take($this->take)->orderBy('chat_messages.updated_at', 'asc')->get();

            $chat_messages = \App\Repositories\CommonRepository::chat_messages_list_response($chat_messages, $request);

            if($request->device_type == DEVICE_WEB) {

                $chat_messages = array_reverse($chat_messages->toArray());

            }
            
            $data['messages'] = $chat_messages ?? [];

            $data['user'] = $request->id == $request->from_user_id ? \App\Models\User::find($request->to_user_id) : \App\Models\User::find($request->to_user_id);

            $data['is_block_user'] = Helper::is_block_user($request->id, $request->to_user_id);

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method followers()
     *
     * @uses Active Followers List
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
    public function active_followers(Request $request) {

        try {

            $blocked_users = blocked_users($request->id);
            
            $base_query = $total_query = Follower::CommonResponse()->whereNotIn('follower_id',$blocked_users)->whereHas('follower')->where('followers.status',FOLLOWER_ACTIVE)->where('user_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            $followers = \App\Repositories\CommonRepository::followers_list_response($followers, $request);

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }


    /** 
     * @method followers()
     *
     * @uses Expired Followers List
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
    public function expired_followers(Request $request) {

        try {

            $blocked_users = blocked_users($request->id);

            $base_query = $total_query = Follower::CommonResponse()->whereNotIn('follower_id',$blocked_users)->whereHas('follower')->where('followers.status',FOLLOWER_EXPIRED)->where('user_id', $request->id);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            $followers = \App\Repositories\CommonRepository::followers_list_response($followers, $request);

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method active_followings()
     *
     * @uses Active Followers List
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
    public function active_followings(Request $request) {

        try {

            $blocked_users = blocked_users($request->id);
           
            $base_query = $total_query = Follower::CommonResponse()->whereNotIn('user_id',$blocked_users)->whereHas('user')->where('follower_id', $request->id)->where('followers.status', YES);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            $followers = \App\Repositories\CommonRepository::followings_list_response($followers, $request);

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method expired_followings()
     *
     * @uses Expired Followers List
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
    public function expired_followings(Request $request) {

        try {

            $blocked_users = blocked_users($request->id);

            $base_query = $total_query = Follower::CommonResponse()->whereNotIn('user_id',$blocked_users)->whereHas('user')->where('follower_id', $request->id)->where('followers.status', NO);

            $followers = $base_query->skip($this->skip)->take($this->take)->orderBy('followers.created_at', 'desc')->get();

            $followers = \App\Repositories\CommonRepository::followings_list_response($followers, $request);

            $data['followers'] = $followers;

            $data['total'] = $total_query->count() ?: 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }

    /** 
     * @method trending_users()
     *
     * @uses Trending users based on followers
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
    public function trending_users(Request $request) {

        try {

            $trending_user_ids = Follower::where('user_id', '!=',$request->id)
                                    ->select('user_id', DB::raw('COUNT(user_id) as user_id_count'))
                                    ->groupBy('user_id')
                                    ->orderBy('user_id_count','desc')
                                    ->pluck('user_id')->toArray();

            $following_user_ids = following_users($request->id);

            $blocked_user_ids = blocked_users($request->id);

            $trending_user_ids_string = implode(',', $trending_user_ids);

            $base_query = $total_query = User::DocumentVerified()->whereNotIn('users.id',$blocked_user_ids)
                                        ->whereNotIn('users.id',$following_user_ids)
                                        ->Approved()->OtherResponse()
                                        ->whereIn('users.id', $trending_user_ids);

            if ($trending_user_ids_string) {

                $base_query = $base_query->orderByRaw(DB::raw("FIELD(users.id, $trending_user_ids_string)"));
            }

            $users = $base_query->skip($this->skip)->take($this->take)->get();

            $data['trending_users'] = $users;

            $data['total'] = $total_query->count() ?? 0;

            return $this->sendResponse($message = "", $code = "", $data);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        
        }

    }
}