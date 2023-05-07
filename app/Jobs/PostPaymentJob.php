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

class PostPaymentJob implements ShouldQueue
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

            $request = (object) $this->data['post_payments'];

            $title = Setting::get('site_name'); 

            $post_payments = \App\Models\PostPayment::where('user_id',$request->id)->where('post_id',$request->post_id)->first();

            $from_user = User::find($post_payments->user_id);

            $paid_amount = \Setting::get('is_only_wallet_payment') ? $post_payments->token : $post_payments->paid_amount;

            $content = $from_user->name.push_messages(606, formatted_amount($paid_amount ?? 0.00));

            $message = tr('post_payments_message', formatted_amount($paid_amount ?? 0.00) )." ".$from_user->name ?? ''; 

            $data['from_user_id'] = $post_payments->user_id ?? '';

            $data['to_user_id'] = $post_payments->postDetails->user_id ?? '';
          
            $data['message'] = $message;

            $data['action_url'] = Setting::get('BN_USER_TIPS');

            $data['notification_type'] = BELL_NOTIFICATION_TYPE_POST_PAYMENT;

            $data['image'] = $post_payments->user->picture ?? asset('placeholder.jpeg');

            $data['post_id'] = $post_payments->post_id ?? '';

            $data['subject'] = $content;

            dispatch(new BellNotificationJob($data));

            $user_details = User::where('id', $data['to_user_id'])->first();

            if (Setting::get('is_push_notification') == YES && $user_details) {

                if($user_details->is_push_notification == YES && ($user_details->device_token != '')) {

                    $push_data = [
                        'content_id' =>$post_payments->post_id ?? '',
                        'notification_type' => BELL_NOTIFICATION_TYPE_POST_PAYMENT,
                        'content_unique_id' => $post_payments->postDetails->post_unique_id ?? '',
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
               
                $email_data['subject'] = tr('post_payments_message', formatted_amount($post_payments->paid_amount ?? 0.00) )." ".$from_user->name ?? ''; 
               
                $email_data['message'] = $message;

                $email_data['page'] = "emails.posts.post_payment";

                $email_data['email'] = $user_details->email;

                $email_data['name'] = $user_details->name;

                $email_data['data'] = $user_details;

                dispatch(new SendEmailJob($email_data));

            }
            
            



        } catch(Exception $e) {

            Log::info("Error ".print_r($e->getMessage(), true));

        }
    }
}
