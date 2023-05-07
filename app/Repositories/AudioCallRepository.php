<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User, App\Models\AudioCallRequest, App\Models\AudioCallPayment;

use App\Repositories\CommonRepository as CommonRepo;

use Carbon\Carbon;

class AudioCallRepository {

    /**
     * @method audio_call_requests_list_response()
     *
     * @uses used to format the live audios response
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $live_audios, object $request
     *
     * @return object $live_audios
     */

    public static function audio_call_requests_list_response($audio_call_requests, $request,$timezone) {

        foreach($audio_call_requests as $audio_call_request){                

            if(Setting::get('is_only_wallet_payment')) {

                $audio_call_request->amount = $audio_call_request->model->audio_call_token ?? 0;

                $audio_call_token = $audio_call_request->audioCallPayments->token ?? 0;

                $audio_call_request->amount = $audio_call_token ? : $audio_call_request->model->audio_call_token ?? 0;

            } else {

                $audio_call_request->amount = $audio_call_request->model->audio_call_amount ?? 0;
            }

            $audio_call_request->accept_btn_status = $audio_call_request->reject_btn_status =$audio_call_request->start_btn_status = $audio_call_request->end_btn_status = $audio_call_request->cancel_btn_status = $audio_call_request->join_btn_status = $audio_call_request->payment_btn_status = $audio_call_request->is_user_needs_to_pay = NO;

            $audio_call_payment = AudioCallPayment::where('audio_call_request_id',$audio_call_request->id)->where('status',PAID)->first();

            $payment_status = CommonRepo::audio_call_payment_check($audio_call_request);

            $payment_status = NO;
            
            $is_paid = $audio_call_request->amount > 0 && $audio_call_payment ? YES : NO;

            if($request->id == $audio_call_request->model_id) {

                $audio_call_request->accept_btn_status = $audio_call_request->call_status == AUDIO_CALL_REQUEST_SENT ? YES : NO;

                $audio_call_request->reject_btn_status = $audio_call_request->call_status == AUDIO_CALL_REQUEST_SENT ? YES : NO;

                // $audio_call_request->start_btn_status = $payment_status == NO && $audio_call_request->call_status == AUDIO_CALL_REQUEST_ACCEPTED ? YES : NO;

                $audio_call_request->start_btn_status = $audio_call_request->call_status == AUDIO_CALL_REQUEST_ACCEPTED ? YES : NO;

                $audio_call_request->end_btn_status = $audio_call_request->call_status == AUDIO_CALL_REQUEST_JOINED ? YES : NO;

                $audio_call_request->cancel_btn_status = in_array($audio_call_request->call_status, [AUDIO_CALL_REQUEST_ACCEPTED]) ? YES : NO;

            } else {

                $audio_call_request->is_user_needs_to_pay = CommonRepo::audio_call_payment_check($audio_call_request);

                if($payment_status == YES && $audio_call_request->call_status == AUDIO_CALL_REQUEST_ACCEPTED) {

                    // $audio_call_request->payment_btn_status = $audio_call_payment ?  NO : YES;

                    $audio_call_request->payment_btn_status = NO;

                }

                $audio_call_request->join_btn_status = in_array($audio_call_request->call_status, [ AUDIO_CALL_REQUEST_JOINED]) && $payment_status == NO ? YES : NO;

            }

            if($audio_call_request->start_btn_status == YES) {

                // Check the schedule time

                $audio_call_start_plus_minus = Setting::get('audio_call_start_plus_minus', 10);

                $sub_start_time = Carbon::parse($audio_call_request->start_time)->subMinutes($audio_call_start_plus_minus);

                $add_start_time = Carbon::parse($audio_call_request->start_time)->addMinutes($audio_call_start_plus_minus);

                if(now() < $sub_start_time) {

                    $audio_call_request->start_btn_status = NO;

                } elseif(now() > $add_start_time) {

                    $audio_call_request->start_btn_status = NO;

                    self::end_audio_call($audio_call_request->id);

                    $audio_call_request->refresh();
                }
            }

            $audio_call_request->is_user_needs_to_pay = CommonRepo::audio_call_payment_check($audio_call_request);

            $audio_call_request->payment_status = $audio_call_payment || $payment_status ? YES : NO;

            $audio_call_request->is_model = ($request->id == $audio_call_request->model_id) ? YES:NO;

            $audio_call_request->call_status_formatted = call_status_formatted($audio_call_request->call_status, $audio_call_request->is_model, $audio_call_request->payment_status,$is_paid);

            $audio_call_request->amount_formatted = formatted_amount($audio_call_request->amount ?? 0);

            $audio_call_request->start_time = common_date($audio_call_request->start_time, $timezone);

            $audio_call_request->end_time = common_date($audio_call_request->end_time, $timezone);


        }

        return $audio_call_requests;
    
    }

