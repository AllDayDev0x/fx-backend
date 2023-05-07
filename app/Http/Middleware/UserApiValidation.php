<?php

namespace App\Http\Middleware;

use Closure;

use Illuminate\Http\Request;

use Validator, DB, Setting, Log, Helper;

use App\Models\User;
use Cache;
use Carbon\Carbon;

class UserApiValidation {
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {

        if(!$request->device_unique_id) {

            $request->request->add(['device_unique_id' => $request->device_model]);
        }
        
        $basic_rules = [
                        'token' => 'required|min:5',
                        'id' => 'required|integer|exists:users,id',
                        'device_model' => 'nullable',
                        'device_unique_id' => 'required',
                    ];

        $custom_errors = [
                    'id' => api_error(1005),
                    'exists' => api_error(1002)
                    ];

        $validator = Validator::make($request->all(), $basic_rules, $custom_errors);

        if($validator->fails()) {

            $error = implode(',', $validator->messages()->all());

            $response = ['success' => false, 'error' => $error, 'error_code' => 1002];

            return response()->json($response, 200);

        } else {

            $token = $request->token; $user_id = $request->id;

            $device_model = $request->device_model;

            $device_unique_id = $request->device_unique_id;

            if (!Helper::is_token_valid(USER, $user_id, $token, $device_unique_id, $error)) {

                $response = ['success' => false, 'error' => $error, 'error_code' => 1003];

                return response()->json($response, 200);

            } else {

                $user = User::find($request->id);

                if(!$user) {
                    
                    $response = ['success' => false, 'error' => api_error(1002), 'error_code' => 1002];

                    return response()->json($response, 200);

                }

                if(in_array($user->status , [USER_DECLINED , USER_PENDING])) {
                    
                    $response = ['success' => false, 'error' => api_error(1000), 'error_code' => 1000];

                    return response()->json($response, 200);
               
                }
            }
       
        }

        $expiresAt = Carbon::now()->addMinutes(Setting::get('user_online_status_limit') ?: 1);
        
        Cache::put($request->id, true, $expiresAt);

        return $next($request);
    }
}
