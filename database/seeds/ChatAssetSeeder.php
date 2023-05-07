<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class ChatAssetSeeder extends Seeder
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
		        'key' => 'is_chat_asset_enabled',
		        'value' => YES
		    ]
		]);
    }
}
