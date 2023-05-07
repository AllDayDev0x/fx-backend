<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddProductsRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        if(!Schema::hasTable('user_products')) {

            Schema::create('user_products', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('category_id');
                $table->integer('sub_category_id');
                $table->string('name');
                $table->text('description');
                $table->string('picture')->default(asset('product-placeholder.jpeg'));
                $table->float('quantity')->default(0.00);
                $table->float('price')->default(0.00);
                $table->float('delivery_price')->default(0.00);
                $table->tinyInteger('is_outofstock')->default(PRODUCT_AVAILABLE);
                $table->tinyInteger('is_visible')->default(YES);
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('user_product_pictures')) {

            Schema::create('user_product_pictures', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_product_id');
                $table->string('picture');
                $table->tinyInteger('status')->default(APPROVED);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('product_inventories')) {

            Schema::create('product_inventories', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_product_id');
                $table->float('total_quantity')->default(0.00);
                $table->float('remaining_quantity')->default(0.00);
                $table->float('onhold_quantity')->default(0.00); // not used
                $table->float('used_quantity')->default(0.00);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('orders')) {

            Schema::create('orders', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('delivery_address_id');
                $table->integer('total_products')->default(0);
                $table->float('sub_total')->default(0.00);
                $table->float('tax_price')->default(0.00);
                $table->float('total')->default(0.00);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('order_products')) {

            Schema::create('order_products', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('order_id');
                $table->integer('user_product_id');
                $table->float('quantity')->default(0.00);
                $table->float('per_quantity_price')->default(0.00);
                $table->float('sub_total')->default(0.00);
                $table->float('tax_price')->default(0.00);
                $table->float('delivery_price')->default(0.00);
                $table->float('total')->default(0.00);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('carts')) {

            Schema::create('carts', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('order_id');
                $table->integer('user_product_id');
                $table->float('quantity')->default(0.00);
                $table->float('per_quantity_price')->default(0.00);
                $table->float('sub_total')->default(0.00);
                $table->float('tax_price')->default(0.00);
                $table->float('delivery_price')->default(0.00);
                $table->float('total')->default(0.00);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('delivery_addresses')) {

            Schema::create('delivery_addresses', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->string('name');
                $table->text('address');
                $table->string('pincode')->default('');
                $table->string('state')->default('');
                $table->string('landmark')->default('');
                $table->string('contact_number')->default('');
                $table->tinyInteger('is_default')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('order_payments')) {

            Schema::create('order_payments', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id');
                $table->integer('order_id');
                $table->string('payment_id');
                $table->string('payment_mode')->default(CARD);
                $table->string('currency')->default('$');
                $table->float('delivery_price')->default(0.00);
                $table->float('sub_total')->default(0.00);
                $table->float('tax_price')->default(0.00);
                $table->float('total')->default(0.00);
                $table->dateTime('paid_date')->nullable();
                $table->tinyInteger('is_failed')->default(0);
                $table->tinyInteger('failed_reason')->default(0);
                $table->tinyInteger('status')->default(0);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('categories')) {

            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('picture')->default(asset('cat-placeholder.jpeg'));
                $table->tinyInteger('status')->default(1);
                $table->timestamps();
            });

        }

        if(!Schema::hasTable('sub_categories')) {

            Schema::create('sub_categories', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->string('name');
                $table->integer('category_id');
                $table->text('description')->nullable();
                $table->string('picture')->default(asset('cat-placeholder.jpeg'));
                $table->tinyInteger('status')->default(1);
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
        Schema::dropIfExists('user_products');
        Schema::dropIfExists('user_product_pictures');
        Schema::dropIfExists('product_inventories');
        Schema::dropIfExists('orders');
        Schema::dropIfExists('order_products');
        Schema::dropIfExists('carts');
        Schema::dropIfExists('delivery_addresses');
        Schema::dropIfExists('order_payments');
        Schema::dropIfExists('categories');
        Schema::dropIfExists('sub_categories');
    }
}
