<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User, App\Models\VideoCallRequest;

use App\Repositories\CommonRepository as CommonRepo;

use Carbon\Carbon;

class VideoCallRepository {

    /**
     * @method video_call_requests_list_response()
     *
     * @uses used to format the live videos response
     *
     * @created Vithya R
     * 
     * @updated Vithya R
     *
     * @param object $live_videos, object $request
     *
     * @return object $live_videos
     */

    public static function video_call_requests_list_response($video_call_requests, $request,$timezone) {

        foreach($video_call_requests as $video_call_request){                

            if(Setting::get('is_only_wallet_payment')) {

                $video_call_token = $video_call_request->videoCallPayments->token ?? 0;

                $video_call_request->amount = $video_call_token ? : $video_call_request->model->video_call_token ?? 0;

            } else {

                $video_call_request->amount = $video_call_request->model->video_call_amount ?? 0;
            }
            
            $video_call_request->accept_btn_status = $video_call_request->reject_btn_status =$video_call_request->start_btn_status = $video_call_request->end_btn_status = $video_call_request->cancel_btn_status = $video_call_request->join_btn_status = $video_call_request->payment_btn_status = $video_call_request->is_user_needs_to_pay = NO;

            $video_call_payment = \App\Models\VideoCallPayment::where('video_call_request_id',$video_call_request->id)->PaidApproved()->first();

            $payment_status = CommonRepo::video_call_payment_check($video_call_request);

            $is_paid = $video_call_request->amount > 0 && $video_call_payment ? YES : NO;

            $payment_status = NO;

            if($request->id == $video_call_request->model_id) {

                $video_call_request->accept_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_SENT ? YES : NO;

                $video_call_request->reject_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_SENT ? YES : NO;

                // $video_call_request->start_btn_status = $payment_status == NO && $video_call_request->call_status == VIDEO_CALL_REQUEST_ACCEPTED ? YES : NO;

                $video_call_request->start_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_ACCEPTED ? YES : NO;

                $video_call_request->end_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_JOINED ? YES : NO;

                $video_call_request->cancel_btn_status = in_array($video_call_request->call_status, [VIDEO_CALL_REQUEST_ACCEPTED]) ? YES : NO;

            } else {

                $video_call_request->is_user_needs_to_pay = CommonRepo::video_call_payment_check($video_call_request);

                if($payment_status == YES && $video_call_request->call_status == VIDEO_CALL_REQUEST_ACCEPTED) {

                    // $video_call_request->payment_btn_status = YES;

                    $video_call_request->payment_btn_status = NO;

                }

                $video_call_request->join_btn_status = in_array($video_call_request->call_status, [ VIDEO_CALL_REQUEST_JOINED]) && $payment_status == NO ? YES : NO;

            }

            if($video_call_request->start_btn_status == YES) {

                // Check the schedule time

                $video_call_start_plus_minus = Setting::get('video_call_start_plus_minus', 10);

                $sub_start_time = Carbon::parse($video_call_request->start_time)->subMinutes($video_call_start_plus_minus);

                $add_start_time = Carbon::parse($video_call_request->start_time)->addMinutes($video_call_start_plus_minus);

                if(now() < $sub_start_time) {

                    $video_call_request->start_btn_status = NO;

                } elseif(now() > $add_start_time) {

                    $video_call_request->start_btn_status = NO;

                    self::end_video_call($video_call_request->id);

                    $video_call_request->refresh();
                }

            }

            $video_call_request->is_user_needs_to_pay = CommonRepo::video_call_payment_check($video_call_request);

            $video_call_request->payment_status = $video_call_payment || $payment_status ? YES : NO;

            $video_call_request->is_model = ($request->id == $video_call_request->model_id) ? YES:NO;

            $video_call_request->call_status_formatted = call_status_formatted($video_call_request->call_status, $video_call_request->is_model, $video_call_request->payment_status,$is_paid);

            $video_call_request->amount_formatted = formatted_amount($video_call_request->amount ?? 0);

            $video_call_request->start_time = common_date($video_call_request->start_time, $timezone);

            $video_call_request->end_time = common_date($video_call_request->end_time, $timezone);

        }

        return $video_call_requests;
    
    }

    /**
     * @method video_call_requests_single_response()
     *
     * @uses used to format the live videos response
     *
     * @created Vithya R
     * 
     * @updated Vithya R
     *
     * @param object $live_videos, object $request
     *
     * @return object $live_videos
     */

