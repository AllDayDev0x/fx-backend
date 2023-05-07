<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'user' ,'as' => 'user.', 'middleware' => 'cors'], function() {

    Route::get('get_settings_json', function () {

        $settings_folder = storage_path('public/'.SETTINGS_JSON);

        if(\File::isDirectory($settings_folder)){

        } else {

            \File::makeDirectory($settings_folder, 0777, true, true);

            \App\Helpers\Helper::settings_generate_json();
        }

        $jsonString = file_get_contents(storage_path('app/public/'.SETTINGS_JSON));

        $data = json_decode($jsonString, true);

        return $data;
    
    });

	/***
	 *
	 * User Account releated routs
	 *
	 */
    Route::get('configurations' , 'ApplicationController@configuration_site');

    Route::post('username_validation','Api\UserAccountApiController@username_validation');
    
    Route::any('chat_messages_save', 'ApplicationController@chat_messages_save');

    Route::any('vc_chat_messages_save', 'ApplicationController@vc_chat_messages_save');

    Route::any('ac_chat_messages_save', 'ApplicationController@ac_chat_messages_save');

    Route::any('lv_chat_messages_save', 'ApplicationController@lv_chat_messages_save');

    Route::any('get_notifications_count', 'ApplicationController@get_notifications_count');

    Route::any('lv_viewer_update', 'ApplicationController@lv_viewer_update');

    Route::post('register','Api\UserAccountApiController@register');
    
    Route::post('login','Api\UserAccountApiController@login');

    Route::post('forgot_password', 'Api\UserAccountApiController@forgot_password');

    Route::post('reset_password', 'Api\UserAccountApiController@reset_password');


    Route::post('regenerate_email_verification_code', 'Api\UserAccountApiController@regenerate_email_verification_code');

    Route::post('verify_email', 'Api\UserAccountApiController@verify_email');

    Route::any('static_pages_web', 'ApplicationController@static_pages_web');

    Route::any('static_pages', 'ApplicationController@static_pages_api');

    Route::post('admin_account_details','Api\WalletApiController@admin_account_details');

    Route::post('referral_code_validate', 'Api\ReferralApiController@referral_code_validate');

    Route::group(['middleware' => 'UserApiVal'] , function() {

        Route::post('two_step_auth_login', 'Api\UserAccountApiController@two_step_auth_login');

        Route::post('two_step_auth_resend_code', 'Api\UserAccountApiController@two_step_auth_resend_code');

        Route::post('profile','Api\UserAccountApiController@profile');

        Route::post('update_profile', 'Api\UserAccountApiController@update_profile')->middleware(['CheckEmailVerify']);
        
        Route::post('user_premium_account_check', 'Api\UserAccountApiController@user_premium_account_check');

        Route::post('change_password', 'Api\UserAccountApiController@change_password');

        Route::post('delete_account', 'Api\UserAccountApiController@delete_account');

        Route::post('logout', 'Api\UserAccountApiController@logout');

        Route::post('push_notification_update', 'Api\UserAccountApiController@push_notification_update');

        Route::post('email_notification_update', 'Api\UserAccountApiController@email_notification_update');

        Route::post('notifications_status_update','Api\UserAccountApiController@notifications_status_update');

        Route::post('feature_story_save', 'Api\UserAccountApiController@feature_story_save');

        Route::post('feature_story_delete', 'Api\UserAccountApiController@feature_story_delete');

        Route::post('lists_index','Api\UserAccountApiController@lists_index');
        
        Route::post('payments_index','Api\UserAccountApiController@payments_index');
        
        Route::post('bell_notifications_index','Api\UserAccountApiController@bell_notifications_index');

        Route::post('verified_badge_status', 'Api\UserAccountApiController@verified_badge_status');


        // Cards management start

        Route::post('cards_add', 'Api\UserAccountApiController@cards_add');

        Route::post('cards_list', 'Api\UserAccountApiController@cards_list');

        Route::post('cards_delete', 'Api\UserAccountApiController@cards_delete');

        Route::post('cards_default', 'Api\UserAccountApiController@cards_default');

        Route::post('payment_mode_default', 'Api\UserAccountApiController@payment_mode_default');

        Route::post('documents_list', 'Api\VerificationApiController@documents_list');

        Route::post('documents_save','Api\VerificationApiController@documents_save');

        Route::post('documents_delete','Api\VerificationApiController@documents_delete');

        Route::post('documents_delete_all','Api\VerificationApiController@documents_delete_all');

        Route::post('user_documents_status','Api\VerificationApiController@user_documents_status');

        Route::post('billing_accounts_list','Api\UserAccountApiController@user_billing_accounts_list');

        Route::post('billing_accounts_save','Api\UserAccountApiController@user_billing_accounts_save')->middleware(['CheckEmailVerify']);

        Route::post('billing_accounts_delete','Api\UserAccountApiController@user_billing_accounts_delete');
        
        Route::post('billing_accounts_default','Api\UserAccountApiController@user_billing_accounts_default');

        // Content Creator profile for other users
        Route::post('content_creators_profile','Api\UserAccountApiController@content_creators_profile');

        Route::post('content_creators_posts','Api\UserAccountApiController@content_creators_posts');

        Route::post('content_creators_post_albums','Api\UserAccountApiController@content_creators_post_albums');

        //content creater list
        Route::post('content_creators_list','Api\UserAccountApiController@content_creators_list');
        
        Route::post('category_listing','Api\UserAccountApiController@category_listing');

        Route::post('user_category_listing','Api\UserAccountApiController@user_category_listing');

        Route::post('content_creators_dashboard','Api\UserAccountApiController@content_creators_dashboard');

        Route::post('content_creators_revenue_dashboard','Api\UserAccountApiController@content_creators_revenue_dashboard');

        Route::post('chat_users_save', 'Api\UserAccountApiController@chat_users_save');

        Route::post('wallets_index','Api\WalletApiController@user_wallets_index');

        Route::post('wallets_add_money_by_stripe', 'Api\WalletApiController@user_wallets_add_money_by_stripe');

        Route::post('wallets_add_money_by_paypal', 'Api\WalletApiController@user_wallets_add_money_by_paypal');

        Route::post('wallets_add_money_by_bank_account','Api\WalletApiController@user_wallets_add_money_by_bank_account');
       
        Route::post('wallets_history','Api\WalletApiController@user_wallets_history');

        Route::post('wallets_history_for_add','Api\WalletApiController@user_wallets_history_for_add');

        Route::post('wallets_history_for_sent','Api\WalletApiController@user_wallets_history_for_sent');

        Route::post('wallets_history_for_received','Api\WalletApiController@user_wallets_history_for_received');

        Route::post('wallets_payment_view','Api\WalletApiController@user_wallets_payment_view');

        Route::post('wallets_send_money','Api\WalletApiController@user_wallets_send_money');

        Route::post('subscriptions_index','Api\SubscriptionApiController@subscriptions_index');

        Route::post('subscriptions_view','Api\SubscriptionApiController@subscriptions_view');

        Route::post('subscriptions_payment_by_card','Api\SubscriptionApiController@subscriptions_payment_by_card');

        Route::post('subscriptions_history','Api\SubscriptionApiController@subscriptions_history');

        Route::post('subscriptions_autorenewal_status','Api\SubscriptionApiController@subscriptions_autorenewal_status');

        Route::post('subscription_payments_autorenewal','ApplicationController@subscription_payments_autorenewal');

        // Withdrawls start

        Route::post('withdrawals_index','Api\WalletApiController@user_withdrawals_index');
        
        Route::post('withdrawals_view','Api\WalletApiController@user_withdrawals_view');

        Route::post('withdrawals_search','Api\WalletApiController@user_withdrawals_search');

        Route::post('withdrawals_send_request','Api\WalletApiController@user_withdrawals_send_request');

        Route::post('withdrawals_cancel_request','Api\WalletApiController@user_withdrawals_cancel_request');

        Route::post('withdrawals_check','Api\WalletApiController@user_withdrawals_check');

    });

    Route::group(['middleware' => 'UserApiVal'] , function() {

        Route::group(['middleware' => 'CheckDocumentVerify'] , function() {

            Route::post('user_products','Api\UserProductApiController@user_products_index');

            Route::post('user_products_save','Api\UserProductApiController@user_products_save');

            Route::post('user_products_view','Api\UserProductApiController@user_products_view');

            Route::post('user_products_delete','Api\UserProductApiController@user_products_delete');

            Route::post('user_products_set_visibility','Api\UserProductApiController@user_products_set_visibility');

            Route::post('user_products_update_availability','Api\UserProductApiController@user_products_update_availability');

            Route::post('product_categories','Api\UserProductApiController@product_categories');

            Route::post('product_sub_categories','Api\UserProductApiController@product_sub_categories');

            Route::post('user_products_search','Api\UserProductApiController@user_products_search');

            Route::post('user_product_pictures' , 'Api\UserProductApiController@user_product_pictures');

            Route::post('user_product_pictures_save','Api\UserProductApiController@user_product_pictures_save');

            Route::post('user_product_pictures_delete','Api\UserProductApiController@user_product_pictures_delete');

            Route::post('user_products_orders_list','Api\UserProductApiController@user_products_orders_list');

            Route::post('ecommerce_home','Api\UserProductApiController@ecommerce_home');

            Route::post('user_products_view_for_others','Api\UserProductApiController@user_products_view_for_others');

            Route::post('orders_list_for_others','Api\UserProductApiController@orders_list_for_others');

            Route::post('orders_view_for_others','Api\UserProductApiController@orders_view_for_others');

            Route::post('order_payments_list','Api\UserProductApiController@order_payments_list');

            Route::post('delivery_addresses_list','Api\UserProductApiController@delivery_addresses_list');

            Route::post('carts_list','Api\UserProductApiController@carts_list');

            Route::post('carts_save','Api\UserProductApiController@carts_save');

            Route::post('carts_remove','Api\UserProductApiController@carts_remove');

            Route::post('orders_payment_by_wallet', 'Api\UserProductApiController@orders_payment_by_wallet');

            Route::post('posts_for_owner','Api\PostsApiController@posts_for_owner');

            Route::post('posts_save_for_owner','Api\PostsApiController@posts_save_for_owner')->middleware(['CheckEmailVerify']);

            Route::post('posts_view_for_owner','Api\PostsApiController@posts_view_for_owner');

            Route::post('posts_delete_for_owner','Api\PostsApiController@posts_delete_for_owner');

            Route::post('post_files_upload','Api\PostsApiController@post_files_upload');

            Route::post('post_files_remove','Api\PostsApiController@post_files_remove');

            //stories

            Route::post('stories_for_creators','Api\StoriesApiController@stories_for_creators');
            
            Route::post('stories_list','Api\StoriesApiController@stories_list');

            Route::post('stories_save','Api\StoriesApiController@stories_save')->middleware(['CheckEmailVerify']);

            Route::post('story_files_upload','Api\StoriesApiController@story_files_upload');

            Route::post('story_files_remove','Api\StoriesApiController@story_files_remove');

            Route::post('stories_view','Api\StoriesApiController@stories_view');

            Route::post('stories_delete','Api\StoriesApiController@stories_delete');

            Route::post('stories_home','Api\StoriesApiController@stories_home');

            Route::post('stories_single_view','Api\StoriesApiController@stories_single_view');

        });
    });

    Route::group(['middleware' => 'UserApiVal'] , function() {

        // Followers and Followings list for content creators
        Route::post('followers', 'Api\FollowersApiController@followers');

        Route::post('followings', 'Api\FollowersApiController@followings');

        Route::post('active_followers', 'Api\FollowersApiController@active_followers');

        Route::post('expired_followers', 'Api\FollowersApiController@expired_followers');

        Route::post('active_followings', 'Api\FollowersApiController@active_followings');

        Route::post('expired_followings', 'Api\FollowersApiController@expired_followings');


        Route::post('follow_users','Api\FollowersApiController@follow_users');

        Route::post('unfollow_users','Api\FollowersApiController@unfollow_users');

        Route::post('chat_assets', 'Api\ChatApiController@chat_assets_index');

        Route::post('chat_assets_save', 'Api\ChatApiController@chat_assets_save');

        Route::post('chat_assets_delete', 'Api\ChatApiController@chat_assets_delete');
        

        Route::post('chat_assets_payment_by_stripe', 'Api\ChatApiController@chat_assets_payment_by_stripe');

        Route::post('chat_assets_payment_by_wallet', 'Api\ChatApiController@chat_assets_payment_by_wallet');

        Route::post('chat_assets_payment_by_paypal', 'Api\ChatApiController@chat_assets_payment_by_paypal');

        Route::post('chat_asset_payments', 'Api\ChatApiController@chat_asset_payments');

        Route::post('chat_asset_payments_view', 'Api\ChatApiController@chat_asset_payments_view');

        Route::post('chat_users_search','Api\ChatApiController@chat_users_search');

        Route::post('chat_messages_search','Api\ChatApiController@chat_messages_search');

        Route::post('chat_bulk_messages','Api\ChatApiController@chat_bulk_messages');


        Route::post('video_call_requests', 'Api\VideoCallApiController@video_call_requests');
        
        Route::post('video_call_requests_view', 'Api\VideoCallApiController@video_call_requests_view');

        Route::post('video_call_requests_save', 'Api\VideoCallApiController@video_call_requests_save');

        Route::post('video_call_requests_accept', 'Api\VideoCallApiController@video_call_requests_accept');

        Route::post('video_call_requests_reject', 'Api\VideoCallApiController@video_call_requests_reject');

        Route::post('video_call_requests_join', 'Api\VideoCallApiController@video_call_requests_join');

        Route::post('video_call_requests_end', 'Api\VideoCallApiController@video_call_requests_end');

        Route::post('video_call_amount_update', 'Api\VideoCallApiController@video_call_amount_update');

        Route::post('video_call_payment_by_stripe', 'Api\VideoCallApiController@video_call_payment_by_stripe');

        Route::post('video_call_payment_by_paypal', 'Api\VideoCallApiController@video_call_payment_by_paypal');

        Route::post('video_call_payment_by_wallet', 'Api\VideoCallApiController@video_call_payment_by_wallet');

        Route::post('video_call_chat', 'Api\VideoCallApiController@video_call_chat');

        Route::post('user_video_call_requests', 'Api\VideoCallApiController@user_video_call_requests');

        Route::post('user_video_call_history', 'Api\VideoCallApiController@user_video_call_history');

        Route::post('model_video_call_requests', 'Api\VideoCallApiController@model_video_call_requests');

        Route::post('model_video_call_history', 'Api\VideoCallApiController@model_video_call_history');

        //Audio call

        Route::post('audio_call_requests', 'Api\AudioCallApiController@audio_call_requests');
        
        Route::post('audio_call_requests_view', 'Api\AudioCallApiController@audio_call_requests_view');

        Route::post('audio_call_requests_save', 'Api\AudioCallApiController@audio_call_requests_save');

        Route::post('audio_call_requests_accept', 'Api\AudioCallApiController@audio_call_requests_accept');

        Route::post('audio_call_requests_reject', 'Api\AudioCallApiController@audio_call_requests_reject');

        Route::post('audio_call_requests_join', 'Api\AudioCallApiController@audio_call_requests_join');

        Route::post('audio_call_requests_end', 'Api\AudioCallApiController@audio_call_requests_end');

        Route::post('audio_call_amount_update', 'Api\AudioCallApiController@audio_call_amount_update');

        Route::post('audio_call_payment_by_stripe', 'Api\AudioCallApiController@audio_call_payment_by_stripe');

        Route::post('audio_call_payment_by_paypal', 'Api\AudioCallApiController@audio_call_payment_by_paypal');

        Route::post('audio_call_payment_by_wallet', 'Api\AudioCallApiController@audio_call_payment_by_wallet');

        Route::post('audio_call_chat', 'Api\AudioCallApiController@audio_call_chat');

        Route::post('user_audio_call_requests', 'Api\AudioCallApiController@user_audio_call_requests');

        Route::post('user_audio_call_history', 'Api\AudioCallApiController@user_audio_call_history');

        Route::post('model_audio_call_requests', 'Api\AudioCallApiController@model_audio_call_requests');

        Route::post('model_audio_call_history', 'Api\AudioCallApiController@model_audio_call_history');

        Route::post('audio_call_charges', 'Api\AudioCallApiController@audio_call_charges');

        Route::post('video_call_charges', 'Api\VideoCallApiController@video_call_charges');

    });
    
    Route::post('other_profile_posts','Api\UserAccountApiController@other_profile_posts');

    Route::post('other_profile','Api\UserAccountApiController@other_profile');
        
    Route::post('other_model_product_list','Api\UserProductApiController@other_model_product_list');

    Route::group(['middleware' => 'UserApiVal'] , function() {

        Route::post('user_subscriptions','Api\UserAccountApiController@user_subscriptions');

        Route::post('user_subscriptions_payment_by_stripe','Api\UserAccountApiController@user_subscriptions_payment_by_stripe');

        Route::post('user_subscriptions_payment_by_wallet','Api\UserAccountApiController@user_subscriptions_payment_by_wallet');

        Route::post('user_subscriptions_history','Api\UserAccountApiController@user_subscriptions_history');

        Route::post('user_subscriptions_autorenewal','Api\UserAccountApiController@user_subscriptions_autorenewal');


        Route::post('home','Api\PostsApiController@home')->middleware(['UserApiVal']);

        Route::post('posts_search','Api\PostsApiController@posts_search');

        Route::post('posts_view_for_others','Api\PostsApiController@posts_view_for_others');

        Route::post('users_search', 'Api\FollowersApiController@users_search');

        Route::post('user_suggestions', 'Api\FollowersApiController@user_suggestions');

        Route::post('posts_payment_by_wallet','Api\PostsApiController@posts_payment_by_wallet');

        Route::post('posts_payment_by_stripe','Api\PostsApiController@posts_payment_by_stripe');

        Route::post('post_comments','Api\PostsApiController@post_comments');

        Route::post('post_comments_save','Api\PostsApiController@post_comments_save');
        
        Route::post('post_comments_delete','Api\PostsApiController@post_comments_delete');


        Route::post('post_bookmarks','Api\PostsApiController@post_bookmarks');

        Route::post('post_bookmarks_photos','Api\PostsApiController@post_bookmarks_photos');

        Route::post('post_bookmarks_videos','Api\PostsApiController@post_bookmarks_videos');

        Route::post('post_bookmarks_audio','Api\PostsApiController@post_bookmarks_audio');

        Route::post('post_bookmarks_save','Api\PostsApiController@post_bookmarks_save');
        
        Route::post('post_bookmarks_delete','Api\PostsApiController@post_bookmarks_delete');


        Route::post('post_likes','Api\PostsApiController@post_likes');

        Route::post('post_likes_save','Api\PostsApiController@post_likes_save');
        
        Route::post('post_likes_delete','Api\PostsApiController@post_likes_delete');

        Route::post('fav_users','Api\PostsApiController@fav_users');

        Route::post('fav_users_save','Api\PostsApiController@fav_users_save');
        
        Route::post('fav_users_delete','Api\PostsApiController@fav_users_delete');

        // Route::post('post_likes','Api\PostsApiController@post_likes');

        // Route::post('post_likes_save','Api\PostsApiController@post_likes_save');
        
        // Route::post('post_likes_delete','Api\PostsApiController@post_likes_delete');
        

        Route::post('tips_payment_by_stripe','Api\PostsApiController@tips_payment_by_stripe');
        
        Route::post('tips_payment_by_wallet','Api\PostsApiController@tips_payment_by_wallet');

        Route::post('chat_users','Api\FollowersApiController@chat_users');

        Route::post('chat_messages','Api\FollowersApiController@chat_messages')->middleware(['CheckEmailVerify']);

        Route::post('block_users_save','Api\UserAccountApiController@block_users_save');

        Route::post('block_users','Api\UserAccountApiController@block_users');

        Route::post('report_posts_save','Api\PostsApiController@report_posts_save');

        Route::post('report_posts','Api\PostsApiController@report_posts');


        Route::post('user_subscriptions_payment_by_paypal','Api\UserAccountApiController@user_subscriptions_payment_by_paypal');

        Route::post('tips_payment_by_paypal','Api\PostsApiController@tips_payment_by_paypal');

        Route::post('posts_payment_by_paypal','Api\PostsApiController@posts_payment_by_paypal');

        Route::post('user_tips_history','Api\UserAccountApiController@user_tips_history');

        Route::post('user_subscriptions_payment_by_paypal_direct','Api\PaypalPaymentController@user_subscriptions_payment_by_paypal_direct');

        Route::get('user_subscriptions_payment/cancel', 'Api\PaypalPaymentController@user_subscriptions_payment_cancel')->name('user_subscriptions_payment.cancel');
            
        Route::get('user_subscriptions_payment/success', 'Api\PaypalPaymentController@user_subscriptions_payment_success')->name('user_subscriptions_payment.success');

        Route::post('tips_payment_by_paypal_direct','Api\PaypalPaymentController@tips_payment_by_paypal_direct');

        Route::get('tips_payment/cancel', 'Api\PaypalPaymentController@tips_payment_cancel')->name('tips_payment.cancel');
            
        Route::get('tips_payment/success', 'Api\PaypalPaymentController@tips_payment_success')->name('tips_payment.success');

        Route::post('posts_payment_by_paypal_direct','Api\PaypalPaymentController@posts_payment_by_paypal_direct');

        Route::get('posts_payment/cancel', 'Api\PaypalPaymentController@posts_payment_cancel')->name('posts_payment.cancel');
            
        Route::get('posts_payment/success', 'Api\PaypalPaymentController@posts_payment_success')->name('posts_payment.success');

        Route::post('tips_payment_by_ccbill', 'Api\CCBillPaymentController@tips_payment_by_ccbill');

        Route::post('posts_payment_by_ccbill', 'Api\CCBillPaymentController@posts_payment_by_ccbill');

        Route::post('user_subscriptions_payment_by_ccbill', 'Api\CCBillPaymentController@subscriptions_payment_by_ccbill');

        Route::post('post_comment_likes','Api\PostsApiController@post_comment_likes');

        Route::post('post_comment_likes_save','Api\PostsApiController@post_comment_likes_save');

        Route::post('post_comment_replies','Api\PostsApiController@post_comment_replies');

        Route::post('post_comments_replies_save','Api\PostsApiController@post_comments_replies_save');
        
        Route::post('post_comments_replies_delete','Api\PostsApiController@post_comments_replies_delete');

        Route::post('explore','Api\PostsApiController@explore');

        Route::post('trending_users', 'Api\FollowersApiController@trending_users');

        // Live videos

        Route::post('live_videos_broadcast_start', 'Api\LiveVideoApiController@live_videos_broadcast_start');

        Route::post('live_videos_check_streaming', 'Api\LiveVideoApiController@live_videos_check_streaming');

        Route::any('live_videos_viewer_update', 'Api\LiveVideoApiController@live_videos_viewer_update'); // Don't change to post.

        Route::post('live_videos_snapshot_save', 'Api\LiveVideoApiController@live_videos_snapshot_save');

        Route::post('live_videos_broadcast_stop', 'Api\LiveVideoApiController@live_videos_broadcast_stop');

        Route::post('live_videos_erase_old_streamings', 'Api\LiveVideoApiController@live_videos_erase_old_streamings');

        Route::post('live_videos_owner_list', 'Api\LiveVideoApiController@live_videos_owner_list');

        Route::post('live_videos_owner_view', 'Api\LiveVideoApiController@live_videos_owner_view');

        Route::post('live_videos_search', 'Api\LiveVideoApiController@live_videos_search');

        Route::post('live_videos', 'Api\LiveVideoApiController@live_videos');

        Route::post('live_videos_view', 'Api\LiveVideoApiController@live_videos_view');

        Route::post('live_videos_viewers_list', 'Api\LiveVideoApiController@live_videos_viewers_list');

        Route::post('live_videos_payment_by_card', 'Api\LiveVideoApiController@live_videos_payment_by_card');

        Route::post('live_videos_payment_by_paypal', 'Api\LiveVideoApiController@live_videos_payment_by_paypal');

        Route::post('live_videos_payment_by_wallet', 'Api\LiveVideoApiController@live_videos_payment_by_wallet');

        Route::post('live_videos_payment_history', 'Api\LiveVideoApiController@live_videos_payment_history');
        
        Route::post('live_video_chat_messages','Api\LiveVideoApiController@live_video_chat_messages');

        // New Live Video Api's Start

        Route::post('live_videos_list', 'Api\LiveVideoApiController@live_videos_list');

        Route::post('single_live_video_view', 'Api\LiveVideoApiController@single_live_video_view');

        Route::post('popular_live_videos', 'Api\LiveVideoApiController@popular_live_videos');
        
        Route::post('recommended_live_videos', 'Api\LiveVideoApiController@recommended_live_videos');

        // New Live Video Api's End

        Route::post('referral_code', 'Api\ReferralApiController@referral_code');

        Route::post('tips_payment_by_coinpayment', 'Api\CoinPaymentController@tips_payment_by_coinpayment');

        Route::post('posts_payment_by_coinpayment', 'Api\CoinPaymentController@posts_payment_by_coinpayment');

        Route::post('user_subscriptions_payment_by_coinpayment', 'Api\CoinPaymentController@subscriptions_payment_by_coinpayment');

        Route::post('live_videos_payment_by_coinpayment', 'Api\CoinPaymentController@live_videos_payment_by_coinpayment');
    

        Route::post('categories_list','Api\CategoryApiController@categories_list');

        Route::post('categories_view','Api\CategoryApiController@categories_view');

        Route::post('post_categories_list','Api\PostsApiController@post_categories_list');

        Route::post('post_categories_view','Api\PostsApiController@post_categories_view');

        Route::post('post_category_listing','Api\PostsApiController@post_category_listing');
        
        Route::post('user_tips_history','Api\UserAccountApiController@user_tips_history');

        Route::post('vod_videos_for_owner','Api\VodApiController@vod_videos_for_owner');

        Route::post('vod_videos_save_for_owner','Api\VodApiController@vod_videos_save_for_owner');

        Route::post('vod_videos_view_for_owner','Api\VodApiController@vod_videos_view_for_owner');

        Route::post('vod_videos_delete_for_owner','Api\VodApiController@vod_videos_delete_for_owner');

        Route::post('vod_videos_files_upload','Api\VodApiController@vod_videos_files_upload');

        Route::post('vod_videos_files_remove','Api\VodApiController@vod_videos_files_remove');

        Route::post('vod_videos_payment_by_wallet','Api\VodApiController@vod_videos_payment_by_wallet');

        Route::post('vod_videos_payment_by_stripe','Api\VodApiController@vod_videos_payment_by_stripe');

        Route::post('vod_videos_payment_by_paypal','Api\VodApiController@vod_videos_payment_by_paypal');

        Route::post('vod_videos_payment_by_paypal_direct','Api\VodApiController@vod_videos_payment_by_paypal_direct');

        Route::post('vod_videos_payment_by_ccbill', 'Api\VodApiController@vod_videos_payment_by_ccbill');

        Route::post('vod_videos_home','Api\VodApiController@vod_videos_home');

        Route::post('vod_videos_search','Api\VodApiController@vod_videos_search');

        Route::post('vod_videos_view_for_others','Api\VodApiController@vod_videos_view_for_others');

        Route::post('vod_videos_transaction_users','Api\VodApiController@vod_videos_transaction_users');

        Route::post('vod_videos_transaction_content_creator','Api\VodApiController@vod_videos_transaction_content_creator');

        Route::post('vod_videos_transaction_view','Api\VodApiController@vod_videos_transaction_view');

        Route::post('promo_code_index', 'Api\PromocodeApiController@promo_code_index');

        Route::post('promo_code_save', 'Api\PromocodeApiController@promo_code_save');

        Route::post('promo_code_delete', 'Api\PromocodeApiController@promo_code_delete');

        Route::post('login_session_index', 'Api\UserAccountApiController@login_session_index');

        Route::post('two_step_auth_update', 'Api\UserAccountApiController@two_step_auth_update');

        Route::post('report_reasons_index', 'Api\PostsApiController@report_reasons_index');

        Route::post('orders_payment_by_stripe', 'Api\UserProductApiController@orders_payment_by_stripe');

        Route::post('orders_payment_by_paypal', 'Api\UserProductApiController@orders_payment_by_paypal');

        Route::post('order_status_update','Api\UserProductApiController@order_status_update'); 
        Route::post('order_cancel','Api\UserProductApiController@order_cancel');

        Route::post('order_product_cancel','Api\UserProductApiController@order_product_cancel');

        Route::post('hashtags_index','Api\PostsApiController@hashtags_index');

        Route::post('hashtags_search','Api\PostsApiController@hashtags_search');

    });


    Route::get('is_demo_enable_api', 'Api\DemoController@is_demo_enable_api');

    Route::get('demo_control_status_update_api', 'Api\DemoController@demo_control_status_update_api');

    Route::post('login_session_delete', 'Api\UserAccountApiController@login_session_delete');

    Route::post('login_session_delete_all', 'Api\UserAccountApiController@login_session_delete_all');

    Route::get('user_demo_update', 'Api\DemoController@user_demo_update');

    Route::get('user_demo_login_check', 'Api\DemoController@user_demo_login_check');

    Route::get('admin_demo_update', 'Api\DemoController@admin_demo_update');

    Route::get('admin_demo_login_check', 'Api\DemoController@admin_demo_login_check');

});



