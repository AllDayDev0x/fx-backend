<?php

Route::group(['middleware' => 'web'], function() {

    Route::group(['as' => 'admin.', 'prefix' => 'admin'], function(){

        Route::get('clear-cache', function() {

            $exitCode = Artisan::call('config:cache');

            return back();

        })->name('clear-cache');

        Route::get('login', 'Auth\AdminLoginController@showLoginForm')->name('login');

        Route::post('login', 'Auth\AdminLoginController@login')->name('login.post');

        Route::get('logout', 'Auth\AdminLoginController@logout')->name('logout');

        /***
         *
         * Admin Account releated routes
         *
         */

        Route::get('profile', 'Admin\AdminAccountController@profile')->name('profile');

        Route::post('profile/save', 'Admin\AdminAccountController@profile_save')->name('profile.save');

        Route::post('change/password', 'Admin\AdminAccountController@change_password')->name('change.password');

        Route::get('/', 'Admin\AdminRevenueController@main_dashboard')->name('dashboard');
        
        // Users CRUD Operations

        Route::get('users', 'Admin\AdminUserController@users_index')->name('users.index');

        Route::get('users/create', 'Admin\AdminUserController@users_create')->name('users.create');

        Route::get('users/edit', 'Admin\AdminUserController@users_edit')->name('users.edit');

        Route::post('users/save', 'Admin\AdminUserController@users_save')->name('users.save');

        Route::get('users/view', 'Admin\AdminUserController@users_view')->name('users.view');

        Route::get('users/delete', 'Admin\AdminUserController@users_delete')->name('users.delete');

        Route::get('users/status', 'Admin\AdminUserController@users_status')->name('users.status');

        Route::get('users/verify', 'Admin\AdminUserController@users_verify_status')->name('users.verify');

        Route::get('users/verify_badge', 'Admin\AdminUserController@users_verify_badge_status')->name('users.verify_badge');

        Route::get('users/excel','Admin\AdminUserController@users_excel')->name('users.excel');

        Route::post('/users/bulk_action', 'Admin\AdminUserController@users_bulk_action')->name('users.bulk_action');

        Route::get('/users/billing_accounts','Admin\AdminUserController@billing_accounts_index')->name('users.billing_accounts');
        
        Route::get('/users/content_creator_upgrade','Admin\AdminUserController@content_creator_upgrade')->name('users.content_creator_upgrade');


        Route::get('user_subscription_payments/index','Admin\AdminUserController@user_subscription_payments')->name('users_subscriptions.index');

        Route::get('user_subscriptions_payment/view','Admin\AdminUserController@user_subscriptions_payment_view')->name('user_subscriptions.view');

        Route::get('users/carts', 'Admin\AdminUserController@carts_list')->name('users.carts');

        Route::get('users/remove', 'Admin\AdminUserController@carts_remove')->name('users.carts.remove');

        //user documents 

        Route::get('user-documents', 'Admin\AdminUserController@user_documents_index')->name('user_documents.index');

        Route::get('user-document', 'Admin\AdminUserController@user_documents_view')->name('user_documents.view');
        
        Route::get('user-documents/verify', 'Admin\AdminUserController@user_documents_verify')->name('user_documents.verify');

        Route::get('users/upgrade_account', 'Admin\AdminUserController@user_upgrade_account')->name('users.upgrade_account');

        //followers
         Route::get('followers' , 'Admin\AdminUserController@user_followers')->name('user_followers');

        Route::get('followings', 'Admin\AdminUserController@user_followings')->name('user_followings');

        //user products CRUD Operations

        Route::get('products', 'Admin\AdminProductController@user_products_index')->name('user_products.index');

        Route::get('products/create', 'Admin\AdminProductController@user_products_create')->name('user_products.create');

        Route::get('products/edit', 'Admin\AdminProductController@user_products_edit')->name('user_products.edit');

        Route::post('products/save', 'Admin\AdminProductController@user_products_save')->name('user_products.save');

        Route::get('products/view', 'Admin\AdminProductController@user_products_view')->name('user_products.view');

        Route::get('products/delete', 'Admin\AdminProductController@user_products_delete')->name('user_products.delete');

        Route::get('products/status', 'Admin\AdminProductController@user_products_status')->name('user_products.status');

        Route::get('products/dashboard', 'Admin\AdminProductController@user_products_dashboard')->name('user_products.dashboard');

        Route::get('order_products','Admin\AdminProductController@order_products')->name('order_products');

        // Document CRUD Operations

        Route::get('documents', 'Admin\AdminLookupController@documents_index')->name('documents.index');

        Route::get('documents/create', 'Admin\AdminLookupController@documents_create')->name('documents.create');

        Route::get('documents/edit', 'Admin\AdminLookupController@documents_edit')->name('documents.edit');

        Route::post('documents/save', 'Admin\AdminLookupController@documents_save')->name('documents.save');

        Route::get('documents/view', 'Admin\AdminLookupController@documents_view')->name('documents.view');

        Route::get('documents/delete', 'Admin\AdminLookupController@documents_delete')->name('documents.delete');

        Route::get('documents/status', 'Admin\AdminLookupController@documents_status')->name('documents.status');

        // Documents end

        //posts start

        Route::get('posts' , 'Admin\AdminPostController@posts_index')->name('posts.index');

        Route::get('posts/delete', 'Admin\AdminPostController@posts_delete')->name('posts.delete');

        Route::get('posts/create', 'Admin\AdminPostController@posts_create')->name('posts.create');

        Route::get('posts/view', 'Admin\AdminPostController@posts_view')->name('posts.view');

        Route::post('posts/save', 'Admin\AdminPostController@posts_save')->name('posts.save');

        Route::get('posts/edit', 'Admin\AdminPostController@posts_edit')->name('posts.edit');

        Route::get('posts/dashboard', 'Admin\AdminPostController@posts_dashboard')->name('posts.dashboard');

        Route::get('posts/status', 'Admin\AdminPostController@posts_status')->name('posts.status');

        Route::get('posts/publish', 'Admin\AdminPostController@posts_publish')->name('posts.publish');

        Route::get('post/payments','Admin\AdminRevenueController@post_payments')->name('post.payments');

        Route::get('post/payments/view','Admin\AdminRevenueController@post_payments_view')->name('post.payments.view');

        Route::get('post_comments','Admin\AdminPostController@post_comments')->name('posts.comments');

        Route::get('post_comments/delete','Admin\AdminPostController@post_comment_delete')->name('post_comment.delete');

        Route::post('/posts/bulk_action', 'Admin\AdminPostController@posts_bulk_action')->name('posts.bulk_action');

        Route::post('/posts/file_delete', 'Admin\AdminPostController@posts_file_delete')->name('posts.file_delete');

        //posts end

        //posts albums start

        Route::get('post_albums' , 'Admin\AdminPostController@post_albums_index')->name('post_albums.index');

        Route::get('post_albums/delete', 'Admin\AdminPostController@post_albums_delete')->name('post_albums.delete');

        Route::get('post_albums/view', 'Admin\AdminPostController@post_albums_view')->name('post_albums.view');

        Route::get('post_albums/status', 'Admin\AdminPostController@post_albums_status')->name('post_albums.status');

        //posts albums end

        //orders start

        Route::get('orders' , 'Admin\AdminPostController@orders_index')->name('orders.index');

        Route::get('orders/view', 'Admin\AdminPostController@orders_view')->name('orders.view');

        Route::get('order/payments','Admin\AdminRevenueController@order_payments')->name('order.payments');

        Route::get('order/payments/view','Admin\AdminRevenueController@order_payments_view')->name('order.payments.view');

        //orders end

        //delivery address routes start

        Route::get('delivery_address' , 'Admin\AdminPostController@delivery_address_index')->name('delivery_address.index');

        Route::get('delivery_address/delete', 'Admin\AdminPostController@delivery_address_delete')->name('delivery_address.delete');

        Route::get('delivery_address/view', 'Admin\AdminPostController@delivery_address_view')->name('delivery_address.view');

        //delivery address routes end

         //bookmarks routes start

        Route::get('bookmarks' , 'Admin\AdminPostController@post_bookmarks_index')->name('bookmarks.index');

        Route::get('bookmarks/delete', 'Admin\AdminPostController@post_bookmarks_delete')->name('bookmarks.delete');

        Route::get('bookmarks/view', 'Admin\AdminPostController@post_bookmarks_view')->name('bookmarks.view');
         //bookmarks routes start


        // fav users route start
        Route::get('fav_users','Admin\AdminPostController@fav_users')->name('fav_users.index');

        Route::get('fav_users/delete','Admin\AdminPostController@fav_users_delete')->name('fav_users.delete');

        // end of fav user route end


      // liked posts route start
        Route::get('post_likes','Admin\AdminPostController@post_likes')->name('post_likes.index');

        Route::get('post_likes/delete','Admin\AdminPostController@post_likes_delete')->name('post_likes.delete');

        // end of liked posts


        
        //user wallet route start

        Route::get('user_wallets' , 'Admin\AdminRevenueController@user_wallets_index')->name('user_wallets.index');

        Route::get('user_wallets/view', 'Admin\AdminRevenueController@user_wallets_view')->name('user_wallets.view');

        Route::get('user_wallet_payments/view', 'Admin\AdminRevenueController@user_wallet_payments_view')->name('user_wallet_payments.view');

        //user wallet route end

        //revenue dashboard start

        Route::get('revenues/dashboard','Admin\AdminRevenueController@revenues_dashboard')->name('revenues.dashboard');

        //revenue dashboard end

        //subscriptions start
        Route::get('subscriptions', 'Admin\AdminRevenueController@subscriptions_index')->name('subscriptions.index');

        Route::get('subscriptions/create', 'Admin\AdminRevenueController@subscriptions_create')->name('subscriptions.create');

        Route::get('subscriptions/edit', 'Admin\AdminRevenueController@subscriptions_edit')->name('subscriptions.edit');

        Route::post('subscriptions/save', 'Admin\AdminRevenueController@subscriptions_save')->name('subscriptions.save');
        

        Route::get('subscriptions/view', 'Admin\AdminRevenueController@subscriptions_view')->name('subscriptions.view');

        Route::get('subscriptions/delete', 'Admin\AdminRevenueController@subscriptions_delete')->name('subscriptions.delete');

        Route::get('subscriptions/status', 'Admin\AdminRevenueController@subscriptions_status')->name('subscriptions.status');

        Route::get('subscriptions_payments/index','Admin\AdminRevenueController@subscription_payments_index')->name('subscription_payments.index');

        Route::get('subscriptions_payments/view','Admin\AdminRevenueController@subscription_payments_view')->name('subscription_payments.view');


        //subscriptions end

        //product_categories start
        Route::get('product_categories', 'Admin\AdminProductController@product_categories_index')->name('product_categories.index');

        Route::get('product_categories/create', 'Admin\AdminProductController@product_categories_create')->name('product_categories.create');

        Route::get('product_categories/edit', 'Admin\AdminProductController@product_categories_edit')->name('product_categories.edit');

        Route::post('product_categories/save', 'Admin\AdminProductController@product_categories_save')->name('product_categories.save');

        Route::get('product_categories/view', 'Admin\AdminProductController@product_categories_view')->name('product_categories.view');

        Route::get('product_categories/delete', 'Admin\AdminProductController@product_categories_delete')->name('product_categories.delete');

        Route::get('product_categories/status', 'Admin\AdminProductController@product_categories_status')->name('product_categories.status');

        Route::post('product_categories/get_product_sub_categories', 'Admin\AdminProductController@get_product_sub_categories')->name('get_product_sub_categories');

        //categories end

        //product_sub_categories start
        Route::get('product_sub_categories', 'Admin\AdminProductController@product_sub_categories_index')->name('product_sub_categories.index');

        Route::get('product_sub_categories/create', 'Admin\AdminProductController@product_sub_categories_create')->name('product_sub_categories.create');

        Route::get('product_sub_categories/edit', 'Admin\AdminProductController@product_sub_categories_edit')->name('product_sub_categories.edit');

        Route::post('product_sub_categories/save', 'Admin\AdminProductController@product_sub_categories_save')->name('product_sub_categories.save');

        Route::get('product_sub_categories/view', 'Admin\AdminProductController@product_sub_categories_view')->name('product_sub_categories.view');

        Route::get('product_sub_categories/delete', 'Admin\AdminProductController@product_sub_categories_delete')->name('product_sub_categories.delete');

        Route::get('product_sub_categories/status', 'Admin\AdminProductController@product_sub_categories_status')->name('product_sub_categories.status');


        //product_sub_categories end

        // CC withdrawals start

        Route::get('user_withdrawals','Admin\AdminRevenueController@user_withdrawals')->name('user_withdrawals');

        Route::get('user_withdrawals/paynow','Admin\AdminRevenueController@user_withdrawals_paynow')->name('user_withdrawals.paynow');

        Route::get('user_withdrawals/reject','Admin\AdminRevenueController@user_withdrawals_reject')->name('user_withdrawals.reject');

         Route::get('user_withdrawals/view','Admin\AdminRevenueController@user_withdrawals_view')->name('user_withdrawals.view');

        // CC withdrawals end

        //inventory route start

        Route::get('product_inventories/index' , 'Admin\AdminRevenueController@product_inventories_index')->name('product_inventories.index');

        Route::get('product_inventories/view', 'Admin\AdminRevenueController@product_inventories_view')->name('product_inventories.view');

        //inventory route end

        //faq CRUD
        Route::get('faqs', 'Admin\AdminLookupController@faqs_index')->name('faqs.index');

        Route::get('faqs/create', 'Admin\AdminLookupController@faqs_create')->name('faqs.create');

        Route::get('faqs/edit', 'Admin\AdminLookupController@faqs_edit')->name('faqs.edit');

        Route::post('faqs/save', 'Admin\AdminLookupController@faqs_save')->name('faqs.save');

        Route::get('faqs/view', 'Admin\AdminLookupController@faqs_view')->name('faqs.view');

        Route::get('faqs/delete', 'Admin\AdminLookupController@faqs_delete')->name('faqs.delete');

        Route::get('faqs/status', 'Admin\AdminLookupController@faqs_status')->name('faqs.status');
        //faq end


        // Static pages start

        Route::get('static_pages' , 'Admin\AdminLookupController@static_pages_index')->name('static_pages.index');

        Route::get('static_pages/create', 'Admin\AdminLookupController@static_pages_create')->name('static_pages.create');

        Route::get('static_pages/edit', 'Admin\AdminLookupController@static_pages_edit')->name('static_pages.edit');

        Route::post('static_pages/save', 'Admin\AdminLookupController@static_pages_save')->name('static_pages.save');

        Route::get('static_pages/delete', 'Admin\AdminLookupController@static_pages_delete')->name('static_pages.delete');

        Route::get('static_pages/view', 'Admin\AdminLookupController@static_pages_view')->name('static_pages.view');

        Route::get('static_pages/status', 'Admin\AdminLookupController@static_pages_status_change')->name('static_pages.status');

        // Static pages end

        // settings

        Route::get('settings-control', 'Admin\AdminSettingController@admin_control')->name('control');

        Route::get('features-control', 'Admin\AdminSettingController@features_control')->name('features_control');

        Route::get('settings', 'Admin\AdminSettingController@settings')->name('settings'); 

        Route::post('settings/save', 'Admin\AdminSettingController@settings_save')->name('settings.save'); 

        Route::post('settings_placeholder_img/save', 'Admin\AdminSettingController@settings_placeholder_img_save')->name('settings_placeholder_img.save'); 

        Route::post('env_settings','Admin\AdminSettingController@env_settings_save')->name('env-settings.save');

        Route::get('support_tickets/index','Admin\AdminSupportMemberController@support_tickets_index')->name('support_tickets.index');

        Route::get('support_tickets/view','Admin\AdminSupportMemberController@support_tickets_view')->name('support_tickets.view');

        Route::get('support_tickets/create', 'Admin\AdminSupportMemberController@support_tickets_create')->name('support_tickets.create');

        Route::post('support_tickets/save', 'Admin\AdminSupportMemberController@support_tickets_save')->name('support_tickets.save');
        
        Route::get('support_tickets/edit', 'Admin\AdminSupportMemberController@support_tickets_edit')->name('support_tickets.edit');

        Route::get('support_tickets/delete', 'Admin\AdminSupportMemberController@support_tickets_delete')->name('support_tickets.delete');

        // Support Members Operations

        Route::get('support_members/index', 'Admin\AdminSupportMemberController@support_members_index')->name('support_members.index');  

        Route::get('support_members/create', 'Admin\AdminSupportMemberController@support_members_create')->name('support_members.create');

        Route::get('support_members/view', 'Admin\AdminSupportMemberController@support_members_view')->name('support_members.view');

        Route::post('support_members/save', 'Admin\AdminSupportMemberController@support_members_save')->name('support_members.save');

        Route::get('support_members/edit', 'Admin\AdminSupportMemberController@support_members_edit')->name('support_members.edit');

        Route::get('support_members/delete', 'Admin\AdminSupportMemberController@support_members_delete')->name('support_members.delete');

        Route::get('support_members/status', 'Admin\AdminSupportMemberController@support_members_status')->name('support_members.status');

        Route::get('support_members/verify', 'Admin\AdminSupportMemberController@support_members_verify_status')->name('support_members.verify');


        Route::get('post_payments/send_invoice', 'Admin\AdminPostController@post_payments_send_invoice')->name('post_payments.send_invoice');

        Route::get('subscription_payments/send_invoice', 'Admin\AdminRevenueController@subscription_payments_send_invoice')->name('subscription_payments.send_invoice');

       
       
        Route::get('block_users', 'Admin\AdminUserController@block_users_index')->name('block_users.index');

        Route::get('block_users/view', 'Admin\AdminUserController@block_users_view')->name('block_users.view');

        Route::get('block_users/delete', 'Admin\AdminUserController@block_users_delete')->name('block_users.delete');


        Route::get('report_posts', 'Admin\AdminPostController@report_posts_index')->name('report_posts.index');

        Route::get('report_posts/view', 'Admin\AdminPostController@report_posts_view')->name('report_posts.view');

        Route::get('report_posts/delete', 'Admin\AdminPostController@report_posts_delete')->name('report_posts.delete');

        Route::get('categories', 'Admin\AdminCategoryController@categories_index')->name('categories.index');

        Route::get('categories/create', 'Admin\AdminCategoryController@categories_create')->name('categories.create');

        Route::get('categories/edit', 'Admin\AdminCategoryController@categories_edit')->name('categories.edit');

        Route::post('categories/save', 'Admin\AdminCategoryController@categories_save')->name('categories.save');

        Route::get('categories/view', 'Admin\AdminCategoryController@categories_view')->name('categories.view');

        Route::get('categories/status', 'Admin\AdminCategoryController@categories_status')->name('categories.status');

        Route::get('categories/delete', 'Admin\AdminCategoryController@categories_delete')->name('categories.delete');

        Route::get('user_tips/index','Admin\AdminRevenueController@user_tips_index')->name('user_tips.index');

        Route::get('user_tips/view','Admin\AdminRevenueController@user_tips_view')->name('user_tips.view');
    
        Route::post('/categories/bulk_action', 'Admin\AdminCategoryController@categories_bulk_action')->name('categories.bulk_action');

        Route::get('chat_asset_payments/index','Admin\AdminUserController@chat_asset_payments')->name('chat_asset_payments.index');

        Route::get('chat_asset_payments/view','Admin\AdminUserController@chat_asset_payment_view')->name('chat_asset_payments.view');

        Route::get('live_videos/index','Admin\AdminLiveVideoController@live_videos_index')->name('live_videos.index');

        Route::get('live_videos','Admin\AdminLiveVideoController@live_videos_onlive')->name('live_videos.onlive');

        Route::get('live_videos/view','Admin\AdminLiveVideoController@live_videos_view')->name('live_videos.view');

        Route::get('live_videos/delete','Admin\AdminLiveVideoController@live_videos_delete')->name('live_videos.delete');

        Route::get('live_videos/payments','Admin\AdminLiveVideoController@live_video_payments')->name('live_videos.payments');

        Route::get('live_videos/payments/view','Admin\AdminLiveVideoController@live_video_payments_view')->name('live_videos.payments.view');

        Route::get('video_call_payments','Admin\AdminVideoCallRequestController@video_call_payments')->name('video_call_payments.index');

        Route::get('video_call_payments/view','Admin\AdminVideoCallRequestController@video_call_payments_view')->name('video_call_payments.view');

        Route::get('video_call_requests','Admin\AdminVideoCallRequestController@video_call_requests_index')->name('video_call_requests.index');

        Route::get('video_call_requests/view','Admin\AdminVideoCallRequestController@video_call_requests_view')->name('video_call_requests.view');

        Route::get('users/dashboard', 'Admin\AdminUserController@user_dashboard')->name('users.dashboard');

        // hashtags

        Route::get('hashtags' , 'Admin\AdminPostController@hashtags_index')->name('hashtags.index');

        Route::get('hashtags/create', 'Admin\AdminPostController@hashtags_create')->name('hashtags.create');

        Route::post('hashtags/save', 'Admin\AdminPostController@hashtags_save')->name('hashtags.save');

        Route::get('hashtags/delete', 'Admin\AdminPostController@hashtags_delete')->name('hashtags.delete');

        Route::get('hashtags/view', 'Admin\AdminPostController@hashtags_view')->name('hashtags.view');

        Route::get('hashtags/status', 'Admin\AdminPostController@hashtags_status_change')->name('hashtags.status');

        //stories start

        Route::get('stories' , 'Admin\AdminStoryController@stories_index')->name('stories.index');

        Route::get('stories/delete', 'Admin\AdminStoryController@stories_delete')->name('stories.delete');

        Route::get('stories/create', 'Admin\AdminStoryController@stories_create')->name('stories.create');

        Route::get('stories/view', 'Admin\AdminStoryController@stories_view')->name('stories.view');

        Route::post('stories/save', 'Admin\AdminStoryController@stories_save')->name('stories.save');

        Route::get('stories/edit', 'Admin\AdminStoryController@stories_edit')->name('stories.edit');

        Route::get('stories/status', 'Admin\AdminStoryController@stories_status')->name('stories.status');

        //referral routes
        Route::get('referrals/index','Admin\AdminUserController@referral_codes')->name('referrals.index');

        Route::get('referrals/view','Admin\AdminUserController@referrals_view')->name('referrals.view');

        Route::get('users/send_week_report', 'Admin\AdminUserController@send_week_report')->name('users.send_week_report');

        Route::get('users/send_monthly_report', 'Admin\AdminUserController@send_monthly_report')->name('users.send_monthly_report');

        Route::get('users/send_custom_report', 'Admin\AdminUserController@send_custom_report')->name('users.send_custom_report');

        //vod_videos start

        Route::get('vod_videos' , 'Admin\AdminVodController@vod_videos_index')->name('vod_videos.index');

        Route::get('vod_videos/delete', 'Admin\AdminVodController@vod_videos_delete')->name('vod_videos.delete');

        Route::get('vod_videos/create', 'Admin\AdminVodController@vod_videos_create')->name('vod_videos.create');

        Route::get('vod_videos/view', 'Admin\AdminVodController@vod_videos_view')->name('vod_videos.view');

        Route::post('vod_videos/save', 'Admin\AdminVodController@vod_videos_save')->name('vod_videos.save');

        Route::get('vod_videos/edit', 'Admin\AdminVodController@vod_videos_edit')->name('vod_videos.edit');

        Route::get('vod_videos/status', 'Admin\AdminVodController@vod_videos_status')->name('vod_videos.status');

        // promo_codes CRUD operations

        Route::get('promo_codes/index', 'Admin\AdminPromoCodeController@promo_codes_index')->name('promo_codes.index');

        Route::get('promo_codes/create', 'Admin\AdminPromoCodeController@promo_codes_create')->name('promo_codes.create');

        Route::get('promo_codes/edit', 'Admin\AdminPromoCodeController@promo_codes_edit')->name('promo_codes.edit');

        Route::post('promo_codes/save', 'Admin\AdminPromoCodeController@promo_codes_save')->name('promo_codes.save');

        Route::get('promo_codes/view', 'Admin\AdminPromoCodeController@promo_codes_view')->name('promo_codes.view');

        Route::get('promo_codes/delete', 'Admin\AdminPromoCodeController@promo_codes_delete')->name('promo_codes.delete');

        Route::get('promo_codes/status', 'Admin\AdminPromoCodeController@promo_codes_status')->name('promo_codes.status');

        Route::get('users/report_dashboard', 'Admin\AdminUserController@report_dashboard')->name('users.report_dashboard');

        Route::get('users/weekly_report','Admin\AdminUserController@weekly_report')->name('users.weekly_report');

        Route::get('users/monthly_report','Admin\AdminUserController@monthly_report')->name('users.monthly_report');

        Route::get('users/custom_report','Admin\AdminUserController@custom_report')->name('users.custom_report');

        // Report Reason start

        Route::get('report_reasons' , 'Admin\AdminLookupController@report_reasons_index')->name('report_reasons.index');

        Route::get('report_reasons/create', 'Admin\AdminLookupController@report_reasons_create')->name('report_reasons.create');

        Route::get('report_reasons/edit', 'Admin\AdminLookupController@report_reasons_edit')->name('report_reasons.edit');

        Route::post('report_reasons/save', 'Admin\AdminLookupController@report_reasons_save')->name('report_reasons.save');

        Route::get('report_reasons/delete', 'Admin\AdminLookupController@report_reasons_delete')->name('report_reasons.delete');

        Route::get('report_reasons/view', 'Admin\AdminLookupController@report_reasons_view')->name('report_reasons.view');

        Route::get('report_reasons/status', 'Admin\AdminLookupController@report_reasons_status_change')->name('report_reasons.status');

        // Report Reason end

        Route::get('audio_call_requests','Admin\AdminAudioCallRequestController@audio_call_requests_index')->name('audio_call_requests.index');

        Route::get('audio_call_requests/view','Admin\AdminAudioCallRequestController@audio_call_requests_view')->name('audio_call_requests.view');

        Route::get('audio_call_payments','Admin\AdminAudioCallRequestController@audio_call_payments')->name('audio_call_payments.index');

        Route::get('audio_call_payments/view','Admin\AdminAudioCallRequestController@audio_call_payments_view')->name('audio_call_payments.view');

        Route::get('post_payment/excel','Admin\AdminRevenueController@post_payment_excel')->name('post_payment.excel');

        Route::get('tip_payment/excel','Admin\AdminRevenueController@tip_payment_excel')->name('tip_payment.excel');

        Route::get('live_video_payment/excel','Admin\AdminLiveVideoController@live_video_payment_excel')->name('live_video_payment.excel');

        Route::get('subscription_payment/excel','Admin\AdminUserController@subscription_payment_excel')->name('subscription_payment.excel');

        Route::get('video_call_payment/excel','Admin\AdminVideoCallRequestController@video_call_payment_excel')->name('video_call_payment.excel');

        Route::get('audio_call_payment/excel','Admin\AdminAudioCallRequestController@audio_call_payment_excel')->name('audio_call_payment.excel');

        Route::get('chat_asset_payment/excel','Admin\AdminUserController@chat_asset_payment_excel')->name('chat_asset_payment.excel');

        Route::get('order_payment/excel','Admin\AdminRevenueController@order_payment_excel')->name('order_payment.excel');

        Route::get('user_login_session/index','Admin\AdminAccountController@user_login_session_index')->name('user_login_session.index');

    });


});