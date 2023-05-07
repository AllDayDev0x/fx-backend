<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\User;

use Log, Auth;

use Setting, Exception;

use App\Helpers\Helper;

class AudioCallRequestSentJob implements ShouldQueue
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
        //
        try {

            $audio_call_request = $this->data['audio_call_request'];

            $title = Setting::get('site_name');

            $content = push_messages(608);

            $message = tr('audio_call_request_sent_message');

            $message = str_replace("<%user%>",$audio_call_request->user->name ?? '' ,$message);

            $data['from_user_id'] = $audio_call_request->user_id;

            $data['to_user_id'] = $audio_call_request->model_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_AUDIO_CALL');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_AUDIO_CALL;

            $data['image'] = $audio_call_request->user->picture ?? asset('placeholder.jpeg');

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user = User::where('id', $audio_call_request->model_id)->first();

            if (Setting::get('is_push_notification') == YES && $user) {

                if($user->is_push_notification == YES && ($user->device_token != '')) {

                    $push_data = [
                        'content_id' => $audio_call_request->id,
                        'notification_type' => BELL_NOTIFICATION_TYPE_AUDIO_CALL,
                        'content_unique_id' => $audio_call_request->unique_id,
                    ];

                    \Notification::send(
                        $user->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $user->device_token,
                            Setting::get('BN_USER_AUDIO_CALL'),
                        )
                    );

                }
            }        
            
            
            if (Setting::get('is_email_notification') == YES && $user) {
               
                $email_data['subject'] = $title;
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.users.audio-call-status";

                $email_data['email'] = $user->email;

                $email_data['name'] = $user->name;

                $email_data['data'] = $user;

                dispatch(new SendEmailJob($email_data));

            }

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
