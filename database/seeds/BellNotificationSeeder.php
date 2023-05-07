<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class BellNotificationSeeder extends Seeder
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
		        'key' => 'BN_USER_FOLLOWINGS',
		        'value' => "fans"
		    ],
		    [
		        'key' => 'BN_USER_COMMENT',
		        'value' => "post/"
		    ],
		    [
		        'key' => 'BN_USER_LIKE',
		        'value' => "post/"
		    ],
		    [
		        'key' => 'BN_USER_TIPS',
		        'value' => "payments"
		    ],
		    [
                'key' => 'BN_CHAT_MESSAGE',
                'value' => 'inbox'
            ],
            [
                'key' => 'BN_LIVE_VIDEO',
                'value' => 'live-videos'
            ],
            [
                'key' => 'BN_USER_VIDEO_CALL',
                'value' => "video-calls-history"
            ],
            [
                'key' => 'BN_USER_AUDIO_CALL',
                'value' => "audio-calls-history"
            ]
		]);
    }
}
