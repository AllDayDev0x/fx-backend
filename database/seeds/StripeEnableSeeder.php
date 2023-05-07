<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class StripeEnableSeeder extends Seeder
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
                'key' => 'is_stripe_enabled',
                'value' => YES,
            ]
        ]);
    }
}
