<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class PaypalSeeder extends Seeder
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
		        'key' => 'is_paypal_enabled',
		        'value' => YES
		    ],
		    [
		        'key' => 'PAYPAL_ID',
		        'value' => 'AaXkweZD5g9s0X3BsO0Y4Q-kNzbmLZaog0mbmVGrTT5IX0O73LoLVcHp17e6pkG7Vm04JEUuG6up30LD'
		    ],
		    [
		        'key' => 'PAYPAL_SECRET',
		        'value' => ''
		    ],
		    [
		        'key' => 'PAYPAL_MODE',
		        'value' => 'sandbox'
		    ]
		]);
    }
}
