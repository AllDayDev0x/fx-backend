<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Validator, Log, Hash, Setting, DB, Exception, File;

use App\Helpers\Helper;

use App\Models\Settings;

use App\Models\User, App\Models\Admin;

class DemoController extends Controller
{
    /**
     * @method is_demo_enable_api()
     * 
     * @uses To check whether the Demo is Enabled or not
     * 
     * @created Subham Kant
     *
     * @usage - API for checking the demo Enabled or not
     *
     * @param object None
     * 
     * @return response of success/failure message with Enable or Disable data
     */
    public function is_demo_enable_api(Request $request) {

        try {

            $data['demo_control_status'] = Setting::get('is_demo_control_enabled') ?? [];

            $data['product'] = Setting::get('site_name') ?? [];

            return $this->sendResponse($message = tr('is_demo_enabled') , $code = '', $data);

        } catch (Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());
        }

    }

    /**
     * @method demo_control_status_update_api()
     * 
     * @uses To change the demo status
     * 
     * @created Subham Kant
     *
     * @usage - API for changing status of demo
     *
     * @param object None
     * 
     * @return response of success/failure message with Enable or Disable data
     */
    public function demo_control_status_update_api(Request $request) {

        try {

            DB::beginTransaction();

            $demo = Settings::where('key', 'is_demo_control_enabled')->first();

            if(!$demo) {

                throw new Exception(api_error(1002), 1002);
            }
            
            $demo->value = Setting::get('is_demo_control_enabled')==YES ? NO : YES;

            $demo->save();

            DB::commit();

            $data['demo_control_status'] = $demo->value;

            $data['site_name'] = Setting::get('site_name');

            return $this->sendResponse(api_success(242), 242, $data);

        } catch (Exception $e) {

            DB::rollback();

            $response_array = ['success'=>false, 'error_messages'=> $e->getMessage() , 'error_code'=>$e->getCode()];

            return response()->json($response_array, 200);
        }

    }

    /**
     * @method user_demo_update()
     * 
     * @uses used to update the user demo logins
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param (request) setting details
     *
     * @return success/error message
     */
    public function user_demo_update(Request $request) {

        try {

            DB::beginTransaction();

            $demo_key = Settings::where('key','demo_user_email')->first();
            
            if(!$demo_key) {

                throw new Exception('demo_user_email'.' '.api_error(602), 602);
            
            }

            $demo_key = Settings::where('key','demo_user_password')->first();
            
            if(!$demo_key) {

                throw new Exception('demo_user_password'.' '.api_error(602), 602);
            
            }

            $user_demo_email = Setting::get('demo_user_email');

            $user_demo_password = Setting::get('demo_user_password');

            if($user_demo_email && $user_demo_password) {

                $user = User::where('email',$user_demo_email)->first();

                $test_user = User::where('email','test@demo.com')->first();

                if(!$user || !$test_user) {

                    if(!$user) {

                        $user = new User;

                        $user->name = "User Demo";

                        $user->first_name = "User";

                        $user->last_name = "Demo";

                        $user->username = "user-demo";

                        $user->email = $user_demo_email;

                        $user->password = Hash::make($user_demo_password ?: "demo123");

                        $user->login_by = "Manual";

                        $user->save();

                    }

                    if(!$test_user) {

                        $test_user = new User;

                        $test_user->name = "Test Demo";

                        $test_user->first_name = "Test";

                        $test_user->last_name = "Demo";

                        $test_user->username = "test-demo";

                        $test_user->email = 'test@demo.com';

                        $test_user->password = Hash::make($user_demo_password ?: "demo123");

                        $test_user->login_by = "Manual";

                        $test_user->save();

                    }

                    DB::commit();

                    $data['user'] = $user;

                    $data['test_user'] = $test_user;

                    $response_array = ['success' => true, 'message' => api_success(806), 'data' => $data];

                    return response()->json($response_array, 200);

                }

                if(!Hash::check($user_demo_password , $user->password) || !Hash::check($user_demo_password , $test_user->password)){

                    if(!Hash::check($user_demo_password , $user->password)){

                        $user->password = Hash::make($user_demo_password ?: "demo123");

                        $user->save();

                    }

                    if(!Hash::check($user_demo_password , $test_user->password)){

                        $test_user->password = Hash::make($user_demo_password ?: "demo123");

                        $test_user->save();

                    }

                    DB::commit();

                    $data['user'] = $user;

                    $data['test_user'] = $test_user;

                    $response_array = ['success' => true, 'message' => api_success(804), 'data' => $data];

                    return response()->json($response_array, 200);

                }

            $data['user'] = $user;

            $data['test_user'] = $test_user;
                
            $response_array = ['success' => true, 'message' => api_success(805),'data' => $data];

            return response()->json($response_array, 200);

            }

            throw new Exception(api_error(601), 601);
            

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method user_demo_login_check()
     * 
     * @uses to check user login details
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param (request) setting details
     *
     * @return success/error message
     */
    public function user_demo_login_check(Request $request) {

        try {

            $user_demo_email = Setting::get('demo_user_email');

            $user_demo_password = Setting::get('demo_user_password');

            $data['user'] = User::where('email',$user_demo_email)->first();

            $data['test'] = User::where('email','test@demo.com')->first();

            $data['demo_email'] = $user_demo_email;

            $data['demo_password'] = $user_demo_password;

            if($data['user'] && $data['test']) {

                if(!Hash::check($user_demo_password,$data['user']->password) || !Hash::check($user_demo_password,$data['test']->password)){

                    $response_array = ['success' => false, 'message' => api_error(605),'data' => $data];

                    return response()->json($response_array, 200);
                    
                }

                $response_array = ['success' => true, 'message' => api_success(805),'data' => $data];

                return response()->json($response_array, 200);

            }

            $response_array = ['success' => false, 'message' => api_error(601),'data' => $data];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method admin_demo_update()
     * 
     * @uses used to update the admin demo logins
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param (request) setting details
     *
     * @return success/error message
     */
    public function admin_demo_update(Request $request) {

        try {

            DB::beginTransaction();

            $demo_key = Settings::where('key','demo_admin_email')->first();
            
            if(!$demo_key) {

                throw new Exception('demo_admin_email'.' '.api_error(602), 602);
            
            }

            $demo_key = Settings::where('key','demo_admin_password')->first();
            
            if(!$demo_key) {

                throw new Exception('demo_admin_password'.' '.api_error(602), 602);
            
            }

            $admin_demo_email = Setting::get('demo_admin_email');

            $admin_demo_password = Setting::get('demo_admin_password');

            if($admin_demo_email && $admin_demo_password) {

                $admin = Admin::where('email',$admin_demo_email)->first();

                $test_admin = Admin::where('email','test@demo.com')->first();

                if(!$admin || !$test_admin) {

                    if(!$admin) {

                        $admin = new Admin;

                        $admin->unique_id = 'admin-demo';

                        $admin->email = $admin_demo_email;

                        $admin->name = 'Admin';

                        $admin->password = Hash::make($admin_demo_password ?: "demo123");

                        $admin->save();

                    }

                    if(!$test_admin) {

                        $test_admin = new Admin;

                        $test_admin->unique_id = 'test-demo';

                        $test_admin->email = 'test@demo.com';

                        $test_admin->name = 'Test';

                        $test_admin->password = Hash::make($admin_demo_password ?: "demo123");

                        $test_admin->save();

                    }

                    DB::commit();

                    $data['admin'] = $admin;

                    $data['test_admin'] = $test_admin;

                    $response_array = ['success' => true, 'message' => api_success(802), 'data' => $data];

                    return response()->json($response_array, 200);

                }

                if(!Hash::check($admin_demo_password , $admin->password) || !Hash::check($admin_demo_password , $test_admin->password)){

                    if(!Hash::check($admin_demo_password , $admin->password)){

                        $admin->password = Hash::make($admin_demo_password ?: "demo123");

                        $admin->save();

                    }

                    if(!Hash::check($admin_demo_password , $test_admin->password)){

                        $test_admin->password = Hash::make($admin_demo_password ?: "demo123");

                        $test_admin->save();

                    }

                    DB::commit();

                    $data['admin'] = $admin;

                    $data['test_admin'] = $test_admin;

                    $response_array = ['success' => true, 'message' => api_success(803), 'data' => $data];

                    return response()->json($response_array, 200);

                }

            $data['admin'] = $admin;

            $data['test_admin'] = $test_admin;
                
            $response_array = ['success' => true, 'message' => api_success(801),'data' => $data];

             return response()->json($response_array, 200);

            }

            throw new Exception(api_error(601), 601);
            

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }

    /**
     * @method admin_demo_login_check()
     * 
     * @uses to check admin login details
     *
     * @created Subham Kant
     *
     * @updated 
     *
     * @param (request) setting details
     *
     * @return success/error message
     */
    public function admin_demo_login_check(Request $request) {

        try {

            $admin_demo_email = Setting::get('demo_admin_email');

            $admin_demo_password = Setting::get('demo_admin_password');

            $data['admin'] = Admin::where('email',$admin_demo_email)->first();

            $data['test'] = Admin::where('email','test@demo.com')->first();

            $data['demo_email'] = $admin_demo_email;

            $data['demo_password'] = $admin_demo_password;

            if($data['admin'] && $data['test']) {

                if(!Hash::check($admin_demo_password,$data['admin']->password) || !Hash::check($admin_demo_password,$data['test']->password)){

                    $response_array = ['success' => false, 'message' => api_error(605),'data' => $data];

                    return response()->json($response_array, 200);
                    
                }

                $response_array = ['success' => true, 'message' => api_success(801),'data' => $data];

                return response()->json($response_array, 200);

            }

            $response_array = ['success' => false, 'message' => api_error(601),'data' => $data];

            return response()->json($response_array, 200);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }

    }
}
