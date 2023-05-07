<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWallet extends Model
{
	protected $hidden = ['deleted_at', 'id', 'unique_id'];

    protected $fillable = ['user_id', 'total', 'used', 'remaining'];

	protected $appends = ['user_wallet_id','user_wallet_unique_id', 'total_formatted', 'used_formatted', 'remaining_formatted', 'onhold_formatted','referral_amount_formatted'];

	public function getUserWalletIdAttribute() {

        return $this->id;
    }

    public function getUserWalletUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getTotalFormattedAttribute() {

        return formatted_amount($this->total);
    }

    public function getUsedFormattedAttribute() {

        return formatted_amount($this->used);
    }

    public function getOnholdFormattedAttribute() {

        return formatted_amount($this->onhold);
    }

    public function getRemainingFormattedAttribute() {

        return formatted_amount($this->remaining);
    }

    public function getReferralAmountFormattedAttribute() {

        return formatted_amount($this->referral_amount);
    }

    public function user() {
        return $this->belongsTo('App\Models\User','user_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {
        return $query->join('users','users.id','=','user_wallets.user_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "UW-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UW-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
    }
}
