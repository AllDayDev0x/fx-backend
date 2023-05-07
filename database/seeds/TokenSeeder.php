<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class TokenSeeder extends Seeder
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
               'key' => 'token_symbol',
               'value' => 'Token'
           ],
           [
               'key' => 'token_amount',
               'value' => 1
           ],
           [
               'key' => 'tip_min_token',
               'value' => 1
           ],
           [
               'key' => 'tip_max_token',
               'value' => 10000
           ]
       ]);
    }
}
