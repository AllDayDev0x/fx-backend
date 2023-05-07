<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Mail\Mailable;

use Exception;

use App\Mail\SendEmail;

use DB, Hash, Setting, Auth, Validator, Enveditor,Log;

use Mailgun\Mailgun;

use App\Mail\InvoiceMail;


class SendEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

   protected $email_data;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 2;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($email_data)
    {
        $this->email_data = $email_data; 


    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            
            if(isset($this->email_data['is_invoice'])){

                $mail_model = new InvoiceMail($this->email_data);

            } else{
                
                $mail_model = new SendEmail($this->email_data);

            }

            $isValid = 1;

            Log::info("mailer - ".$this->email_data['email']);

            if(envfile('MAIL_MAILER') == 'mailgun' && envfile('MAILGUN_SECRET')!='' && Setting::get('is_mailgun_email_validate') == YES) {

                Log::info("isValid - START");

                # Instantiate the client.

                $email_address = Mailgun::create(envfile('MAILGUN_SECRET'));

                $validateAddress = $this->email_data['email'];

                // 'https://api.mailgun.net/v4/address/validate'
                # Issue the call to the client.

                $result =  $email_address->domains()->verify($validateAddress);

                $isValid = $result->http_response_body->is_valid;

                Log::info("isValid FINAL STATUS - ".$isValid);

            }

            if($isValid) {

                \Mail::queue($mail_model);

                Log::info("EmailJob Success");
            }

        } catch(Exception $e) {

            Log::info("SendEmailJob Error".print_r($e->getMessage(), true));

        }

    }
}
