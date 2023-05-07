<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Viewer extends Model
{

    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['viewer_id', 'viewer_unique_id'];

    public function getViewerIdAttribute() {

        return $this->id;
    }

    public function getViewerUniqueIdAttribute() {

        return $this->unique_id;
    }
    
    public function scopeBaseResponse($query) {

        return $query->leftJoin('users', 'users.id', '=', 'viewers.user_id')
            ->select(
                'users.name as user_name',
                'users.username',
                'users.picture as user_picture',
                'viewers.*'
            );
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = uniqid();

        });
        
        static::created(function ($model) {

            $model->attributes['unique_id'] = "V-".$model->attributes['id']."-".uniqid();
        });

    }
}
