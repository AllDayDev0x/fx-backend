<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Log, Auth, Setting;

use App\Models\User;

class ReportJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        //
        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        try {

            $data = $this->data;

            $user = User::where('id', $data['user_id'])->first();

            if($data['type'] == WEELKY_REPORT){

                $message = tr('user_report_message', tr('weekly') ?? '');

            }elseif($data['type'] == MONTHLY_REPORT){

                $message = tr('user_report_message', tr('monthly') ?? '');

            }else{

                $message = tr('user_report_message', tr('custom') ?? '');

            }  
            
            if (Setting::get('is_email_notification') == YES && $user) {
                              
                $email_data['subject'] = tr('user_report_subject', $user->name ?? ''); 

                $email_data['message'] = $message;

                $email_data['page'] = "emails.users.report";

                $email_data['email'] = $user->email;

                $email_data['name'] = $user->name;

                $email_data['data'] = $user;

                $email_data['main'] = $data;

                dispatch(new SendEmailJob($email_data));


            }

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
