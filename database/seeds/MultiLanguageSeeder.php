<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class MultiLanguageSeeder extends Seeder
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
		        'key' => 'is_multilanguage_enabled',
		        'value' => YES
		    ]
		]);
    }
}
