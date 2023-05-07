<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class CartSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        
        if(Schema::hasTable('settings')) {

            DB::table('settings')->insert([
                [
                    'key' => 'buy_single_user_products',
                    'value' => 0
                ],
            ]);

        }

    }
}
