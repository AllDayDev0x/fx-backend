<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class PlaceholderSeeder extends Seeder
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
                'key' => 'profile_placeholder',
                'value' => asset('placeholder.jpeg')
            ],
            [
                'key' => 'cover_placeholder',
                'value' => asset('cover.jpg')
            ],
            [
                'key' => 'post_image_placeholder',
                'value' => asset('images/post_image_placeholder.jpg')
            ],
            [
                'key' => 'video_call_placeholder',
                'value' => asset('images/video_call_placeholder.jpg')
            ],
            [
                'key' => 'audio_call_placeholder',
                'value' => asset('images/audio_call_placeholder.jpg')
            ],
            [
                'key' => 'ppv_image_placeholder',
                'value' => asset('images/ppv_image_placeholder.jpg')
            ],
            [
                'key' => 'ppv_audio_placeholder',
                'value' => asset('images/ppv_audio_placeholder.jpg')
            ],
            [
                'key' => 'ppv_video_placeholder',
                'value' => asset('images/ppv_video_placeholder.jpg')
            ]
        ]);
    }
}
