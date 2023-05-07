<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Story extends Model
{
    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['story_id', 'story_unique_id', 'user_displayname', 'username','updated','status_formatted'];
    
    public function getStoryIdAttribute() {

        return $this->id;
    }

    public function getUpdatedAttribute() {

        return $this->updated_at->diffForHumans();
    }

    public function getStatusFormattedAttribute() {

        return stories_status_text($this->status);
    }

    public function getStoryUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function storyFiles() {

       return $this->hasMany(StoryFile::class, 'story_id');
    }

     /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('stories.is_published', YES)->where('stories.status', YES);

        return $query;

    }

    public function scopeCurrentStories($query) {

        return $query->where('is_published', PUBLISHED)->where('publish_time', '>=', \Carbon\Carbon::now()->subDay())->whereHas('storyFiles');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = "ST"."-".uniqid();

        });
        
        static::created(function ($model) {

            $model->attributes['unique_id'] = "ST"."-".$model->attributes['id']."-".uniqid();

        });

        static::deleting(function ($model){

            foreach ($model->storyFiles as $key => $storyFile) {

                $storyFile->delete();

            }
            
            
        });

    }

    public function getUserDisplaynameAttribute() {

        $name = $this->user->name ?? "";

        // unset($this->user);

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
}
