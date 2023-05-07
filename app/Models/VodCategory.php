<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VodCategory extends Model
{

    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['vod_category_id','vod_category_unique_id', 'vodtitle'];

    public function getVodCategoryIdAttribute() {

        return $this->id;
    }

    public function getVodCategoryUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getVodTitleAttribute() {

        return $this->vod->title ?? "";
    }

    public function vod(){

        return $this->belongsTo(VodVideo::class,'vod_video_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "VC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
