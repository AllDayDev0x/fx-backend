<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class StoryFile extends Model
{
    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['story_file_id', 'story_file_unique_id','updated'];
    
    public function getStoryFileIdAttribute() {

        return $this->id;
    }

    public function getStoryFileUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getUpdatedAttribute() {

        return $this->updated_at->diffForHumans();
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return 
            $query->select(
            'story_files.*',
            'story_files.file as story_file'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlurResponse($query) {

        return $query->select(
            'story_files.*',
            'story_files.blur_file as story_file'
            );
    
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "SF"."-".uniqid();

        });
        
        static::created(function ($model) {

            $model->attributes['unique_id'] = "SF"."-".$model->attributes['id']."-".uniqid();

        });

        static::deleting(function($model) {

            Helper::storage_delete_file($model->file, POST_TEMP_PATH.$model->user_id.'/');

            Helper::storage_delete_file($model->file, POST_PATH.$model->user_id.'/');

            Helper::storage_delete_file($model->blur_file, POST_BLUR_PATH.$model->user_id.'/');

            Helper::storage_delete_file($model->preview_file, POST_PATH.$model->user_id.'/');

        });

    }
}
