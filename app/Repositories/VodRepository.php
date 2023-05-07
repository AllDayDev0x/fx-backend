<?php

namespace App\Repositories;

use App\Helpers\Helper;

use Log, Validator, Setting, Exception, DB;

use App\Models\User, App\Models\VodVideo, App\Models\VodPayment;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

class VodRepository {

    /**
     * @method vods_list_response()
     *
     * @uses Format the vod response
     *
     * @created Subham
     * 
     * @updated 
     *
     * @param object $request
     *
     * @return object $details
     */

    public static function vods_list_response($vods, $request) {
        
        $vods = $vods->map(function ($vod, $key) use ($request) {

                        $vod->delete_btn_status =  $request->id == $vod->user_id ? YES : NO;

                        $vod->share_link = Setting::get('frontend_url')."vod/".$vod->vod_unique_id;

                        $vod->is_user_subscribed = $vod->payment_info->is_user_subscribed ?? NO;

                        $vod->share_link = Setting::get('frontend_url')."vod/".$vod->vod_unique_id;

                        $vod->payment_info = self::vods_user_payment_check($vod, $request);

                        $is_user_needs_pay = NO; 

                        $vod->vodFiles = VodVideo::where('id', $vod->vod_id)->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                                                    return $q->OriginalResponse();
                                                })
                                                ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {
                                                    return $q->BlurResponse();
                                                })->get();

                        $vod->publish_time_formatted = common_date($vod->publish_time, $request->timezone, 'M d');


                        return $vod;
                    });


        return $vods;

    }

    /**
     * @method vods_single_response()
     *
     * @uses Format the vod response
     *
     * @created Subham
     * 
     * @updated
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function vods_single_response($vod, $request) {

        $is_user_needs_pay = NO; 

        $vod->vodFiles = VodVideo::where('id', $vod->id)->when($is_user_needs_pay == NO, function ($q) use ($is_user_needs_pay) {
                                    return $q->OriginalResponse();
                                })
                                ->when($is_user_needs_pay == YES, function($q) use ($is_user_needs_pay) {
                                    return $q->BlurResponse();
                                })->get();

        $vod->publish_time_formatted = common_date($vod->publish_time, $request->timezone, 'M d');

        return $vod;
    
    }

    /**
     * @method vods_user_payment_check()
     *
     * @uses Check the vod payment status for each vods
     *
     * @created Subham
     * 
     * @updated
     *
     * @param object $request
     *
     * @return object $payment_details
     */

    public static function vods_user_payment_check($vod, $request) {

        $vod_user = $vod->user ?? [];
        
        $data['is_user_needs_pay'] = $data['is_free_account'] =  NO;

        $data['vod_payment_type'] = $data['payment_text'] = $data['is_user_subscribed'] = "";

        if(!$vod_user) {

            goto vod_end;

        }

        if($vod_user->user_id == $request->id) {

            goto vod_end;
        }

        // Check the user has subscribed for this vod user plans

        $current_date = Carbon::now()->format('Y-m-d');

        $check_vod_payment = VodPayment::where('from_user_id', $request->id)
                ->whereDate('expiry_date','>=', $current_date)
                ->where('to_user_id', $vod_user->id)
                ->get()->first();  
            
        if($check_vod_payment) {

            $data['amount'] = $check_vod_payment->amount ?? 0;

            $data['admin_amount'] = $check_vod_payment->admin_amount ?? 0;

            $data['user_amount'] = $check_vod_payment->user_amount ?? 0;

            $data['payment_mode'] = $check_vod_payment->payment_mode ?? '';

            $data['expiry_date'] = $check_vod_payment->expiry_date ?? '';

            $data['paid_date'] = $check_vod_payment->paid_date ?? '';

            $data['is_user_needs_pay'] = YES;

            $data['is_free_account'] = $check_vod_payment->amount ? tr('paid') : tr('not_paid');

        }else{
            
            $data['is_user_needs_pay'] = NO;

        }

        $data['post_payment_type'] = PLAN_TYPE_VOD;

        vod_end:

        return (object)$data;
    
    }

}