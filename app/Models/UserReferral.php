<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserReferral extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['user_referral_id','user_referral_unique_id','username'];

	public function getUserReferralIdAttribute() {

		return $this->id;
	}

	public function getUserReferralUniqueIdAttribute() {

		return $this->unique_id;
	}
	public function getUsernameAttribute() {

    	return $this->user->username ?? "";
    }

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function userReferral() {

	   return $this->belongsTo(UserReferral::class, 'referral_code_id');
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "UR"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UR"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){
            
        });

    }
}
