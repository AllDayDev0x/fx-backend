<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;

use App\Helpers\Helper;

use DB, Log, Hash, Validator, Exception, Setting;

use App\Models\User;

use Carbon\Carbon;

use App\Repositories\CommonRepository as CommonRepo;

use App\Models\ReferralCode;

class ReferralApiController extends Controller
{
    protected $loginUser;

    protected $skip, $take;

    public function __construct(Request $request) {

        Log::info(url()->current());

        Log::info("Request Data".print_r($request->all(), true));
        
        $this->loginUser = User::find($request->id);

        $this->skip = $request->skip ?: 0;

        $this->take = $request->take ?: (Setting::get('admin_take_count') ?: TAKE_COUNT);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }


    /**
     * @method referral_code()
     * 
     * @uses Save and Display User Referral Codes
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param object $request - user id
     * 
     * @return response of Referral Codes
     */
    public function referral_code(Request $request) {

        try {

            DB::beginTransaction();

            $user = User::find($request->id);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            $referral_code = ReferralCode::firstWhere('referral_codes.user_id',$user->id);

            if(!$referral_code) {

                $referral_code = CommonRepo::user_referral_code($user->id);

            }

            // share message start
            $share_message = tr('referral_code_share_message', Setting::get('site_name', 'FANSCLUB'));

            $share_message = str_replace('<%referral_code%>', $referral_code->referral_code, $share_message);

            $share_message = str_replace("<%referral_earnings%>", formatted_amount(Setting::get('referral_earnings', 10)),$share_message);

            $referrals_signup_url = Setting::get('frontend_url')."?referral=".$referral_code->referral_code;

            $referral_code->share_message = $share_message." ".$referrals_signup_url;

            $referral_code->referrals_signup_url = $referrals_signup_url;

            DB::commit();

            return $this->sendResponse($message = "", $code = "", $referral_code);

        } catch(Exception $e) {

            DB::rollback();

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    }

    /**
     * @method validate_referral_code()
     * 
     * @uses To Validate Referral Code
     *
     * @created Bhawya
     *
     * @updated Bhawya
     *
     * @param object $referral_code
     * 
     * @return response success/error
     */
    public function referral_code_validate(Request $request) {

        try {

            $rules = [
                'referral_code' => 'required',
            ];

            Helper::custom_validator($request->all(), $rules);

            $referral_code =  ReferralCode::where('referral_code', $request->referral_code)->firstWhere('status', APPROVED);

            if(!$referral_code) {

                throw new Exception(api_error(183) , 183);

            }

            $user = User::where('id', $referral_code->user_id)->firstWhere('status', USER_APPROVED);

            if(!$user) {

                throw new Exception(api_error(1002), 1002);
            }

            return $this->sendResponse(api_success(219),219,$request->referral_code);

        } catch(Exception $e) {

            return $this->sendError($e->getMessage(), $e->getCode());

        }
    }

}