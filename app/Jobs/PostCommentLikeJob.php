<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\Post, App\Models\User;

use Carbon\Carbon;

use Log, Auth, Setting, Exception;

use App\Helpers\Helper;

class PostCommentLikeJob implements ShouldQueue
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

            $post_comment_like = $this->data['post_comment_like'];

            $post = $this->data['post'];

            $title = Setting::get('site_name');

            $content = push_messages(607);

            $message = tr('user_post_comment_like_message', $post_comment_like->User->name ?? ''); 

            $data['from_user_id'] = $post_comment_like->user_id;

            $data['to_user_id'] = $post_comment_like->post_user_id;

            $data['post_id'] = $post_comment_like->post_id;
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_LIKE');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_POST_COMMENT;

            $data['image'] = $post_comment_like->User->picture ?? asset('placeholder.jpeg');

            $data['subject'] = $content;

            $data['type'] = BELL_NOTIFICATION_TYPE_LIKE;

            dispatch(new BellNotificationJob($data));

            $user = User::where('id', $post_comment_like->post_user_id)->first();

            if (Setting::get('is_push_notification') == YES && $user) {

                if($user->is_push_notification == YES && ($user->device_token != '')) {

                    $push_data = [
                        'content_id' =>$post->id,
                        'notification_type' => BELL_NOTIFICATION_TYPE_POST_COMMENT,
                        'content_unique_id' =>  $post->unique_id ?? 0,
                    ];

                    \Notification::send(
                        $user->id, 
                        new \App\Notifications\PushNotification(
                            $title , 
                            $content, 
                            json_encode($push_data), 
                            $user->device_token,
                            Setting::get('BN_USER_LIKE'),
                        )
                    );

                }
            }      

            if (Setting::get('is_email_notification') == YES && $user) {

                $email_data['subject'] = tr('user_post_comment_like_message', $post_comment_like->User->name ?? ''); 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.posts.post_like";

                $email_data['email'] = $user->email;

                $email_data['name'] = $user->name;

                $email_data['data'] = $user;

                Log::info("message_save".print_r($email_data['email'], true));

                dispatch(new SendEmailJob($email_data));


            }

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
