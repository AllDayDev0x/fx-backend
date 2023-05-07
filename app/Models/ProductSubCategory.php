<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class ProductSubCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];


    protected $appends = ['product_sub_category_id'];

    public function getProductSubCategoryIdAttribute() {

        return $this->id;
    }

    public function productCategory() {

        return $this->belongsTo(ProductCategory::class,'product_category_id');

    }

    public function UserProducts() {

        return $this->hasMany(UserProduct::class,'product_sub_category_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PSC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PSC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){

            Helper::storage_delete_file($model->picture, CATEGORY_FILE_PATH);
            
        });

    }
}
