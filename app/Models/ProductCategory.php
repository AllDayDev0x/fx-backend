<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class ProductCategory extends Model
{
    use HasFactory;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

    protected $appends = ['product_category_id'];

    public function getProductCategoryIdAttribute() {

        return $this->id;
    }

    public function productSubCategories() {

        return $this->hasMany(ProductSubCategory::class,'product_category_id');
    }

    public function UserProducts() {

        return $this->hasMany(UserProduct::class,'product_category_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){

            Helper::storage_delete_file($model->picture, CATEGORY_FILE_PATH);
            
            foreach ($model->productSubCategories as $key => $subCategory) {

                $subCategory->delete();

            }
            
        });

    }
}
