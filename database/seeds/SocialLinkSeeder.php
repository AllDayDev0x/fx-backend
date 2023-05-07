<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class SocialLinkSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('settings')->insert([
            [
                'key' => 'snapchat_link',
                'value' => ''
            ],
            [
                'key' => 'youtube_link',
                'value' => ''
            ],
            [
                'key' => 'google_plus_link',
                'value' => ''
            ]
        ]);
    }
}
