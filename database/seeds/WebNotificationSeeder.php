<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class WebNotificationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        \DB::table('settings')->insert([
            [
		        'key' => 'is_web_notification_enabled',
		        'value' => NO
		    ]
		]);
    }
}
