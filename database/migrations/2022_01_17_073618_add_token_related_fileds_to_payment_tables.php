<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenRelatedFiledsToPaymentTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('publish_time');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->float('monthly_token')->default(0.00)->after('yearly_amount');
            $table->float('yearly_token')->default(0.00)->after('monthly_token');
        });

        Schema::table('user_wallet_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('usage_type');
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('post_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('currency');
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00);
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('user_tips', function (Blueprint $table) {
            $table->float('token')->default(0.00);
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('user_wallets', function (Blueprint $table) {
            $table->float('referral_token')->default(0)->after('referral_amount');
        });

        Schema::table('audio_call_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00);
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('chat_assets', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('amount');
        });

        Schema::table('chat_messages', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('amount');
        });

        Schema::table('chat_asset_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('currency');
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('live_videos', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('amount');
        });

        Schema::table('live_video_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('amount');
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('tax_price');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('tax_price');
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('delivery_price');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('publish_time');
        });

        Schema::table('video_call_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('currency');
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('vod_payments', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('payment_id');
            $table->float('admin_token')->default(0.00);
            $table->float('user_token')->default(0.00);
        });

        Schema::table('vod_videos', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('publish_time');
        });

        Schema::table('user_withdrawals', function (Blueprint $table) {
            $table->float('requested_token')->default(0.00)->after('requested_amount');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->float('video_call_token')->default(0.00)->after('video_call_amount');
            $table->float('audio_call_token')->default(0.00)->after('audio_call_amount');
        });

        Schema::table('user_products', function (Blueprint $table) {
            $table->float('token')->default(0.00)->after('quantity');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::table('user_subscriptions', function (Blueprint $table) {
            $table->dropColumn('monthly_token');
            $table->dropColumn('yearly_token');
        });

        Schema::table('user_wallet_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('post_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('user_subscription_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('user_tips', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('user_wallets', function (Blueprint $table) {
            $table->dropColumn('referral_token');
        });

        Schema::table('audio_call_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('chat_asset_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('live_videos', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::table('live_video_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::table('order_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('order_products', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::table('video_call_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('vod_payments', function (Blueprint $table) {
            $table->dropColumn('token');
            $table->dropColumn('admin_token');
            $table->dropColumn('user_token');
        });

        Schema::table('vod_videos', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('video_call_token');
            $table->dropColumn('audio_call_token');
        });

        Schema::table('user_withdrawals', function (Blueprint $table) {
            $table->dropColumn('requested_token');
        });

        Schema::table('chat_assets', function (Blueprint $table) {
            $table->dropColumn('token');
        });

        Schema::table('user_products', function (Blueprint $table) {
            $table->dropColumn('token');
        });
    }
}
