<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Log, Validator, Exception, DB, Setting;

use App\Helpers\Helper;

use App\Models\StaticPage;

use App\Models\SubscriptionPayment;

use App\Models\User;

use App\Models\Subscription;

class UpgradeController extends Controller
{

    protected $loginUser;

    public function __construct(Request $request) {
        
        $this->loginUser = User::find($request->id);

        $this->timezone = $this->loginUser->timezone ?? "America/New_York";

    }

    /**
     * @method free_subscriptions_clear()
     *
     * @uses to get the pages
     *
     * @created Vidhya R 
     *
     * @edited Vidhya R
     *
     * @param - 
     *
     * @return JSON Response
     */

    public function free_subscriptions_clear(Request $request) {

        $user_subscriptions = \App\Models\UserSubscription::where('monthly_amount', '>', 0)->orWhere('yearly_amount', '>', 0)->get();

        foreach ($user_subscriptions as $key => $user_subscription) {
        
            $change_expiry_user_ids = \App\Models\UserSubscriptionPayment::where('user_subscription_id', 0)->where('to_user_id', $user_subscription->user_id)->pluck('from_user_id')->implode(',') ?? "";

            \App\Models\Follower::whereIn('follower_id', [$change_expiry_user_ids])->where('user_id', $user_subscription->user_id)->delete();
            
            \App\Models\UserSubscriptionPayment::where('user_subscription_id', 0)->where('to_user_id', $user_subscription->user_id)->update(['is_current_subscription' => NO, 'expiry_date' => date('Y-m-d H:i:s'),'cancel_reason' => 'Model added subscription']);
        }

            return $this->sendResponse("", "", $data = ['user_subscriptions' => $user_subscriptions->count() ?? 0]);

    }
}