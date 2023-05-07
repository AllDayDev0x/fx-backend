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

class LiveVideoNotificationToFollower implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $logged_in_user_id;

    protected $live_video;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($logged_in_user_id, $live_video)
    {
        //
        $this->logged_in_user_id = $logged_in_user_id;

        $this->live_video = $live_video;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
        $user = \App\Models\User::find($this->logged_in_user_id);

        $user_subscribed = \App\Models\UserSubscriptionPayment::where('to_user_id',$this->logged_in_user_id)->where('is_current_subscription',YES)->pluck('from_user_id')->toArray();

        $base_query = \App\Models\Follower::leftJoin('users', 'users.id', '=', 'followers.user_id')
            ->select('followers.*')
            ->where('user_id', $this->logged_in_user_id)
            ->where('users.status', APPROVED)
            ->whereIn('follower_id',$user_subscribed)
            ->where('users.is_email_verified', YES)
            ->orderBy('followers.created_at', 'desc');

       
            $base_query->chunk(30,function($followers) use ($user) {

                $title = Setting::get('site_name');

                $content = tr('user_live_started_message', $user->name ?? '');

                $message = tr('user_live_started_message', $user->name ?? ''); 

                foreach ($followers as $key => $value) {

                    $data['from_user_id'] = $value->follower_id;

                    $data['to_user_id'] = $value->user_id;
                  
                    $data['message'] = $message;
        
                    $data['action_url'] =  Setting::get('BN_USER_LIKE');
        
                    $data['image'] = $value->user->picture ?? asset('placeholder.jpeg');
        
                    $data['subject'] = $content;
        
                    dispatch(new BellNotificationJob($data));
    
                    $user = User::where('id', $value->follower_id)->first();

                    if (Setting::get('is_push_notification') == YES && $user) {

                        if($user->is_push_notification == YES && ($user->device_token != '')) {
        
                            $push_data = [
                                'content_id' => $this->live_video->id,
                                'notification_type' => BELL_NOTIFICATION_TYPE_LIVE_VIDEO,
                                'content_unique_id' => $this->live_video->unique_id,
                            ];
                    
                            \Notification::send(
                                $user->id, 
                                new \App\Notifications\PushNotification(
                                    $title , 
                                    $content, 
                                    json_encode($push_data), 
                                    $user->device_token,
                                    Setting::get('BN_LIVE_VIDEO'),
                                )
                            );
        
                        }
                    }

                    if (Setting::get('is_email_notification') == YES && $user) {
               
                        $email_data['subject'] = tr('new_video_streaming');
                       
                        $email_data['message'] = $message;
        
                        $email_data['page'] = "emails.users.live_video_notification";
        
                        $email_data['email'] = $user->email;
        
                        $email_data['name'] = $user->name;
        
                        $email_data['data'] = $user;
        
                        dispatch(new SendEmailJob($email_data));
        
                    }
    
    
                }
            });
        

       
    }
}
