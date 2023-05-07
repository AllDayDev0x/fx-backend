<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddV6RelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->dropColumn('is_featured');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->string('featured_story')->default("");
        });

        Schema::table('post_files', function (Blueprint $table) {
            $table->string('video_preview_file')->default('');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stories', function (Blueprint $table) {
            $table->tinyInteger('is_featured')->after('is_published')->default(NO);
        });
        
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('featured_story');
        });

        Schema::table('post_files', function (Blueprint $table) {
            $table->dropColumn('video_preview_file');
        });
    }
}
