<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User, App\Models\LiveVideo, App\LiveGroup, App\Models\Viewer, App\Models\CategoryDetail;

class LiveVideoRepository {

    /**
     * @method live_videos_common_query()
     *
     * @uses used to check the conditions to fetch the live videos
     *
     * @created Bhawya N
     * 
     * @updated Bhawya N
     *
     * @param object $base_query, object $request
     *
     * @return object $base_query
     */

    public static function live_videos_common_query($request, $base_query,$type = '') {

        if($request->id) {

            // omit the live videos - blocked by you, who blocked you & your live videos

            $block_user_ids = blocked_users($request->id);

            // groups based videos

            if($block_user_ids) {

                $base_query = $base_query->whereNotIn('live_videos.user_id', $block_user_ids);
            }

        }

        if($type) {

            if($type == TYPE_PRIVATE) {

                $follower_ids = get_follower_ids($request->id);

                $base_query = $base_query->whereIn('live_videos.user_id', $follower_ids);
            }

            $base_query = $base_query->where('live_videos.type', $type);
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

        return $base_query;

    }

    /**
     * @method live_videos_list_response()
     *
     * @uses used to format the live videos response
     *
     * @created Bhawya N
     * 
     * @updated Bhawya N
     *
     * @param object $live_videos, object $request
     *
     * @return object $live_videos
     */

    public static function live_videos_list_response($live_videos, $request) {

        $user_following_ids = get_follower_ids($request->id);

        foreach ($live_videos as $key => $live_video) {

            $live_video->post_type = LIVE_VIDEO;

            $live_video->share_link = Setting::get('ANGULAR_URL');

            if($request->id == $live_video->user_id) {

                $live_video->is_user_needs_to_pay =  NO;

            } else {

                $live_video->is_user_needs_to_pay = self::live_videos_check_payment($live_video, $request->id); 

            }

            $recent_viewer_ids = Viewer::where('live_video_id', $live_video->id)->orderBy('created_at', 'desc')->take(3)->pluck('user_id');

            $live_video->recent_viewers = User::whereIn('id', $recent_viewer_ids)->get();

            $block_users = blocked_users($live_video->user_id);

            $live_video->started_at = common_date($live_video->created_at, $request->timezone, "d M Y h:i A");

            //Don't move this to top

            if($user_following_ids && $live_video->type == TYPE_PRIVATE || in_array($request->id, $block_users)) {

                if(!in_array($live_video->user_id, $user_following_ids)) {
                    
                    // unset($key);

                    $live_videos->forget($key); 

                }
            }

        }

        return $live_videos;
    
    }

    public static function live_videos_check_payment($live_video, $user_id) {

        $is_user_needs_to_pay = NO;

        if($live_video->payment_status == YES && $live_video->amount > 0) {

            $is_user_needs_to_pay = \App\Models\LiveVideoPayment::where('live_video_viewer_id', $user_id)->where('status', PAID_STATUS)->where('live_video_id', $live_video->live_video_id)->count() ? NO : YES;

        }

        return $is_user_needs_to_pay;
    }


    public static function video_call_payment_check($video_call_request) {

        $is_user_needs_to_pay = NO;

        if($video_call_request->amount > 0) {

            $is_user_needs_to_pay = \App\Models\VideoCallPayment::where('video_call_request_id', $video_call_request->id)->where('user_id',$video_call_request->user_id)->where('status', PAID_STATUS)->count() ? NO : YES;

        }

        return $is_user_needs_to_pay;
    }

    /**
     * @method popular_live_videos()
     *
     * @uses Popular live videos response
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $live_videos, object $request
     *
     * @return object $live_videos
     */

    public static function popular_live_videos($request) {

        $live_video_ids = LiveVideo::CurrentLive()
                            ->where('user_id', '!=',$request->id)
                            ->where('viewer_cnt', '>=', 10)
                            ->pluck('id');

        return $live_video_ids;
    
    }

    /**
     * @method live_videos_index_list_response()
     *
     * @uses to formate the live videos list api response & add conditional fields.
     *
     * @created Karthick
     * 
     * @updated
     *
     * @param user_id, timezone
     *
     * @return object $live_videos
     */

    public static function live_videos_index_list_response($live_videos, $user_id, $timezone = "America/New_York") {

        foreach ($live_videos as $key => $live_video) {

            $live_video->post_type = LIVE_VIDEO;

            $live_video->share_link = Setting::get('ANGULAR_URL');

            if($user_id == $live_video->user_id) {

                $live_video->is_user_needs_to_pay =  NO;

            } else {

                $live_video->is_user_needs_to_pay = self::live_videos_check_payment($live_video, $user_id); 

            }

            $live_video->started_at = common_date($live_video->created_at, $timezone, "d M Y h:i A");

            $recent_viewer_ids = Viewer::where('live_video_id', $live_video->id)->orderBy('created_at', 'desc')->take(3)->pluck('user_id');

            $live_video->recent_viewers = User::whereIn('id', $recent_viewer_ids)->get();

        }

        return $live_videos;
    
    }

    /**
     * @method apply_filters()
     *
     * @uses to apply filters for live videos
     *
     * @created Karthick
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $base_query
     */

    public static function apply_filters($base_query, $request) {

        if($request->filled('category_id')) {
                
            $user_ids = CategoryDetail::where(['category_id' => $request->category_id, 'post_id' => 0])->pluck('user_id');

            $base_query = $base_query->whereIn('user_id', $user_ids);
        }

        if($request->filled('search_key')) {

            $live_video_ids = LiveVideo::when($request->type, function($query) use($request) {
                                    $query->where('type', $request->type);
                                })->whereHas('user', function($query) use($request){
                                    $query->where('name', 'LIKE', '%'.$request->search_key.'%');
                                })->orWhere(function($query) use ($request) {
                                    $query->where('title', 'LIKE', '%'.$request->search_key.'%')
                                    ->orWhere('description', 'LIKE', '%'.$request->search_key.'%');
                                })->pluck('id');

            $base_query = $base_query->whereIn('id', $live_video_ids);
        }

        return $base_query;
    
    }

}