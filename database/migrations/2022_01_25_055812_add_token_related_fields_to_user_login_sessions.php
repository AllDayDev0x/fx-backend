<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTokenRelatedFieldsToUserLoginSessions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_login_sessions', function (Blueprint $table) {
            $table->string('token');
            $table->string('token_expiry');
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
            $table->dropColumn('token_expiry');
            $table->dropColumn('token');
        });
    }
}
