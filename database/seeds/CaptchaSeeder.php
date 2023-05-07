<?php

namespace Database\Seeders;

use DB;

use Illuminate\Database\Seeder;

class CaptchaSeeder extends Seeder
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
                'key' => 'NOCAPTCHA_SECRET_KEY',
                'value' => ''
            ],
            [
                'key' => 'NOCAPTCHA_SITE_KEY',
                'value' => ''
            ],
            [
                'key' => 'is_captcha_enabled',
                'value' => YES
            ]
        ]);
    }
}
