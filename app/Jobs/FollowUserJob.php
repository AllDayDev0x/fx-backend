<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Post;

use Carbon\Carbon;

use Log, Auth;

use Setting, Exception;

use App\Helpers\Helper;

use App\Models\User;

class FollowUserJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    protected $data;
    
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
    public function __construct($data)
    {
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

            $follower = $this->data['follower'];

            $title = Setting::get('site_name'); 

            $content = push_messages(602);

            $message = tr('user_follow_message', $follower->followerDetails->name ?? ''); 

            $data['from_user_id'] = $follower->follower_id;

            $data['to_user_id'] = $follower->user_id;
          
            $data['message'] = $message;

            $data['action_url'] =  Setting::get('BN_USER_FOLLOWINGS');
            
            $data['notification_type'] =  BELL_NOTIFICATION_TYPE_FOLLOW;

            $data['image'] = $follower->user->picture ?? asset('placeholder.jpeg');

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user = User::where('id', $follower->user_id)->first();

            if (Setting::get('is_push_notification') == YES && $user) {

                if($user->is_push_notification == YES && ($user->device_token != '')) {

                    $push_data = [
                        'content_id' => $follower->followerDetails->id,
                        'notification_type' => BELL_NOTIFICATION_TYPE_FOLLOW,
                        'content_unique_id' => $follower->followerDetails->unique_id,
                    ];

                    \Notification::send(
                        $user->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $user->device_token,
                            Setting::get('BN_USER_FOLLOWINGS'),
                        )
                    );

                }
            }  
            
            if (Setting::get('is_email_notification') == YES && $user) {
             
                $email_data['subject'] = tr('user_follow_message', $follower->followerDetails->name ?? ''); 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.users.follow-user";

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
