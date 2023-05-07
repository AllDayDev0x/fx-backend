<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddV6AdditionalFields extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_login_sessions', function (Blueprint $table) {
            $table->text("device_token")->nullable()->after("device_type");
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
            $table->dropColumn("device_token");
        });
    }
}
