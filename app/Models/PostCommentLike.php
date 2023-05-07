<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCommentLike extends Model
{
    protected $fillable = ['user_id', 'post_comment_id', 'post_comment_reply_id', 'post_user_id', 'status'];

    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['post_comment_like_id', 'post_comment_like_unique_id'];
    
    public function getPostCommentLikeIdAttribute() {

        return $this->id;
    }

    public function getPostCommentLikeUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function User() {

        return $this->belongsTo(User::class, 'user_id');
     }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('post_comment_likes.status', APPROVED);

        return $query;

    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PCL"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PCL"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
