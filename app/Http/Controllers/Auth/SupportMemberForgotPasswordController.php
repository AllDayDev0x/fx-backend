<?php


namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;

use DB, Auth, Hash, Validator, Exception;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

use Illuminate\Http\Request;

use Illuminate\Notifications\notify;

use Password;

class SupportMemberForgotPasswordController extends Controller
{
    
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest:support_member');
    }

    public function showLinkRequestForm() {
        
        try {

            $is_email_configured = YES;

            if(!envfile('MAIL_USERNAME') || !envfile('MAIL_PASSWORD') || !envfile('MAIL_FROM_ADDRESS') || !envfile('MAIL_FROM_NAME')) {

                $is_email_configured = NO;

                // throw new Exception(tr('email_not_configured'), 101);
                
            }

            return view('support_member.auth.forgot')->with('is_email_configured', $is_email_configured);

        } catch(Exception $e){ 

            return redirect()->route('support_member.login')->with('flash_error', $e->getMessage());

        } 
    }

    protected function broker() {

        return Password::broker('support_members');
    }

    public function sendPasswordResetNotification($token)
    {

        $this->notify(new App\Notifications\CustomResetPasswordNotification($token));
    }

}
