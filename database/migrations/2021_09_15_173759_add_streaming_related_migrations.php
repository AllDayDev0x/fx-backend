<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStreamingRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if(!Schema::hasTable('video_call_requests')) {

            Schema::create('video_call_requests', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('model_id');
                $table->integer('call_status')->default(0);
                $table->dateTime('start_time')->nullable();
                $table->dateTime('end_time')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('video_call_payments')) {

            Schema::create('video_call_payments', function (Blueprint $table) {
                $table->id();
                $table->integer('model_id');
                $table->integer('user_id');
                $table->integer('video_call_request_id');
                $table->integer('user_card_id')->default(0);
                $table->string('payment_id');
                $table->string('payment_mode')->default(CARD);
                $table->string('currency')->default('$');
                $table->float('paid_amount')->default(0.00);
                $table->dateTime('paid_date')->nullable();
                $table->tinyInteger('is_failed')->default(0);
                $table->tinyInteger('failed_reason')->default(0);
                $table->float('admin_amount')->default(0.00);
                $table->float('user_amount')->default(0.00);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('live_videos')) {

            Schema::create('live_videos', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->string('agora_token')->default(uniqid());
                $table->string('virtual_id')->default(uniqid());
                $table->string('type')->default(TYPE_PUBLIC)->comment('Public, Private');
                $table->string('broadcast_type')->default(BROADCAST_TYPE_BROADCAST);
                $table->integer('payment_status')->default(0)->comment('0 - No, 1 - Yes');
                $table->string('title')->default('');
                $table->text('description')->nullabe();
                $table->string('browser_name')->default('')->comment("Store Streamer Browser Name");
                $table->float('amount')->default(0.00);
                $table->integer('is_streaming')->default(0);
                $table->string('snapshot')->default(asset('images/live-streaming.jpeg'));
                $table->text('video_url')->nullabe();
                $table->integer('viewer_cnt')->default(0);
                $table->time('start_time')->nullable();
                $table->time('end_time')->nullable();
                $table->integer('no_of_minutes')->default(0);
                $table->string('port_no')->default('');
                $table->tinyInteger('status')->default(1);
                $table->softDeletes();
                $table->timestamps();
            });
            
        }

        if(!Schema::hasTable('live_video_payments')) {

            Schema::create('live_video_payments', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(rand());
                $table->integer('live_video_id');
                $table->integer('user_id');
                $table->integer('live_video_viewer_id');
                $table->string('payment_id');
                $table->string('payment_mode')->default(CARD);
                $table->float('live_video_amount')->default(0.00);
                $table->float('amount')->default(0.00);
                $table->float('admin_amount')->default(0.00);
                $table->float('user_amount')->default(0.00);
                $table->string('currency')->default('$');
                $table->tinyInteger('status')->default(1);
                $table->softDeletes();
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('viewers')) {

            Schema::create('viewers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(rand());
                $table->integer('live_video_id');
                $table->integer('user_id');
                $table->integer('count')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });
            
        }

        if(!Schema::hasTable('vc_chat_messages')) {

            Schema::create('vc_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('video_call_request_id');
                $table->integer('model_id');
                $table->integer('user_id');
                $table->text('message')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->softDeletes();
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('live_video_chat_messages')) {

            Schema::create('live_video_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('from_user_id');
                $table->integer('live_video_id')->default(0);
                $table->text('message')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('vod_videos')) {

            Schema::create('vod_videos', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id')->default(0);
                $table->string('title')->default("");
                $table->text('description')->nullable();
                $table->dateTime('publish_time')->nullable();
                $table->float('amount')->default(0.00);
                $table->string('preview_file')->default("");
                $table->string('file')->default("");
                $table->string('blur_file')->default("");
                $table->tinyInteger('is_paid_vod')->default(0);
                $table->tinyInteger('is_published')->default(1);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('vod_categories')) {

            Schema::create('vod_categories', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('vod_video_id')->default(0);
                $table->integer('post_category_id')->default(0);
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('vod_payments')) {

            Schema::create('vod_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(uniqid());
                $table->integer('from_user_id');
                $table->integer('to_user_id');
                $table->string('payment_id')->default("");
                $table->float('amount')->default(0.00);
                $table->string('payment_mode')->default(CARD);
                $table->datetime('expiry_date')->nullable();
                $table->datetime('paid_date')->nullable();
                $table->tinyInteger('status')->default(PAID);
                $table->float('admin_amount')->default(0.00);
                $table->float('user_amount')->default(0.00);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('audio_call_payments')) {

            Schema::create('audio_call_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('model_id');
                $table->integer('user_id');
                $table->integer('audio_call_request_id');
                $table->integer('user_card_id')->default(0);
                $table->string('payment_id');
                $table->string('payment_mode')->default(CARD);
                $table->string('currency')->default('$');
                $table->float('paid_amount')->default(0.00);
                $table->dateTime('paid_date')->nullable();
                $table->tinyInteger('is_failed')->default(0);
                $table->tinyInteger('failed_reason')->default(0);
                $table->float('admin_amount')->default(0.00);
                $table->float('user_amount')->default(0.00);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('audio_call_requests')) {

            Schema::create('audio_call_requests', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('model_id');
                $table->string('agora_token')->default(uniqid());
                $table->string('virtual_id')->default(uniqid());
                $table->integer('call_status')->default(0);
                $table->dateTime('start_time')->nullable();
                $table->dateTime('end_time')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('audio_chat_messages')) {

            Schema::create('audio_chat_messages', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('audio_call_request_id');
                $table->integer('model_id');
                $table->integer('user_id');
                $table->text('message')->nullable();
                $table->tinyInteger('status')->default(0);
                $table->softDeletes();
                $table->timestamps();
            });

        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('video_call_requests');
        Schema::dropIfExists('video_call_payments');
        Schema::dropIfExists('live_videos');
        Schema::dropIfExists('live_video_payments');
        Schema::dropIfExists('viewers');
        Schema::dropIfExists('vc_chat_messages');
        Schema::dropIfExists('live_video_chat_messages');
        Schema::dropIfExists('vod_videos');
        Schema::dropIfExists('vod_categories');
        Schema::dropIfExists('vod_payments');
        Schema::dropIfExists('audio_call_payments');
        Schema::dropIfExists('audio_call_requests');
        Schema::dropIfExists('audio_chat_messages');
    }
}
