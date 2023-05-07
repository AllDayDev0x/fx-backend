<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follower extends Model
{
    /**
     * Load follower using relation model
     */
    public function getUser()
    {
        return $this->hasOne('App\Models\User', 'id', 'follower_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->leftJoin('users' , 'users.id' ,'=' , 'followers.follower_id')
			->select(
				'users.id as u_id',
	            'users.unique_id as u_unique_id',
                'users.username',
                'users.name',
	            'users.email as email',
	            'users.picture as picture',
                'users.cover as cover',
                'followers.user_id',
                'followers.follower_id',
                'followers.created_at',
                'followers.updated_at'
            );
    
    }

    public function follower() {

        return $this->belongsTo(User::class,'follower_id');
    }

    public function user() {

        return $this->belongsTo(User::class,'user_id');
    }

    public function followerDetails() {

        return $this->belongsTo(User::class,'follower_id');
    }

    public function userDetails() {

        return $this->belongsTo(User::class,'user_id');
    }

}
