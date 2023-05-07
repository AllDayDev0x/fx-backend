<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class CoinPaymentSeeder extends Seeder
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
		        'key' => 'is_coinpayment_enabled',
		        'value' => NO
		    ]
		]);
    }
}
