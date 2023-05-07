<?php

namespace App\Http\Middleware;

use Closure;

class UserEmailMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {

        if($request->id && $request->token) {
        
            $user = \App\Models\User::find($request->id);

            if(!$user) {
                
                $response = ['success' => false, 'error' => api_error(1002), 'error_code' => 1002];

                return response()->json($response, 200);

            }

            if($user->is_email_verified == USER_EMAIL_NOT_VERIFIED) {

                if(\Setting::get('is_account_email_verification') && !in_array($user->login_by, ['facebook', 'google', 'apple', 'linkedin', 'instagram'])) {

                    // Check the verification code expiry

                    // \App\Helpers\Helper::check_email_verification("", $user, $error, USER);
                
                    $response = ['success' => false, 'error' => api_error(1001), 'error_code' => 156];

                    return response()->json($response, 200);

                }
            
            }
        }


        return $next($request);
    }
}
