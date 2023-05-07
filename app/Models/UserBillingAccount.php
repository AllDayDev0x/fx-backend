<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBillingAccount extends Model
{
    protected $fillable = ['user_id', 'account_number', 'business_name', 'first_name', 'last_name', 'bank_type', 'route_number', 'nickname', 'bank_name'];

	protected $hidden = ['deleted_at', 'id', 'unique_id'];

	protected $appends = ['user_billing_account_id','user_billing_account_unique_id'];

    public function getUserBillingAccountIdAttribute() {

        return $this->id;
    }

    public function getUserBillingAccountUniqueIdAttribute() {

        return $this->unique_id;
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

        return $query;
    
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "BID"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "BID"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }

}
