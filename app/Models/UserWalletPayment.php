<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWalletPayment extends Model
{
    protected $hidden = ['deleted_at', 'id', 'unique_id'];

	protected $appends = ['user_wallet_payment_id','user_wallet_payment_unique_id', 'paid_amount_formatted', 'status_formatted', 'wallet_picture', 'received_from_username','requested_amount_formatted','admin_amount_formatted','user_amount_formatted','token_formatted','admin_token_formatted','user_token_formatted'];

    public function getUserWalletPaymentIdAttribute() {

        return $this->id;
    }

    public function getUserWalletPaymentUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getAdminTokenFormattedAttribute() {

        return formatted_amount($this->admin_token);
    }

    public function getUserTokenFormattedAttribute() {

        return formatted_amount($this->admin_token);
    }

    public function getPaidAmountFormattedAttribute() {

        return wallet_formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->paid_amount, $this->amount_type);
    }

    public function getRequestedAmountFormattedAttribute() {

        return formatted_amount($this->requested_amount,'','',NO);
    }

    public function getAdminAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->admin_token : $this->admin_amount);
    }

    public function getTokenFormattedAttribute() {

        return formatted_amount($this->token);
    }
    
    public function getUserAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->user_token : $this->user_amount);
    }

    public function getStatusFormattedAttribute() {

        return paid_status_formatted($this->status,$this->payment_type);
    }

    public function getWalletPictureAttribute() {

        return wallet_picture($this->amount_type);
    }

    // public function getUsernameAttribute() {

    //     $username = $this->toUser ? $this->toUser->name : "You";

    //     unset($this->toUser);

    //     return $username;
    // }

    public function getReceivedFromUsernameAttribute() {

        $username = $this->ReceivedFromUser ? $this->ReceivedFromUser->name : "";

        unset($this->ReceivedFromUser);

        return $username;
    }
    
    public function user() {
    	return $this->belongsTo('App\Models\User','user_id');
    }

    public function toUser() {
        return $this->belongsTo('App\Models\User','to_user_id');
    }

    public function ReceivedFromUser() {

        return $this->belongsTo('App\Models\User', 'received_from_user_id');
    }

    public function billingaccountDetails() {
        return $this->belongsTo('App\Models\UserBillingAccount','user_billing_account_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {
        return $query;
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "UW"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UW"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
