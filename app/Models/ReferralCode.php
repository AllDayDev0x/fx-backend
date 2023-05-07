<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralCode extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['referral_code_id','referral_code_unique_id','referral_earnings_formatted','referee_earnings_formatted','total_formatted','total_earnings','remaining_formatted','used_formatted','username','total_referrals_formatted'];

	public function getReferralCodeIdAttribute() {

		return $this->id;
	}

	public function getReferralCodeUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getReferralEarningsFormattedAttribute() {

		return formatted_amount($this->referral_earnings);
	}

	public function getRefereeEarningsFormattedAttribute() {

		return formatted_amount($this->referee_earnings);
	}

	public function getTotalFormattedAttribute() {

		return formatted_amount($this->referee_earnings+$this->referral_earnings);
	}

	public function getTotalEarningsAttribute() {

		return $this->referee_earnings+$this->referral_earnings;
	}

	public function getRemainingFormattedAttribute() {

		$referral_amount = $this->user->userWallets ? $this->user->userWallets->referral_amount_formatted : formatted_amount(0);

		return $referral_amount;
	}

	public function getUsedFormattedAttribute() {

		$used_amount = $this->user->userWallets ? ($this->referee_earnings+$this->referral_earnings) - $this->user->userWallets->referral_amount : 0;

		return formatted_amount($used_amount);
	}

	public function getUsernameAttribute() {

    	return $this->user->name ?? "";
    }

    public function getTotalReferralsFormattedAttribute() {

    	return $this->userReferrals->count() ?? 0;
    }


	public function user() {


	   return $this->belongsTo(User::class, 'user_id');
	}

	/**
     * Get the UserCard record associated with the user.
     */
    public function userReferrals() {
        
        return $this->hasMany(UserReferral::class, 'referral_code_id');
    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "RF"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "RF"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){

            foreach ($model->userReferrals as $key => $referrals) {

                $referrals->delete();
                
            }

        });

    }
}
