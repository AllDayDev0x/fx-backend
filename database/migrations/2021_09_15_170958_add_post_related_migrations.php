<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPostRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!Schema::hasTable('posts')) {

            Schema::create('posts', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->text('content')->nullable();
                $table->dateTime('publish_time')->nullable();
                $table->float('amount')->default(0.00);
                $table->tinyInteger('is_paid_post')->default(0);
                $table->tinyInteger('is_published')->default(1);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_albums')) {

            Schema::create('post_albums', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->string('name');
                $table->text('description')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_comments')) {

            Schema::create('post_comments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('post_id');
                $table->text('comment');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_payments')) {

            Schema::create('post_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('post_id');
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

        if(!Schema::hasTable('post_files')) {

            Schema::create('post_files', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('post_id');
                $table->string('file');
                $table->string('blur_file')->default("");
                $table->string('file_type')->default('image');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_album_files')) {

            Schema::create('post_album_files', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('post_id');
                $table->string('file');
                $table->string('file_type')->default('image');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_bookmarks')) {

            Schema::create('post_bookmarks', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('post_id');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_likes')) {

            Schema::create('post_likes', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('post_id');
                $table->integer('post_user_id');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('report_posts')) {

            Schema::create('report_posts', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('post_id');
                $table->integer('block_by');    
                $table->text('reason')->nullable();
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_comment_replies')) {

            Schema::create('post_comment_replies', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('post_id');
                $table->integer('post_comment_id');
                $table->text('reply');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_comment_likes')) {

            Schema::create('post_comment_likes', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('post_comment_id')->default(0);
                $table->integer('post_comment_reply_id')->default(0);
                $table->integer('post_user_id');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('stories')) {

            Schema::create('stories', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id')->default(0);
                $table->text('content')->nullable();
                $table->dateTime('publish_time')->nullable();
                $table->float('amount')->default(0.00);
                $table->tinyInteger('is_paid_story')->default(0);
                $table->tinyInteger('is_published')->default(1);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('story_files')) {

            Schema::create('story_files', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id')->default(0);
                $table->integer('story_id')->default(0);
                $table->string('file')->default(asset('placeholder.jpeg'));
                $table->string('blur_file')->default(asset('placeholder.jpeg'));
                $table->string('file_type')->default(FILE_TYPE_IMAGE);
                $table->string('preview_file')->default(asset('placeholder.jpeg'));
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('hashtags')) {

            Schema::create('hashtags', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->integer('count')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_hashtags')) {

            Schema::create('post_hashtags', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id')->default(0);
                $table->integer('post_id')->default(0);
                $table->string('hashtag_id')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_category_details')) {

            Schema::create('post_category_details', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('post_id')->default(0);
                $table->integer('post_category_id')->default(0);
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('post_categories')) {

            Schema::create('post_categories', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('name')->nullable();
                $table->text('description')->nullable();
                $table->string('picture')->default(asset('placeholder.jpeg'));
                $table->tinyInteger('status')->default(YES);
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
        Schema::dropIfExists('posts');
        Schema::dropIfExists('post_albums');
        Schema::dropIfExists('post_comments');
        Schema::dropIfExists('post_payments');
        Schema::dropIfExists('post_files');
        Schema::dropIfExists('post_album_files');
        Schema::dropIfExists('post_bookmarks');
        Schema::dropIfExists('post_likes');
        Schema::dropIfExists('report_posts');
        Schema::dropIfExists('post_comment_replies');
        Schema::dropIfExists('post_comment_likes');
        Schema::dropIfExists('stories');
        Schema::dropIfExists('story_files');
        Schema::dropIfExists('hashtags');
        Schema::dropIfExists('post_hashtags');
        Schema::dropIfExists('post_category_details');
        Schema::dropIfExists('post_categories');
    }
}
