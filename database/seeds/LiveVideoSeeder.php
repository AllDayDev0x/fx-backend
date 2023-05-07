<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class LiveVideoSeeder extends Seeder
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
		        'key' => 'live_streaming_placeholder_img',
		        'value' => asset('images/livestreaming_placeholder.jpg'),
            ],
            [
		        'key' => 'live_streaming_admin_commission',
		        'value' => 0,
            ],
            [
		        'key' => 'agora_app_id',
		        'value' => '',
            ],
            [
		        'key' => 'agora_certificate_id',
		        'value' => '',
		    ],
            [
                'key' => 'is_agora_configured',
                'value' => 0,
            ],
		]);
    }
}
