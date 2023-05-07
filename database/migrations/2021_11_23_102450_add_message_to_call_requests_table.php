<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMessageToCallRequestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('video_call_requests', function (Blueprint $table) {
            $table->string('message')->nullable()->after('status');
        });

        Schema::table('audio_call_requests', function (Blueprint $table) {
            $table->string('message')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('video_call_requests', function (Blueprint $table) {
            $table->dropColumn('message');
        });

        Schema::table('audio_call_requests', function (Blueprint $table) {
            $table->dropColumn('message');
        });
    }
}
