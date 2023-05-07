<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeviceUniqueIdFieldToUserLoginSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_login_sessions', function (Blueprint $table) {
            $table->text('device_unique_id')->after('device_model')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_login_sessions', function (Blueprint $table) {
            $table->dropColumn('device_unique_id');
        });
    }
}
