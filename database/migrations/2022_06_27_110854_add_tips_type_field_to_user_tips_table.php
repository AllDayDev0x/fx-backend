<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTipsTypeFieldToUserTipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_tips', function (Blueprint $table) {
            $table->string('tips_type')->after('amount')->default(TIPS_TYPE_PROFILE);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_tips', function (Blueprint $table) {
            $table->dropColumn('tips_type');
        });
    }
}
