<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBrowserTypeToUserLoginSessionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_login_sessions', function (Blueprint $table) {
            $table->string('browser_type')->nullable()->after('device_model');
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
            $table->dropColumn('browser_type');
        });
    }
}
