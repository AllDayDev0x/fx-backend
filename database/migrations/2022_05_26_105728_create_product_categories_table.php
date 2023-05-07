<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_categories', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('picture')->default(asset('cat-placeholder.jpeg'));
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::create('product_sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('unique_id')->default(rand());
            $table->string('name');
            $table->integer('product_category_id');
            $table->text('description')->nullable();
            $table->string('picture')->default(asset('cat-placeholder.jpeg'));
            $table->tinyInteger('status')->default(APPROVED);
            $table->timestamps();
        });

        Schema::table('user_products', function (Blueprint $table) {
            $table->renameColumn('category_id', 'product_category_id');
            $table->renameColumn('sub_category_id', 'product_sub_category_id');
        });

        Schema::dropIfExists('post_categories');

        Schema::dropIfExists('post_category_details');

        Schema::dropIfExists('u_categories');

        Schema::dropIfExists('user_categories');

        if(!Schema::hasTable('category_details')) {

            Schema::create('category_details', function (Blueprint $table) {
                $table->id();
                $table->string('unique_id')->default(rand());
                $table->integer('user_id')->default(0);
                $table->integer('post_id')->default(0);
                $table->integer('category_id')->default(0);
                $table->enum('type', ['profile', 'post'])->default('profile');
                $table->tinyInteger('status')->default(YES);
                $table->timestamps();
            });

        }

        Schema::table('vod_categories', function (Blueprint $table) {
            $table->renameColumn('post_category_id', 'category_id');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_categories');

        Schema::dropIfExists('product_sub_categories');

        Schema::table('user_products', function (Blueprint $table) {
            $table->renameColumn('product_category_id', 'category_id');
            $table->renameColumn('product_sub_category_id', 'sub_category_id');
        });

        Schema::dropIfExists('category_details');

        Schema::table('vod_categories', function (Blueprint $table) {
            $table->renameColumn('category_id', 'post_category_id');
        });

    }
}
