<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use DB, Schema;

class WatermarkLogoSeeder extends Seeder
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
		        'key' => 'is_watermark_logo_enabled',
		        'value' => NO
		    ],
    		[
		        'key' => 'watermark_logo',
		        'value' => asset('images/watermark.png')
            ],
            [
		        'key' => 'watermark_position',
		        'value' => WATERMARK_TOP_LEFT
		    ]
		]);
    }
}
