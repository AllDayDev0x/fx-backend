<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscriptionPayment extends Model
{
	protected $hidden = ['id','unique_id'];

	protected $appends = ['user_subscription_payment_id','user_subscription_payment_unique_id', 'from_username', 'from_user_picture', 'from_user_unique_id', 'to_username', 'to_user_picture', 'to_user_unique_id', 'amount_formatted','plan_text_formatted','admin_amount_formatted','user_amount_formatted'];

    protected $fillable = ['from_user_id'];
	
	public function getUserSubscriptionPaymentIdAttribute() {

		return $this->id;
	}

	public function getUserSubscriptionPaymentUniqueIdAttribute() {

		return $this->unique_id;
	}

    public function getFromUsernameAttribute() {

    	return $this->fromUser->name ?? "";
    }

    public function getFromUserPictureAttribute() {

    	return $this->fromUser->picture ?? "";
    }

    public function getFromUserUniqueIdAttribute() {

    	return $this->fromUser->unique_id ?? "";
    }

    public function getToUsernameAttribute() {

    	return $this->toUser->name ?? "";
    }

    public function getToUserPictureAttribute() {

    	return $this->toUser->picture ?? "";
    }

    public function getToUserUniqueIdAttribute() {

    	return $this->toUser->unique_id ?? "";
    }

    public function getAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->amount);
    }

    public function fromUser() {

    	return $this->belongsTo(User::class,'from_user_id');
    }

    public function toUser() {

    	return $this->belongsTo(User::class, 'to_user_id');
    }

    public function getPlanTextFormattedAttribute() {

        return plan_text($this->plan,$this->plan_type);
    }

    public function getAdminAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->admin_token : $this->admin_amount);
    }


    public function getUserAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->user_token : $this->user_amount);
    }


    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserPaid($query, $from_user_id, $to_user_id) {

        $query->where('user_subscription_payments.from_user_id', $from_user_id)->where('user_subscription_payments.to_user_id', $to_user_id)->where('user_subscription_payments.status', PAID)->where('is_current_subscription', YES);

        return $query;

    }

}
