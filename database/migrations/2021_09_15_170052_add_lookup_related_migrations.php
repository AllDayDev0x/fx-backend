<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddLookupRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if(!Schema::hasTable('static_pages')) {

            Schema::create('static_pages', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(uniqid());
                $table->string('title')->unique();
                $table->text('description');
                $table->enum('type',['about','privacy','terms','refund','cancellation','faq','help','contact','others'])->default('others');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('settings')) {

            Schema::create('settings', function (Blueprint $table) {
                $table->increments('id');
                $table->string('key');
                $table->text('value');
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });  
        }

        if(!Schema::hasTable('faqs')) {

            Schema::create('faqs', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('question');
                $table->text('answer');
                $table->tinyInteger('status')->default(YES);            
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('support_members')) {

            Schema::create('support_members', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('name');
                $table->string('first_name')->default('');
                $table->string('middle_name')->default('');
                $table->string('last_name')->default('');
                $table->string('username')->unique()->nullable();
                $table->string('email')->unique();
                $table->text('about')->nullable();
                $table->string('picture')->default(asset('placeholder.jpeg'));
                $table->string('password');
                $table->string('mobile');
                $table->string('address')->default('');
                $table->string('token');
                $table->string('token_expiry');
                $table->string('device_token')->nullable();
                $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
                $table->integer('is_email_verified')->default(YES);
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('support_tickets')) {

            Schema::create('support_tickets', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('support_member_id')->default(0);
                $table->string('subject');
                $table->text('message');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('support_chats')) {

            Schema::create('support_chats', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('support_member_id')->default(0);
                $table->integer('support_ticket_id');
                $table->text('message');
                $table->string('type')->comment="su, us";
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('bell_notifications')) {

            Schema::create('bell_notifications', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('from_user_id');
                $table->integer('to_user_id');
                $table->string('image')->default("");
                $table->string('subject')->default("");
                $table->text('message');
                $table->string('action_url')->default("/home");
                $table->tinyInteger('is_read')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('page_counters')) {

            Schema::create('page_counters', function (Blueprint $table) {
                $table->id();
                $table->string('page');
                $table->integer('count');
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
        Schema::dropIfExists('static_pages');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('faqs');
        Schema::dropIfExists('support_members');
        Schema::dropIfExists('support_tickets');
        Schema::dropIfExists('support_chats');
        Schema::dropIfExists('bell_notifications');
        Schema::dropIfExists('page_counters');
    }
}
