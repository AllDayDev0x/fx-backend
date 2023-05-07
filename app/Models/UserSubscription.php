<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['user_subscription_id', 'user_subscription_unique_id', 'monthly_amount_formatted', 'yearly_amount_formatted', 'user_unique_id', 'username','user_displayname', 'user_picture'];
	
	public function getUserSubscriptionIdAttribute() {

		return $this->id;
	}

	public function getUserSubscriptionUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getMonthlyAmountFormattedAttribute() {

		return formatted_amount($this->monthly_amount);
	}

	public function getYearlyAmountFormattedAttribute() {

		return formatted_amount($this->yearly_amount);
	}

	public function getUserUniqueIdAttribute() {

		$user_unique_id = $this->user->unique_id ?? "";

		unset($this->user);

		return $user_unique_id ?? "";
	}

	public function getUsernameAttribute() {

		$username = $this->user->username ?? "";

		unset($this->user);

		return $username ?? "";
	}

	public function getUserDisplaynameAttribute() {

		$name = $this->user->name ?? "";

		unset($this->user);

		return $name ?? "";
	}

	public function getUserPictureAttribute() {

		$picture = $this->user->picture ?? "";

		unset($this->user);

		return $picture ?? "";
	}

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function userSubscriptioPayments() {

	   return $this->belongsTo(UserSubscriptioPayment::class, 'user_subscription_id');
	}

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('user_subscriptions.status', APPROVED);

        return $query;

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return $query->select(
	            'user_subscriptions.*',
	            'user_subscriptions.monthly_amount as monthly_amount',
	            'user_subscriptions.yearly_amount as yearly_amount'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTokenResponse($query) {

        return $query->select(
            'user_subscriptions.*',
	        'user_subscriptions.monthly_token as monthly_amount',
	        'user_subscriptions.yearly_token as yearly_amount'
        );
    
    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "US-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "US-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function($model) {

            $model->userSubscriptioPayments()->delete();
        
        });
    }
}
