<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB;

class SymbolPositionSeeder extends Seeder
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
                'key' => 'symbol_position',
                'value' => SUFFIX,
            ]
        ]);
    }
}