    /**
     * @method audio_call_requests_single_response()
     *
     * @uses used to format the live audios response
     *
     * @created Arun
     * 
     * @updated 
     *
     * @param object $live_audios, object $request
     *
     * @return object $live_audios
     */

    public static function audio_call_requests_single_response($audio_call_request, $request,$timezone) {

        $audio_call_request->accept_btn_status = $audio_call_request->reject_btn_status =$audio_call_request->start_btn_status = $audio_call_request->end_btn_status = $audio_call_request->cancel_btn_status = $audio_call_request->join_btn_status = $audio_call_request->payment_btn_status = $audio_call_request->is_user_needs_to_pay = $audio_call_request->is_owner = NO;

        $audio_call_payment = AudioCallPayment::where('audio_call_request_id', $audio_call_request->id)->where('status',PAID)->first();

        if($request->id == $audio_call_request->model_id) {

            $audio_call_request->accept_btn_status = $audio_call_request->call_status == AUDIO_CALL_REQUEST_SENT ? YES : NO;

            $audio_call_request->reject_btn_status = $audio_call_request->call_status == AUDIO_CALL_REQUEST_SENT ? YES : NO;

            $audio_call_request->start_btn_status = $audio_call_payment && $audio_call_request->call_status == AUDIO_CALL_REQUEST_ACCEPTED ? YES : NO;

            $audio_call_request->end_btn_status = $audio_call_request->call_status == AUDIO_CALL_REQUEST_JOINED ? YES : NO;

            $audio_call_request->cancel_btn_status = in_array($audio_call_request->call_status, [AUDIO_CALL_REQUEST_ACCEPTED]) ? YES : NO;

            $audio_call_request->is_owner = YES;
        } else {

            $audio_call_request->is_user_needs_to_pay = CommonRepo::audio_call_payment_check($audio_call_request);

            if($audio_call_request->call_status == AUDIO_CALL_REQUEST_ACCEPTED) {

                $audio_call_request->payment_btn_status = $audio_call_payment ?  NO : YES;

            }

            $audio_call_request->join_btn_status = in_array($audio_call_request->call_status, [AUDIO_CALL_REQUEST_ACCEPTED, AUDIO_CALL_REQUEST_JOINED]) && $audio_call_payment ? YES : NO;

        }

        if($audio_call_request->start_btn_status == YES) {

            // Check the schedule time

            $audio_call_start_plus_minus = Setting::get('audio_call_start_plus_minus', 10);

            $sub_start_time = Carbon::parse($audio_call_request->start_time)->subMinutes($audio_call_start_plus_minus);

            $add_start_time = Carbon::parse($audio_call_request->start_time)->addMinutes($audio_call_start_plus_minus);

            if(now() < $sub_start_time) {

                $audio_call_request->start_btn_status = NO;
            }

            if(now() > $add_start_time) {

                $audio_call_request->start_btn_status = NO;

                self::end_audio_call($audio_call_request->id);

                $audio_call_request->refresh();
            }

        }

        if(Setting::get('is_only_wallet_payment')) {
            $audio_call_request->amount = $audio_call_request->model->audio_call_token ?? 0;
        } else {
            $audio_call_request->amount = $audio_call_request->model->audio_call_amount ?? 0;
        }

        $audio_call_request->is_user_needs_to_pay = CommonRepo::audio_call_payment_check($audio_call_request);

        $audio_call_request->payment_status = $audio_call_payment ? YES:NO;

        $audio_call_request->is_model = ($request->id == $audio_call_request->model_id) ? YES:NO;

        $audio_call_request->call_status_formatted = call_status_formatted($audio_call_request->call_status,$audio_call_request->is_model, $audio_call_request->payment_status);

        $audio_call_request->amount_formatted = formatted_amount($audio_call_request->amount ?? 0);

        $audio_call_request->start_time = common_date($audio_call_request->start_time, $request->timezone);

        $audio_call_request->end_time = common_date($audio_call_request->end_time, $request->timezone);

        $api_call_request_start_time = (Setting::get('min_token_call_charge') / $audio_call_request->amount);
         
        $hours = floor($api_call_request_start_time / 60);
        $minutes = ($api_call_request_start_time % 60);

        $audio_call_request->api_call_request_start_time = sprintf('%02d:%02d:%02d', $hours, $minutes,'00');

        return $audio_call_request;
    
    }

    /**
     * @method end_audio_call()
     *
     * @uses to update audio call status to ended
     *
     * @created Karthick
     * 
     * @updated 
     *
     * @param audio_call_request_id
     *
     * @return
     */
    public function end_audio_call($audio_call_request_id) {

        $audio_call_request = AudioCallRequest::find($audio_call_request_id);

        $audio_call_request->update(['call_status' => AUDIO_CALL_REQUEST_ENDED, 'end_time' => now(), 'message' => tr('timeout')]);
    }

}