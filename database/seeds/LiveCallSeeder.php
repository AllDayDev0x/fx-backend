<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class LiveCallSeeder extends Seeder
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
		        'key' => 'min_token_call_charge',
		        'value' => 10
		    ],
		]);
    }
}
