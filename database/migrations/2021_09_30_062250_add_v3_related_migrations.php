<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddV3RelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        Schema::table('static_pages', function (Blueprint $table) {
            $table->string('section_type')->nullable()->after('type');
        });

        Schema::table('user_billing_accounts', function (Blueprint $table) {
            $table->string('iban_number')->default("")->before('status');
            $table->string('route_number')->default("")->after('iban_number');
            $table->string('business_name')->nullable()->after('nickname');
            $table->string('first_name')->nullable()->after('business_name');
            $table->string('last_name')->nullable()->after('first_name');
            $table->string('bank_type')->default(BANK_TYPE_SAVINGS)->after('swift_code');
        });

        Schema::table('bell_notifications', function (Blueprint $table) {
            $table->integer('post_id')->after('message')->default(0);
            $table->integer('post_comment_id')->after('post_id')->default(0);
            $table->string('notification_type')->default(BELL_NOTIFICATION_TYPE_FOLLOW)->after('action_url'); 
        });

        Schema::table('post_files', function (Blueprint $table) {
            $table->string('preview_file')->default("")->after('blur_file');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->tinyInteger('is_verified_badge')->default(NO);
            $table->string('instagram_link')->default("");
            $table->string('snapchat_link')->default("");
            $table->string('facebook_link')->default("");
            $table->string('twitter_link')->default("");
            $table->string('linkedin_link')->default("");
            $table->string('pinterest_link')->default("");
            $table->string('youtube_link')->default("");
            $table->string('twitch_link')->default("");
            $table->float('video_call_amount')->after('no_of_days')->default(0.00);
            $table->string('timezone')->default("");
            $table->tinyInteger('ios_theme')->default(0);
            $table->string('eyes_color')->nullable()->after('remember_token');
            $table->integer('height')->default(0)->comment("Height in cm")->after('eyes_color');
            $table->integer('weight')->default(0)->comment("Weight in pounds")->after('height'); 
            $table->double('latitude',15,8)->default(0.000000);
            $table->double('longitude',15,8)->default(0.000000);
            $table->tinyInteger('is_content_creator')->default(DEFAULT_USER)->after('updated_at'); 
            $table->float('audio_call_amount')->after('video_call_amount')->default(0.00);
            $table->tinyInteger('is_online_status')->default(YES)->after('email_verified_at');
            $table->string('default_payment_method')->default(PAYMENT_MODE_WALLET)->after('is_online_status');
            $table->tinyInteger('content_creator_step')->default(CONTENT_CREATOR_INITIAL)->after('is_content_creator');
            $table->tinyInteger('is_two_step_auth_enabled')->default(NO)->after('updated_at');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->integer('is_file_uploaded')->default(NO);
            $table->float('amount')->default(0.00);
            $table->tinyInteger('is_paid')->default(NO);
        });

        Schema::table('u_categories', function($table) {
            $table->string('unique_id')->unique()->after('id');
        });

        Schema::table('user_tips', function (Blueprint $table) {
            $table->float('admin_amount')->default(0.00)->after('currency');
            $table->float('user_amount')->default(0.00)->after('admin_amount');
            $table->text('message')->after('amount')->nullable();
            $table->integer('user_wallet_payment_id')->default(0);
            $table->string('trans_token')->default("");

        });

        Schema::table('user_wallet_payments', function (Blueprint $table) {
            $table->float('admin_amount')->default(0.00)->after('paid_amount');
            $table->float('user_amount')->default(0.00)->after('admin_amount');
            $table->string('usage_type')->after('amount_type')->default("");
        });

        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->string('trans_token')->default("");
            $table->string('promo_code')->default('')->after('amount');
            $table->float('promo_code_amount')->default(0.00)->after('promo_code');
            $table->tinyInteger('is_promo_code_applied')->default(0)->after('promo_code_amount');
            $table->text('promo_code_reason')->default('')->after('is_promo_code_applied');
        });

        Schema::table('post_payments', function (Blueprint $table) {
            $table->string('trans_token')->default("");
            $table->string('promo_code')->default('')->after('payment_mode');
            $table->float('promo_code_amount')->default(0.00)->after('promo_code');
            $table->tinyInteger('is_promo_code_applied')->default(0)->after('promo_code_amount');
            $table->text('promo_code_reason')->default('')->after('is_promo_code_applied');
        });

        Schema::table('video_call_requests', function (Blueprint $table) {
            $table->string('agora_token')->default(uniqid());
            $table->string('virtual_id')->default(uniqid());
        });

        Schema::table('user_wallets', function (Blueprint $table) {
            $table->float('referral_amount')->default(0.00)->after('remaining');
        });

        Schema::table('chat_assets', function (Blueprint $table) {
            $table->string('blur_file')->default("");
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->float('vod_amount')->default(1)->after('yearly_amount');
        });

        Schema::table('report_posts', function (Blueprint $table) {
            $table->integer('report_reason_id')->default(0)->after('block_by');
        });

        \DB::statement("ALTER TABLE `users` CHANGE `gender` `gender` ENUM('male','female','others','rather-not-select') DEFAULT 'rather-not-select';");

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
        Schema::table('static_pages', function (Blueprint $table) {
            $table->dropColumn('section_type');
        });

        Schema::table('user_billing_accounts', function (Blueprint $table) {
            $table->dropColumn('iban_number');
            $table->dropColumn('route_number');
            $table->dropColumn('business_name');
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('bank_type');
        });

        Schema::table('bell_notifications', function (Blueprint $table) {
            $table->dropColumn('post_id');
            $table->dropColumn('post_comment_id');
            $table->dropColumn('notification_type');
        });

        Schema::table('post_files', function (Blueprint $table) {
            $table->dropColumn('preview_file');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_verified_badge');
            $table->dropColumn('instagram_link');
            $table->dropColumn('snapchat_link');
            $table->dropColumn('facebook_link');
            $table->dropColumn('twitter_link');
            $table->dropColumn('linkedin_link');
            $table->dropColumn('pinterest_link');
            $table->dropColumn('youtube_link');
            $table->dropColumn('twitch_link');
            $table->dropColumn('video_call_amount');
            $table->dropColumn('timezone');
            $table->dropColumn('ios_theme');
            $table->dropColumn('eyes_color');
            $table->dropColumn('height');
            $table->dropColumn('weight');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('is_content_creator');
            $table->dropColumn('audio_call_amount');
            $table->dropColumn('is_online_status');
            $table->dropColumn('default_payment_method');
            $table->dropColumn('content_creator_step');
            $table->dropColumn('is_two_step_auth_enabled');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->dropColumn('is_file_uploaded');
            $table->dropColumn('amount');
            $table->dropColumn('is_paid');
        });

        Schema::table('u_categories', function($table) {
            $table->dropColumn('unique_id');
        });

        Schema::table('user_tips', function (Blueprint $table) {
            $table->dropColumn('admin_amount');
            $table->dropColumn('user_amount');
            $table->dropColumn('message');
            $table->dropColumn('user_wallet_payment_id');
            $table->dropColumn('trans_token');
        });

        Schema::table('user_wallet_payments', function (Blueprint $table) {
            $table->dropColumn('admin_amount');
            $table->dropColumn('user_amount');
            $table->dropColumn('usage_type');
        });

        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->dropColumn('trans_token');
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_code_amount');
            $table->dropColumn('is_promo_code_applied');
            $table->dropColumn('promo_code_reason');
        });

        Schema::table('post_payments', function (Blueprint $table) {
            $table->dropColumn('trans_token');
            $table->dropColumn('promo_code');
            $table->dropColumn('promo_code_amount');
            $table->dropColumn('is_promo_code_applied');
            $table->dropColumn('promo_code_reason');
        });

        Schema::table('video_call_requests', function (Blueprint $table) {
            $table->dropColumn('agora_token');
            $table->dropColumn('virtual_id');
        });

        Schema::table('user_wallets', function (Blueprint $table) {
            $table->dropColumn('referral_amount');
        });

        Schema::table('chat_assets', function (Blueprint $table) {
            $table->dropColumn('blur_file');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('vod_amount');
        });

        Schema::table('report_posts', function (Blueprint $table) {
            $table->dropColumn('report_reason_id');
        });

    }
}
