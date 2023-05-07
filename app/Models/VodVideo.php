<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VodVideo extends Model
{

    protected $fillable = ['file'];

    protected $hidden = ['id', 'unique_id', 'file'];

    protected $appends = ['user_displayname', 'username'];

    public function getUserDisplaynameAttribute() {

        $name = $this->user->name ?? "";

        return $name ?? "";
    }

    public function getUsernameAttribute() {

        $username = $this->user->username ?? "";

        // unset($this->user);

        return $username ?? "";
    }

    public function user() {

       return $this->belongsTo(User::class, 'user_id');
    }

    public function vodFiles() {

       return $this->hasMany(VodVideo::class, 'id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            
            $model->attributes['unique_id'] = "VV"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VV"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){

            $model->vodFiles()->delete();
            
        });

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return 
            $query->select(
            'vod_videos.*',
            'vod_videos.file as vod_file'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlurResponse($query) {

        return $query->select(
            'vod_videos.*',
            'vod_videos.blur_file as vod_file'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('vod_videos.is_published', YES)->where('vod_videos.status', YES);

        return $query;

    }
}
