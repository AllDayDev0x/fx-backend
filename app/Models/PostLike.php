<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    protected $fillable = ['user_id', 'post_id', 'post_user_id'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['post_like_id', 'post_like_unique_id', 'username', 'user_picture', 'user_unique_id', 'liked_username', 'liked_user_picture', 'liked_user_unique_id'];
	
	public function getPostLikeIdAttribute() {

		return $this->id;
	}

	public function getPostLikeUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getUsernameAttribute() {

		$name = $this->postUser->name ?? "";

		unset($this->postUser);

		return $name;
	}

	public function getUserPictureAttribute() {

		$picture = $this->postUser->picture ?? "";

		unset($this->postUser);

		return $picture ?? "";
	}

	public function postUser() {

	   return $this->belongsTo(User::class, 'post_user_id');
	}

	public function User() {

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

        $query->where('post_likes.status', APPROVED);

        return $query;

    }

    public function getUserUniqueIdAttribute() {

		$unique_id = $this->postUser->unique_id ?? "";

		unset($this->postUser);

		return $unique_id;
	}

	public function getLikedUsernameAttribute() {

		$name = $this->User->name ?? "";

		unset($this->User);

		return $name;
	}

	public function getLikedUserPictureAttribute() {

		$picture = $this->User->picture ?? "";

		unset($this->User);

		return $picture ?? "";
	}

	public function getLikedUserUniqueIdAttribute() {

		$unique_id = $this->User->unique_id ?? "";

		unset($this->User);

		return $unique_id;
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
