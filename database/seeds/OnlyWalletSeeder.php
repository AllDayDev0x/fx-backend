<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class OnlyWalletSeeder extends Seeder
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
                'key' => 'is_only_wallet_payment',
                'value' => YES
            ],
            [
                'key' => 'is_wallet_payment_enabled',
                'value' => 1
            ]
        ]);
    }
}
