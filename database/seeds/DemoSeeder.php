<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Helpers\Helper;

use DB, Schema;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Schema::hasTable('admins')) {

            $check_admin_details = DB::table('admins')->where('email' , 'demo@demo.com')->count();

            if(!$check_admin_details) {

            	DB::table('admins')->insert([
            		[
        		        'name' => 'Admin',
                        'unique_id' => 'admin-demo',
        		        'email' => 'demo@demo.com',
                        'about' => 'About',
        		        'password' => \Hash::make('demo123'),
        		        'picture' => asset('placeholder.jpeg'),
                        'status' => 1,
                        'timezone' => 'Asia/Kolkata',
        		        'created_at' => date('Y-m-d H:i:s'),
        		        'updated_at' => date('Y-m-d H:i:s')
        		    ]
                ]);

            }

            $check_test_admin_details = DB::table('admins')->where('email' , 'test@demo.com')->count();

            if(!$check_test_admin_details) {

                DB::table('admins')->insert([

                    [
                        'name' => 'Test',
                        'unique_id' => 'admin-demo',
                        'email' => 'test@demo.com',
                        'password' => \Hash::make('demo123'),
                        'about' => 'About',
                        'picture' => asset('placeholder.jpeg'),
                        'status' => 1,
                        'timezone' => 'Asia/Kolkata',
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
    		    ]);
            }
        
        }

        if(Schema::hasTable('users')) {

            $check_admin_details = DB::table('users')->where('email' , 'demo@demo.com')->count();

            if(!$check_admin_details) {

                DB::table('users')->insert([
                    [
                        'name' => 'User',
                        'first_name' => 'User',
                        'last_name' => 'Demo',
                        'unique_id' => 'user-demo',
                        'username' => 'user-demo',
                        'email' => 'demo@demo.com',
                        'password' => \Hash::make('demo123'),
                        'picture' => asset('placeholder.jpeg'),
                        'login_by' => 'manual',
                        'mobile' => '9836367763',
                        'device_type' => 'web',
                        'status' => USER_APPROVED,
                        'is_email_verified' => USER_EMAIL_VERIFIED,
                        'token' => Helper::generate_token(),
                        'token_expiry' => Helper::generate_token_expiry(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]
                ]);

            }

            $check_test_admin_details = DB::table('users')->where('email' , 'test@demo.com')->count();

            if(!$check_test_admin_details) {

                DB::table('users')->insert([
                    [
                        'name' => 'Test',
                        'first_name' => 'User',
                        'last_name' => 'Test',
                        'unique_id' => 'user-test',
                        'username' => 'user-test',
                        'email' => 'test@demo.com',
                        'password' => \Hash::make('demo123'),
                        'picture' => asset('placeholder.jpeg'),
                        'login_by' => 'manual',
                        'mobile' => '9836367763',
                        'device_type' => 'web',
                        'status' => USER_APPROVED,
                        'is_email_verified' => USER_EMAIL_VERIFIED,
                        'token' => Helper::generate_token(),
                        'token_expiry' => Helper::generate_token_expiry(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ],
                ]);
            }
        
        }

    }
}
