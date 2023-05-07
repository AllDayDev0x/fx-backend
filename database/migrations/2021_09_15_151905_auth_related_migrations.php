<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AuthRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if(!Schema::hasTable('users')) {

            Schema::create('users', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unique_id')->default(rand());
                $table->string('name');
                $table->string('first_name')->default('');
                $table->string('middle_name')->default('');
                $table->string('last_name')->default('');
                $table->string('username')->nullable();
                $table->string('email')->unique();
                $table->text('about')->nullable();
                $table->enum('gender',['male','female','others','rather-not-select'])->default('male');
                $table->string('cover')->default(asset('cover.jpg'));
                $table->string('picture')->default(asset('placeholder.jpeg'));
                $table->string('password');
                $table->string('mobile');
                $table->string('address')->default('');
                $table->string('website')->default('');
                $table->string('amazon_wishlist')->default('');
                $table->tinyInteger('user_type')->default(0);
                $table->tinyInteger('user_account_type')->default(USER_FREE_ACCOUNT);
                $table->tinyInteger('is_document_verified')->default(0);
                $table->string('payment_mode')->default(CARD);
                $table->string('token');
                $table->string('token_expiry');
                $table->string('device_token')->nullable();
                $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
                $table->enum('login_by', ['manual','facebook','google', 'instagram', 'apple', 'linkedin'])->default('manual');
                $table->string('social_unique_id')->default('');
                $table->tinyInteger('registration_steps')->default(0);
                $table->tinyInteger('is_push_notification')->default(YES);
                $table->tinyInteger('is_email_notification')->default(YES);
                $table->integer('user_card_id')->default(0);
                $table->tinyInteger('is_email_verified')->default(0);
                $table->string('verification_code')->default('');
                $table->string('verification_code_expiry')->default('');
                $table->timestamp('email_verified_at')->nullable();
                $table->tinyInteger('status')->default(1);
                $table->tinyInteger('one_time_subscription')->comment("0 - Not Subscribed , 1 - Subscribed")->default(0);
                $table->float('amount_paid')->default(0);
                $table->dateTime('expiry_date')->nullable();
                $table->tinyInteger('no_of_days')->default(0);
                $table->rememberToken();
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('admins')) {

            Schema::create('admins', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unique_id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('password');
                $table->text('about')->nullable();
                $table->string('picture')->default(asset('placeholder.jpeg'));
                $table->string('timezone')->default("");
                $table->enum('gender', ['male', 'female', 'others','rather-not-select'])->default('male');
                $table->tinyInteger('status')->default(1);
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if(!Schema::hasTable('documents')) {

            Schema::create('documents', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('name');
                $table->string('image_type')->default('jpg');
                $table->string('picture')->default(asset('document.jpg'));
                $table->text('description')->nullable();
                $table->tinyInteger('is_required')->default(1);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_documents')) {

            Schema::create('user_documents', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('document_id');
                $table->string('document_file');
                $table->string('document_file_front')->default('');
                $table->string('document_file_back')->default('');
                $table->tinyInteger('is_verified')->default(0)->comment('0 - pending, 1 - approved, 2 - declined');
                $table->string('uploaded_by')->default('user')->comment('user | admin');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_wallets')) {

            Schema::create('user_wallets', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->float('total')->default(0.00);
                $table->float('onhold')->default(0.00);
                $table->float('used')->default(0.00);
                $table->float('remaining')->default(0.00);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_wallet_payments')) {

            Schema::create('user_wallet_payments', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('to_user_id')->default(0);
                $table->integer('received_from_user_id')->default(0);
                $table->integer('generated_invoice_id')->default(0);
                $table->string('payment_id');
                $table->string('payment_type')->default(WALLET_PAYMENT_TYPE_ADD)->comment("add, paid, credit");
                $table->string('amount_type')->default(WALLET_PAYMENT_TYPE_ADD)->comment("add, minus");
                $table->float('requested_amount')->default(0.00);
                $table->float('paid_amount')->default(0.00);
                $table->string('currency')->default('$');
                $table->string('payment_mode')->default(CARD);
                $table->dateTime('paid_date')->nullable();
                $table->string('message')->default("");
                $table->integer('is_cancelled')->default(0);
                $table->string('cancelled_reason')->default("");
                $table->string('updated_by')->default('user')->comment('admin, user');
                $table->string('bank_statement_picture')->default('');
                $table->tinyInteger('is_admin_approved')->default(0);
                $table->integer('user_billing_account_id')->default(0);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });
            
        }

        if(!Schema::hasTable('subscriptions')) {

            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('title');
                $table->text('description');
                $table->float('amount')->default(0.00);
                $table->integer('plan')->default(1);
                $table->string('plan_type')->default(PLAN_TYPE_MONTH);
                $table->tinyInteger('is_free')->default(0);
                $table->tinyInteger('is_popular')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_withdrawals')) {

            Schema::create('user_withdrawals', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('user_wallet_payment_id')->default(0);
                $table->string('payment_id')->default("");
                $table->string('payment_mode')->default(PAYMENT_OFFLINE);
                $table->float('requested_amount')->default(0.00);
                $table->float('paid_amount')->default(0.00);
                $table->text('cancel_reason')->nullable();
                $table->integer('user_billing_account_id')->default(0);
                $table->tinyInteger('status')->default(0)->comment("0 - pending, 1 - paid, 2 - rejected");
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('subscription_payments')) {

            Schema::create('subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(uniqid());
                $table->integer('subscription_id');
                $table->integer('user_id');
                $table->string('payment_id')->default("");
                $table->float('amount')->default(0.00);
                $table->string('payment_mode')->default(CARD);
                $table->integer('is_current_subscription')->default(0);
                $table->datetime('expiry_date')->nullable();
                $table->datetime('paid_date')->nullable();
                $table->tinyInteger('status')->default(PAID);
                $table->tinyInteger('is_cancelled')->default(0);
                $table->text('cancel_reason')->nullable("");
                $table->integer('plan')->default(1);
                $table->string('plan_type')->default(PLAN_TYPE_MONTH);
                $table->softDeletes();
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_cards')) {

            Schema::create('user_cards', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(uniqid());
                $table->integer('user_id');
                $table->string('card_holder_name')->default("");
                $table->string('card_type');
                $table->string('customer_id');
                $table->string('last_four');
                $table->string('card_token');
                $table->integer('is_default')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_billing_accounts')) {

            Schema::create('user_billing_accounts', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('unique_id')->unique();
                $table->integer('user_id');
                $table->string('nickname')->nullable();
                $table->string('bank_name')->default("");
                $table->string('account_holder_name');
                $table->string('account_number');
                $table->string('ifsc_code')->default("");
                $table->string('swift_code')->default("");
                $table->tinyInteger('is_default')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            
            });
            
        }

        if(!Schema::hasTable('followers')) {

            Schema::create('followers', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->comment("login user id - content_creators,users");
                $table->integer('follower_id')->comment("fallowers id of content_creators");
                $table->integer('status');
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('fav_users')) {

            Schema::create('fav_users', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('fav_user_id');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_tips')) {

            Schema::create('user_tips', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('to_user_id');
                $table->integer('post_id')->default(0);
                $table->float('amount')->default(0.00);
                $table->string('payment_id');
                $table->integer('user_card_id')->default(0);
                $table->string('payment_mode')->default(CARD);
                $table->string('currency')->default('$');
                $table->dateTime('paid_date')->nullable();
                $table->tinyInteger('is_failed')->default(0);
                $table->tinyInteger('failed_reason')->default(0);
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_subscription_payments')) {

            Schema::create('user_subscription_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(uniqid());
                $table->integer('user_subscription_id');
                $table->integer('from_user_id');
                $table->integer('to_user_id');
                $table->string('payment_id')->default("");
                $table->float('amount')->default(0.00);
                $table->string('payment_mode')->default(CARD);
                $table->integer('is_current_subscription')->default(0);
                $table->datetime('expiry_date')->nullable();
                $table->datetime('paid_date')->nullable();
                $table->tinyInteger('status')->default(PAID);
                $table->tinyInteger('is_cancelled')->default(0);
                $table->text('cancel_reason')->nullable("");
                $table->integer('plan')->default(1);
                $table->string('plan_type')->default(PLAN_TYPE_MONTH);
                $table->float('admin_amount')->default(0.00);
                $table->float('user_amount')->default(0.00);
                $table->softDeletes();
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_subscriptions')) {

            Schema::create('user_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->float('monthly_amount')->default(1);
                $table->float('yearly_amount')->default(10);
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('chat_messages')) {

            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('from_user_id');
                $table->integer('to_user_id');
                $table->text('message');
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('chat_users')) {

            Schema::create('chat_users', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('from_user_id');
                $table->integer('to_user_id');
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('block_users')) {

            Schema::create('block_users', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->nullable();
                $table->integer('block_by');
                $table->integer('blocked_to');
                $table->text('reason')->nullable();
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('u_categories')) {

            Schema::create('u_categories', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('picture')->default(asset('categories-placeholder.png'));
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_categories')) {

            Schema::create('user_categories', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('u_category_id');
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('referral_codes')) {

            Schema::create('referral_codes', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->string('referral_code')->default("");
                $table->integer('total_referrals')->default(0);
                $table->float('referral_earnings')->default(0.00)->comment("Using the current user code, if someone joined means the current user will get this earnings");
                $table->float('referee_earnings')->default(0.00)->comment("if the current user joined using someother user referral code means the current user will get some earnings");
                $table->integer('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_referrals')) {

            Schema::create('user_referrals', function (Blueprint $table) {
                $table->increments('id');
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('parent_user_id');
                $table->integer('referral_code_id');
                $table->string('referral_code')->default("");
                $table->string('device_type')->default(DEVICE_WEB);           
                $table->integer('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_login_sessions')) {

            Schema::create('user_login_sessions', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id')->default(0);
                $table->enum('device_type', ['web', 'android', 'ios'])->default('web');
                $table->string('device_model')->default("");
                $table->string('ip_address')->default("");
                $table->tinyInteger('is_current_session')->default(YES);
                $table->dateTime('last_session')->nullable("");
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('verification_codes')) {

            Schema::create('verification_codes', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('email')->default('');
                $table->string('username')->default('');
                $table->string('code')->default('');
                $table->tinyInteger('status')->default(YES);            
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('report_reasons')) {

            Schema::create('report_reasons', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('title')->default('');
                $table->string('description')->default('');
                $table->tinyInteger('status')->default(YES);            
                $table->timestamps();  
            });

        }

        if(!Schema::hasTable('chat_assets')) {

            Schema::create('chat_assets', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('from_user_id');
                $table->integer('to_user_id');
                $table->integer('chat_message_id');
                $table->string('file');
                $table->string('file_type')->default(FILE_TYPE_IMAGE);
                $table->float('amount')->default(0.00);
                $table->tinyInteger('is_paid')->default(NO);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('chat_asset_payments')) {

            Schema::create('chat_asset_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('from_user_id');
                $table->integer('to_user_id');
                $table->integer('chat_message_id');
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

        if(!Schema::hasTable('promo_codes')) {

            Schema::create('promo_codes', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(uniqid());
                $table->string('title')->default("");
                $table->string('promo_code')->unique();
                $table->string('amount_type')->default(0);
                $table->float('amount')->default(0);
                $table->dateTime('start_date')->nullable();
                $table->dateTime('expiry_date')->nullable();
                $table->integer('user_id')->default(0);
                $table->string('description')->default("");
                $table->smallInteger('no_of_users_limit')->nullable();
                $table->tinyInteger('per_users_limit')->nullable();
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_promo_codes')) {

            Schema::create('user_promo_codes', function (Blueprint $table) {
                $table->id();
                $table->integer('user_id')->default(0);
                $table->string('promo_code')->nullable();
                $table->tinyInteger('no_of_times_used')->default(0);
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
        Schema::dropIfExists('users');
        Schema::dropIfExists('admins');
        Schema::dropIfExists('documents');
        Schema::dropIfExists('user_documents');
        Schema::dropIfExists('user_wallets');
        Schema::dropIfExists('user_wallet_payments');
        Schema::dropIfExists('subscriptions');
        Schema::dropIfExists('user_withdrawals');
        Schema::dropIfExists('subscription_payments');
        Schema::dropIfExists('user_cards');
        Schema::dropIfExists('user_billing_accounts');
        Schema::dropIfExists('followers');
        Schema::dropIfExists('fav_users');
        Schema::dropIfExists('user_tips');
        Schema::dropIfExists('user_subscription_payments');
        Schema::dropIfExists('user_subscriptions');
        Schema::dropIfExists('chat_messages');
        Schema::dropIfExists('chat_users');
        Schema::dropIfExists('block_users');
        Schema::dropIfExists('u_categories');
        Schema::dropIfExists('user_categories');
        Schema::dropIfExists('referral_codes');
        Schema::dropIfExists('user_referrals');
        Schema::dropIfExists('user_login_sessions');
        Schema::dropIfExists('verification_codes');
        Schema::dropIfExists('report_reasons');
        Schema::dropIfExists('chat_assets');
        Schema::dropIfExists('chat_asset_payments');
        Schema::dropIfExists('promo_codes');
        Schema::dropIfExists('user_promo_codes');
    }
}
