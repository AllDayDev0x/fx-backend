<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class AdsSeeder extends Seeder
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
		        'key' => 'is_ads_enabled',
		        'value' => 0
		    ],
		    [
		        'key' => 'header_ad',
		        'value' => ''
		    ],
		    [
		        'key' => 'footer_ad',
		        'value' => ''
		    ],
		    [
		        'key' => 'sidebar_ad',
		        'value' => ''
		    ]
		]);
    }
}
