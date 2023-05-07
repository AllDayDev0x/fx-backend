<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Carbon\Carbon;
use Log, Auth;
use Setting, Exception;
use App\Helpers\Helper;
use App\Models\User;

class TipPaymentJob implements ShouldQueue
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

            $user_tips = (object) $this->data['user_tips'];

            $title = Setting::get("site_name");

            $from_user = User::find($user_tips->id);

            $content = push_messages(605, $from_user->name ?? '').' '.formatted_amount($user_tips->amount ?? 0.00);

            $message = tr('user_tips_message', formatted_amount($user_tips->amount ?? 0.00) )." ".$from_user->name ?? ''; 

            $data['from_user_id'] = $user_tips->id;

            $data['to_user_id'] = $user_tips->user_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_TIPS');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_SEND_TIP;

            $data['image'] = $user_tips->user->picture ?? asset('placeholder.jpeg');

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user_details = User::where('id', $user_tips->user_id)->first();

            if (Setting::get('is_push_notification') == YES && $user_details) {

                if($user_details->is_push_notification == YES && ($user_details->device_token != '')) {

                    $push_data = [
                        'content_id' =>$user_tips->id,
                        'notification_type' => BELL_NOTIFICATION_TYPE_SEND_TIP,
                        'content_unique_id' =>  $user_tips->unique_id ?? 0,
                    ];

                    \Notification::send(
                        $user_details->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $user_details->device_token,
                            Setting::get('BN_USER_TIPS'),
                        )
                    );


                }
            } 

            if (Setting::get('is_email_notification') == YES && $user_details) {
               
                $email_data['subject'] = tr('user_tips_message', formatted_amount($user_tips->amount ?? 0.00) )." ".$from_user->name ?? ''; 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.posts.tip_payment";

                $email_data['email'] = $user_details->email;

                $email_data['name'] = $user_details->name;

                $email_data['data'] = $user_details;

                $email_data['filename'] = 'Invoice'.date('m-d-Y_giA').'.pdf';

                $email_data['is_invoice'] = 1;

                dispatch(new SendEmailJob($email_data));

            }          


        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
