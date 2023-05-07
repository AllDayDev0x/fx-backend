<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class ReferralSeeder extends Seeder
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
		        'key' => 'referral_earnings',
		        'value' => 10
		    ],
            [
                'key' => 'referrer_earnings',
                'value' => 10
            ],
            [
                'key' => 'is_referral_enabled',
                'value' => YES
            ],
		]);
    }
}
