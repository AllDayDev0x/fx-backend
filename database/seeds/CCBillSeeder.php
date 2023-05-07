<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class CCBillSeeder extends Seeder
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
			        'key' => 'is_ccbill_enabled',
			        'value' => 0
			    ],
	        	[
			        'key' => 'ccbill_url',
			        'value' => "",
			    ],
	    		[
			        'key' => 'ccbill_account_number',
			        'value' => "",
			    ],
			    [
			        'key' => 'ccbill_sub_account_number',
			        'value' => "",
			    ],
			    [
			        'key' => 'flex_form_id',
			        'value' => "",
			    ],
			    [
			        'key' => 'salt_key',
			        'value' => "",
			    ],			    
			]);

		}
    }
}
