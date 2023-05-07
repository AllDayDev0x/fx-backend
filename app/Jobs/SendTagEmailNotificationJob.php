<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Setting, Log;

use App\Models\User;

class SendTagEmailNotificationJob implements ShouldQueue
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
        try{

            $user = $this->data['user'];

            $tagged_users = $this->data['tagged_users'];

            $post_url = $this->data['post'];

            $message = $this->data['message'];

            $post_details = $this->data['post_details'];

            foreach ($tagged_users as $key => $value) {
                preg_match('/href=(["\'])([^\1]*)\1/i', $value, $m);
                if($m){
                    $tagged_users[$key] = str_replace(Setting::get('frontend_url'),'',$m[2]);
                    Log::info($tagged_users[$key]);
                    $tagged_user = User::where('unique_id',$tagged_users[$key])->first();

                    if($tagged_user){

                        $title = Setting::get('site_name');

                        $content = push_messages(604, $tagged_user->name ?? '');

                        if (Setting::get('is_email_notification') == YES) {

                            $email_data['subject'] = tr('tagged_email_notification' , Setting::get('site_name'));

                            $email_data['email']  = $tagged_user->email ?? "-";

                            $email_data['tagged_user_name']  = $tagged_user->name ?? "-";
                            
                            $email_data['name']  = $user->name ?? "-";

                            $email_data['message']  = $message;

                            $email_data['post_content'] = $post_details->content;

                            $email_data['post_url'] = $post_url;

                            $email_data['page'] = "emails.users.tag_email_notification";

                            dispatch(new \App\Jobs\SendEmailJob($email_data));

                        }

                        $data['from_user_id'] = $user->id;

                        $data['to_user_id'] = $tagged_user->id;
                      
                        $data['message'] = $message;

                        $data['action_url'] = Setting::get('BN_USER_COMMENT').$post_details->unique_id;

                        $data['notification_type'] = BELL_NOTIFICATION_TYPE_POST_COMMENT;

                        $data['image'] = $user->picture ?? asset('placeholder.jpeg');

                        $data['subject'] = $content;

                        dispatch(new BellNotificationJob($data));

                        if (Setting::get('is_push_notification') == YES) {

                            if($tagged_user->is_push_notification == YES && ($tagged_user->device_token != '')) {;

                                $push_data = [
                                    'content_id' =>$post_details->id,
                                    'notification_type' => BELL_NOTIFICATION_TYPE_POST_COMMENT,
                                    'content_unique_id' => $post_details->unique_id ?? 0,
                                ];

                                \Notification::send(
                                    $user->id, 
                                    new \App\Notifications\PushNotification(
                                        $title , 
                                        $content, 
                                        json_encode($push_data), 
                                        $user->device_token,
                                        Setting::get('BN_USER_COMMENT').$post_details->unique_id,
                                    )
                                );


                            }
                        }

                    }

                }
            }

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
