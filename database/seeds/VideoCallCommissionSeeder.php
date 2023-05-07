<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class VideoCallCommissionSeeder extends Seeder
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
		        'key' => 'video_call_admin_commission',
		        'value' => 10,
            ],
            [
                'key' => 'video_call_start_plus_minus',
                'value' => 10,
            ],
            [
                'key' => 'is_one_to_one_call_enabled',
                'value' => 1,
            ],
            [
                'key' => 'is_one_to_many_call_enabled',
                'value' => 1,
            ],
            [
                'key' => 'audio_call_admin_commission',
                'value' => 10,
            ],
            [
                'key' => 'audio_call_start_plus_minus',
                'value' => 10,
            ]

		]);
    }
}
