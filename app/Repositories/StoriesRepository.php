<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User;

use App\Models\Story, App\Models\StoryFile;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

class StoriesRepository {

    /**
     * @method stories_list_response()
     *
     * @uses Format the story response
     *
     * @created Jeevan
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function stories_list_response($users, $request) {
        
        $follower_ids = get_follower_ids($request->id);

        $blocked_users = blocked_users($request->id);

        $users = $users->map(function ($user, $key) use ($request, $blocked_users, $follower_ids) {

            $story_ids = Story::Approved()->whereNotIn('stories.user_id',$blocked_users)
                ->whereIn('stories.user_id', $follower_ids)
                ->where('publish_time', '>=', Carbon::now()->subDay())
                ->where('user_id',$user->id)->pluck('id');
                        
            $user->delete_btn_status =  $request->id == $user->user_id ? YES : NO;

            $user->share_link = Setting::get('frontend_url')."stories/".$user->story_unique_id;

            $user->storyFiles = StoryFile::whereIn('story_id', $story_ids)->orderBy('story_files.id', 'desc')->get();

            return $user;
        });

        return $users;

    }
    
    /**
     * @method stories_single_response()
     *
     * @uses Format the story response
     *
     * @created Jeevan
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function stories_single_response($story, $request) {
        
        $story->is_user_needs_pay = $story->is_paid_story && $story->amount > 0 ? YES : NO;

        $is_user_needs_pay = $story->is_user_needs_pay;

        $story->delete_btn_status =  $request->id == $story->user_id ? YES : NO;

        $story->share_link = Setting::get('frontend_url')."stories/".$story->story_unique_id;

        $story->storyFiles = StoryFile::where('story_id', $story->story_id)->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                                                
                                                    return $q->OriginalResponse();
                                                })
                                                ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {


                                                    return $q->BlurResponse();
                                                })->get();

        $story->publish_time_formatted = common_date($story->publish_time, $request->timezone, 'M d');

        return $story;
    
    }

}