<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostHashtag extends Model
{
    protected $hidden = ['id','unique_id'];

    protected $appends = ['post_hashtag_id', 'post_hashtag_unique_id'];

    public function getPostHashtagIdAttribute() {

        return $this->id;
    }

    public function getPostHashtagUniqueIdAttribute() {

        return $this->unique_id;
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PH"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PH"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