    public static function video_call_requests_single_response($video_call_request, $request,$timezone) {

        if(Setting::get('is_only_wallet_payment')) {
            $video_call_request->amount = $video_call_request->model->video_call_token ?? 0;
        } else {
            $video_call_request->amount = $video_call_request->model->video_call_amount ?? 0;
        }

        $video_call_request->accept_btn_status = $video_call_request->reject_btn_status =$video_call_request->start_btn_status = $video_call_request->end_btn_status = $video_call_request->cancel_btn_status = $video_call_request->join_btn_status = $video_call_request->payment_btn_status = $video_call_request->is_user_needs_to_pay = $video_call_request->is_owner = NO;

        $video_call_payment = \App\Models\VideoCallPayment::where('video_call_request_id',$video_call_request->id)->PaidApproved()->first();

        $payment_status = CommonRepo::video_call_payment_check($video_call_request);

        $payment_status = NO;

        $is_paid = $video_call_request->amount > 0 && $video_call_payment ? YES : NO;

        if($request->id == $video_call_request->model_id) {

            $video_call_request->accept_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_SENT ? YES : NO;

            $video_call_request->reject_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_SENT ? YES : NO;

            // $video_call_request->start_btn_status = $payment_status == NO && $video_call_request->call_status == VIDEO_CALL_REQUEST_ACCEPTED ? YES : NO;

            $video_call_request->start_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_ACCEPTED ? YES : NO;

            $video_call_request->end_btn_status = $video_call_request->call_status == VIDEO_CALL_REQUEST_JOINED ? YES : NO;

            $video_call_request->cancel_btn_status = in_array($video_call_request->call_status, [VIDEO_CALL_REQUEST_ACCEPTED]) ? YES : NO;

            $video_call_request->is_owner = YES;

        } else {

            $video_call_request->is_user_needs_to_pay = CommonRepo::video_call_payment_check($video_call_request);

            if($payment_status == YES && $video_call_request->call_status == VIDEO_CALL_REQUEST_ACCEPTED) {

                $video_call_request->payment_btn_status = YES;

                $video_call_request->payment_btn_status = NO;


            }

            $video_call_request->join_btn_status = in_array($video_call_request->call_status, [ VIDEO_CALL_REQUEST_JOINED]) && $payment_status == NO ? YES : NO;

        }

        if($video_call_request->start_btn_status == YES) {

            // Check the schedule time

            $video_call_start_plus_minus = Setting::get('video_call_start_plus_minus', 10);

            $sub_start_time = Carbon::parse($video_call_request->start_time)->subMinutes($video_call_start_plus_minus);

            $add_start_time = Carbon::parse($video_call_request->start_time)->addMinutes($video_call_start_plus_minus);

            if(now() < $sub_start_time) {

                $video_call_request->start_btn_status = YES;

            } elseif(now() > $add_start_time) {

                $video_call_request->start_btn_status = NO;

                self::end_video_call($video_call_request->id);

                $video_call_request->refresh();
            }

        }

        $video_call_request->is_user_needs_to_pay = CommonRepo::video_call_payment_check($video_call_request);

        $video_call_request->payment_status = $video_call_payment || $payment_status ? YES : NO;

        $video_call_request->is_model = ($request->id == $video_call_request->model_id) ? YES:NO;

        $video_call_request->call_status_formatted = call_status_formatted($video_call_request->call_status, $video_call_request->is_model, $video_call_request->payment_status,$is_paid);

        $video_call_request->amount_formatted = formatted_amount($video_call_request->amount ?? 0);

        $video_call_request->start_time = common_date($video_call_request->start_time, $timezone);

        $video_call_request->end_time = common_date($video_call_request->end_time, $timezone);

        $api_call_request_start_time = (Setting::get('min_token_call_charge') / $video_call_request->amount);
         
        $hours = floor($api_call_request_start_time / 60);
        $minutes = ($api_call_request_start_time % 60);

        $video_call_request->api_call_request_start_time = "00:01:00";

        return $video_call_request;
    
    }

    /**
     * @method end_video_call()
     *
     * @uses to update video call status to ended
     *
     * @created Karthick
     * 
     * @updated 
     *
     * @param video_call_request_id
     *
     * @return
     */
    public function end_video_call($video_call_request_id) {

        $video_call_request = VideoCallRequest::find($video_call_request_id);

        $video_call_request->update(['call_status' => VIDEO_CALL_REQUEST_ENDED, 'end_time' => now(), 'message' => tr('timeout')]);
    }

}