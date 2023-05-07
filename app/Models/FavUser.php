<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FavUser extends Model
{
    protected $fillable = ['user_id', 'fav_user_id'];

    public function favUser() {

        return $this->belongsTo(User::class,'fav_user_id');
    }

    public function user() {

        return $this->belongsTo(User::class,'user_id');
    }

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('fav_users.status', APPROVED);

        return $query;

    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "FP-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "FP-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
