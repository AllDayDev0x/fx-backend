<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class VerifiedBadgeSeeder extends Seeder
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
		        'key' => 'is_user_allowed_verified_badge',
		        'value' => NO
		    ],

            [
                'key' => 'verified_badge_file',
                'value' => asset('images/verified.svg')
            ],
            [
                'key' => 'verified_badge_text',
                'value' => "Verified"
            ]
		]);
    }
}
