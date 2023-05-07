<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostBookmark extends Model
{
    protected $fillable = ['post_id', 'user_id'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['post_bookmark_id', 'post_bookmark_unique_id', 'username', 'user_picture'];
	
	public function getPostBookmarkIdAttribute() {

		return $this->id;
	}

	public function getPostBookmarkUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getUsernameAttribute() {

		$username = $this->user->name ?? "";

		unset($this->user);

		return $username;
	}

	public function getUserPictureAttribute() {

		$user_picture = $this->user->picture ?? "";

		unset($this->user);

		return $user_picture;
	}

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function post() {

	   return $this->belongsTo(Post::class, 'post_id');
	}

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('post_bookmarks.status', APPROVED);

        return $query;

    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PBM-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PBM-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
    }
}
