<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hashtag extends Model
{
    protected $fillable = ['name', 'description', 'count', 'status'];

    protected $hidden = ['id','unique_id'];

    protected $appends = ['hashtag_id','hashtag_unique_id'];
    
    public function getHashtagIdAttribute() {

        return $this->id;
    }

    public function getHashtagUniqueIdAttribute() {

        return $this->unique_id;
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('hashtags.status', APPROVED);

        return $query;

    }


    public function postHashtag() {

        return $this->hasMany(PostHashtag::class,'hashtag_id');
    }


    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "HT"."-".uniqid();
            $model->attributes['count'] = 1;
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "HT"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
