<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use Illuminate\Foundation\Auth\AuthenticatesUsers;

use Illuminate\Support\Facades\Auth;


class SupportMemberLoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:support_member', ['except' => ['logout']]);
    }

    /**
     * Show the application’s login form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showLoginForm()
    {

        return view('support_member.auth.login');
    }

    protected function guard() {

        return Auth::guard('support_member');;

    }
    
    public function login(Request $request) {
     
        // Validate the form data
        $this->validate($request, [
            'email'   => 'required|email',
            'password' => 'required|min:5'
        ]);
      
        // Attempt to log the user in
        if (Auth::guard('support_member')->attempt(['email' => $request->email, 'password' => $request->password])) {
              
            // if successful, then redirect to their intended location
            return redirect()->route('support_member.dashboard')->with('flash_success',tr('login_success'));

        }
     
        // if unsuccessful, then redirect back to the login with the form data
     
        return redirect()->back()->withInput($request->only('email', 'remember'))->with('flash_error', tr('username_password_not_match'));
    }

    public function logout() {

        Auth::guard('support_member')->logout();
        
        return redirect()->route('support_member.login')->with('flash_success',tr('logout_success'));
    }

}

