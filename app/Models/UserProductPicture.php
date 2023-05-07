<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class UserProductPicture extends Model
{
    protected $appends = ['user_product_picture_id'];

    protected $hidden = ['id'];

	public function getUserProductPictureIdAttribute() {

		return $this->id;
	}

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return 
            $query->select(
            'user_product_pictures.*',
            'user_product_pictures.picture as product_picture'
            );
    
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "UPP"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UPP"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

            Helper::storage_delete_file($model->picture, COMMON_FILE_PATH);

        });

    }
}
