<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWithdrawal extends Model
{
    protected $hidden = ['deleted_at', 'id', 'unique_id'];

    protected $fillable = ['user_id', 'requested_amount'];

	protected $appends = ['user_withdrawal_id','user_withdrawal_unique_id', 'requested_amount_formatted', 'paid_amount_formatted', 'status_formatted', 'withdraw_picture', 'billing_account_name', 'paid_date'];

	public function getUserWithdrawalIdAttribute() {

        return $this->id;
    }

    public function getUserWithdrawalUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getBillingAccountNameAttribute() {

        return $this->billingAccount->first_name ?? "-";
    }

    public function getRequestedAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->requested_token : $this->requested_amount);
    }

    public function getPaidAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->paid_amount/\Setting::get('token_amount') : $this->paid_amount);
    }

    public function getStatusFormattedAttribute() {

        return withdrawal_status_formatted($this->status);
    }

    public function getWithdrawPictureAttribute() {

        return withdraw_picture($this->status);
    }

    public function getCancelBtnStatusAttribute() {

        if(in_array($this->status, [WITHDRAW_INITIATED, WITHDRAW_ONHOLD])) {
            return YES;
        }

        return NO;
    }

    public function getPaidDateAttribute() {

        return $this->userWalletPayment->paid_date ?? "";
    }

    public function userWalletPayment() {
        return $this->belongsTo('App\Models\UserWalletPayment','user_wallet_payment_id');
    }


    public function user() {
    	return $this->belongsTo('App\Models\User','user_id');
    }

    public function scopeCommonResponse($query) {
        return $query
        ->join('users','users.id','=','user_withdrawals.user_id')
        ->select('user_withdrawals.*');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "WDR-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "WDR-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
    }

    public function billingAccount() {
    	return $this->belongsTo(UserBillingAccount::class,'user_billing_account_id');
    }
}
