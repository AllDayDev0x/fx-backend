<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCallChargeTokenRelatedMigrations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Schema::table('audio_call_payments', function (Blueprint $table) {
            $table->time('total_time')->nullable();
        });

        Schema::table('video_call_payments', function (Blueprint $table) {
            $table->time('total_time')->nullable();
        });

        Schema::table('video_call_requests', function (Blueprint $table) {
            $table->time('total_time')->nullable();
        });

        Schema::table('audio_call_requests', function (Blueprint $table) {
            $table->time('total_time')->nullable();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audio_call_payments', function (Blueprint $table) {
            $table->dropColumn("total_time");
        });

        Schema::table('video_call_payments', function (Blueprint $table) {
            $table->dropColumn("total_time");
        });

        Schema::table('video_call_requests', function (Blueprint $table) {
            $table->dropColumn("total_time");
        });

        Schema::table('audio_call_requests', function (Blueprint $table) {
            $table->dropColumn("total_time");
        });
    }
}
