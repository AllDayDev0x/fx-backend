<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User, App\Vod, App\VodFile, App\Models\Category, App\Models\CategoryDetail;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

use App\Http\Resources\{ UserPreviewResource };

class PostRepository {

    /**
     * @method posts_list_response()
     *
     * @uses Format the post response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function posts_list_response($posts, $request) {
        
        $posts = $posts->map(function ($post, $key) use ($request) {

                        $post->is_user_needs_pay = $post->is_paid_post && $post->amount > 0 ? YES : NO;

                        $post->delete_btn_status =  $request->id == $post->user_id ? YES : NO;

                        $post->is_user_liked = $post->postLikes->where('user_id', $request->id)->count() ? YES : NO;

                        $post->is_user_bookmarked = $post->postBookmarks->where('user_id', $request->id)->count() ? YES : NO;

                        $post->share_link = Setting::get('frontend_url')."post/".$post->post_unique_id;

                        $post->payment_info = self::posts_user_payment_check($post, $request);

                        $is_user_needs_pay = $post->payment_info->is_user_needs_pay ?? NO; 

                        $post->is_user_subscribed = $post->payment_info->is_user_subscribed ?? NO; 

                        $post->postFiles = \App\Models\PostFile::where('post_id', $post->post_id)->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                                                    return $q->OriginalResponse();
                                                })
                                                ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {
                                                    return $q->BlurResponse();
                                                })->get();

                        $liked_by = $post->postLikes->first();

                        $post->like_count = $post->postLikes->count();

                        $last_3_like_user_ids = $post->postLikes()->orderByDesc('updated_at')->take(3)->pluck('user_id');

                        $last_3_likes = UserPreviewResource::collection(User::whereIn('id', $last_3_like_user_ids)->get());

                        $post->recent_likes = $last_3_likes ?? emptyObject();

                        $liked_by_formatted = '';

                        if ($liked_by) {
                            
                            $liked_by_formatted = ($liked_by->user_id == $request->id) ? tr('liked_by_you') : (!$liked_by->User  ? "" : 'Liked by '.$liked_by->User->name);

                            if ($post->like_count > 1) {
                                
                                $liked_by_formatted = $liked_by_formatted." ".tr('and')." ".($post->like_count -1)." ".tr('others');
                            }
                        }

                        $post->liked_by_formatted = $liked_by_formatted;

                        $post->publish_time_formatted = common_date($post->publish_time, $request->timezone, 'M d');

                        $post->unsetRelation('postLikes')->unsetRelation('postBookmarks');


                        return $post;
                    });


        return $posts;

    }
    
    /**
     * @method posts_single_response()
     *
     * @uses Format the post response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function posts_single_response($post, $request) {
        
        $post->is_user_needs_pay = $post->is_paid_post;

        $post->delete_btn_status =  $request->id == $post->user_id ? YES : NO;
        
        $post->is_user_liked = $post->postLikes->where('user_id', $request->id)->count() ? YES : NO;

        $post->is_user_bookmarked = $post->postBookmarks->where('user_id', $request->id)->count() ? YES : NO;

        $post->share_link = Setting::get('frontend_url')."post/".$post->post_unique_id;

        $post->payment_info = self::posts_user_payment_check($post, $request);

        $is_user_needs_pay = $post->payment_info->is_user_needs_pay ?? NO; 

        $post->postFiles = \App\Models\PostFile::where('post_id', $post->post_id)->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                                    return $q->OriginalResponse();
                                })
                                ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {
                                    return $q->BlurResponse();
                                })->get();

        $liked_by = $post->postLikes->first();

        $liked_by_formatted = '';

        $post->like_count = $post->postLikes->count();

        if ($liked_by) {

            $liked_by_formatted = ($liked_by->user_id == $request->id) ? tr('liked_by_you') : ($liked_by->User ? tr('liked_by').$liked_by->User->name : "");

            if ($post->like_count > 1) {
                
                $liked_by_formatted = $liked_by_formatted." ".tr('and')." ".($post->like_count -1)." ".tr('others');
            }
        }

        $last_3_like_user_ids = $post->postLikes()->orderByDesc('updated_at')->take(3)->pluck('user_id');

        $last_3_likes = UserPreviewResource::collection(User::whereIn('id', $last_3_like_user_ids)->get());

        $post->recent_likes = $last_3_likes ?? emptyObject();

        $post->liked_by_formatted = $liked_by_formatted;

        $post->publish_time_formatted = common_date($post->publish_time, $request->timezone, 'M d');

        $post->unsetRelation('postLikes')->unsetRelation('postBookmarks')->unsetRelation('user');

        return $post;
    
    }

    /**
     * @method posts_user_payment_check()
     *
     * @uses Check the post payment status for each post
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function posts_user_payment_check($post, $request) {

        $post_user = $post->user ?? [];

        $data['is_user_needs_pay'] = $data['is_free_account'] =  NO;

        $data['post_payment_type'] = $data['payment_text'] = $data['is_user_subscribed'] = "";

        if(!$post_user) {

            goto post_end;

        }

        if($post_user->user_id == $request->id) {

            goto post_end;
        }

        $follower = \App\Models\Follower::where('status', YES)->where('follower_id', $request->id)->where('user_id', $post_user->user_id)->first();

        if(!$follower) {

            $data['is_free_account'] =  NO;
 
        }

        $is_only_wallet_payment = Setting::get('is_only_wallet_payment');

        $user_subscription = \App\Models\UserSubscription::where('user_id', $post_user->id)
            ->when($is_only_wallet_payment == NO, function ($q) use ($is_only_wallet_payment) {
                return $q->OriginalResponse();
            })
            ->when($is_only_wallet_payment == YES, function($q) use ($is_only_wallet_payment) {
                return $q->TokenResponse();
            })->first();

        if(!$user_subscription) {

            $data['is_free_account'] =  YES;
 
        } else {

        }

        $user_subscription_id = $user_subscription->id ?? 0;

        if(!$post->is_paid_post && !$post->amount && $user_subscription) {

            // Check the user has subscribed for this post user plans

            $current_date = Carbon::now()->format('Y-m-d');

            $check_user_subscription_payment = \App\Models\UserSubscriptionPayment::where('user_subscription_id', $user_subscription_id)
                ->where('from_user_id', $request->id)
                ->where('is_current_subscription',YES)
                ->whereDate('expiry_date','>=', $current_date)
                ->where('to_user_id', $post_user->id)
                ->count();
            
            if(!$check_user_subscription_payment) {

                $data['is_user_needs_pay'] = YES;

                $data['post_payment_type'] = POSTS_PAYMENT_SUBSCRIPTION;

                $data['payment_text'] = tr('unlock_subscription_text', $user_subscription->monthly_amount_formatted ?? formatted_amount(0.00));

            }
        }

        $post_user_account_type = $post_user->user_account_type ?? USER_FREE_ACCOUNT;

        $login_user = \App\Models\User::find($request->id);

        $post_payment_check = NO;

        if($post_user_account_type == USER_FREE_ACCOUNT) {

            if($post->is_paid_post && $post->amount > 0) {

                $post_payment_check = YES;

                goto post_payment_check;

            }

        } elseif ($post_user_account_type == USER_PREMIUM_ACCOUNT) {

            // Check the post paid or normal post

            if($post->is_paid_post && $post->amount > 0) {

                $post_payment_check = YES;

                goto post_payment_check;

            }

        } else {

        }

        post_payment_check:

        if($post_payment_check == YES) {

            // Check the user already paid

            $post_payment = \App\Models\PostPayment::where('user_id', $request->id)->where('post_id', $post->post_id)->where('status', PAID)->count();

            if(!$post_payment) {

                $data['is_user_needs_pay'] = YES;

                $data['post_payment_type'] = POSTS_PAYMENT_PPV;

                $data['payment_text'] = tr('unlock_post_text', $post->amount_formatted);
            }
        
            // $data['is_user_subscribed'] = check_user_subscribed($post_user,$request);
       
        }

        post_end:

        return (object)$data;
    
    }

    /**
     * @method content_creators_list_response()
     *
     * @uses Format the post response
     *
     * @created vithya R
     * 
     * @updated vithya R
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function content_creators_list_response($users, $request) {
        
        $users = $users->map(function ($user, $key) use ($request) {

                        $user->user_id = $user->id;

                        $user_category = CategoryDetail::where('user_id', $user->user_id)->where('type', CATEGORY_TYPE_PROFILE)->pluck('category_id');

                        $user->categories = Category::whereIn('categories.id', $user_category ?? 0)->first('name') ?? emptyObject();

                        $latest_post = \App\Models\Post::where('user_id', $user->user_id)
                                                    ->latest()->get();

                        $post_ids = $latest_post->pluck('id')->toArray();

                        $user_files = \App\Models\PostFile::whereIn('post_id', $post_ids)->whereIn('file_type', [POSTS_IMAGE, POSTS_VIDEO])->OriginalResponse()->latest()->take(5)->get();

                        foreach($user_files as $user_file){

                            $post = \App\Models\Post::where('id', $user_file->post_id)->first();

                            $user_file->is_paid_post = $post->is_paid_post ?? 0;
                        }

                        $user->user_files = $user_files;

                        $user->total_posts_count = $user->posts->count();

                        $user->image_count =\App\Models\PostFile::whereIn('post_id', $post_ids)->where('file_type','image')->count();
                        $user->video_count =\App\Models\PostFile::whereIn('post_id', $post_ids)->where('file_type','video')->count();

                        return $user;
                    });


        return $users;

    }

}