<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VodPayment extends Model
{
    protected $hidden = ['id','unique_id'];

    protected $appends = ['vod_subscription_payment_id','vod_subscription_payment_unique_id', 'from_username', 'from_user_picture', 'from_user_unique_id', 'to_username', 'to_user_picture', 'to_user_unique_id', 'amount_formatted','admin_amount_formatted','user_amount_formatted'];
    
    public function getVodSubscriptionPaymentIdAttribute() {

        return $this->id;
    }

    public function getVodSubscriptionPaymentUniqueIdAttribute() {

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

        return formatted_amount($this->amount);
    }

    public function fromUser() {

        return $this->belongsTo(User::class,'from_user_id');
    }

    public function toUser() {

        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function getAdminAmountFormattedAttribute() {

        return formatted_amount($this->admin_amount);
    }


    public function getUserAmountFormattedAttribute() {

        return formatted_amount($this->user_amount);
    }


    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeVodPaid($query, $from_user_id, $to_user_id) {

        $query->where('vod_payments.from_user_id', $from_user_id)->where('vod_payments.to_user_id', $to_user_id)->where('vod_payments.status', PAID);

        return $query;

    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            
            $model->attributes['unique_id'] = "VP"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VP"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){

            $model->vodFiles()->delete();
            
        });

    }
}