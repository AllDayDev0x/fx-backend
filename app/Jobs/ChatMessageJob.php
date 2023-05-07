<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Models\User;

use Log, Auth, Setting, Exception;

use App\Services\FCMService;

class ChatMessageJob implements ShouldQueue
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

            $chat_message = $this->data['chat_message'];

            $title = Setting::get('site_name');

            $content = $message = tr('user_chat_message_received', $chat_message->from_displayname ?? ''); 

            $user = User::where('id', $chat_message->to_user_id)->first();

            if (Setting::get('is_push_notification') == YES && $user) {

                if($user->is_push_notification == YES && ($user->device_token != '')) {

                    $push_data = [
                        'content_id' =>$chat_message->id,
                        'notification_type' => BELL_NOTIFICATION_TYPE_CHAT,
                        'content_unique_id' => $chat_message->unique_id ?? 0,
                        'message' => $chat_message->message,
                    ];

                    // \Notification::send(
                    //     $user->id, 
                    //     new \App\Notifications\PushNotification(
                    //         $title , 
                    //         $content, 
                    //         json_encode($push_data), 
                    //         $user->device_token,
                    //         Setting::get('BN_CHAT_MESSAGE'),
                    //     )
                    // );

                }
            }      

        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
