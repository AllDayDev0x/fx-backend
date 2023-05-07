<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class NotificationCountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
    		[
		        'key' => 'is_notification_count_enabled',
		        'value' => 0
		    ],
		    [
		        'key' => 'notification_time',
		        'value' => ''
		    ]
		]);
    }
}
