<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class SubCategory extends Model
{    
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];


    protected $appends = ['sub_category_id'];

    public function getSubCategoryIdAttribute() {

        return $this->id;
    }

    public function category() {

        return $this->belongsTo(Category::class,'category_id');

    }

    public function UserProducts() {

        return $this->hasMany(UserProduct::class,'sub_category_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "C"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "C"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){

            Helper::storage_delete_file($model->picture, CATEGORY_FILE_PATH);
            
        });

    }
}
