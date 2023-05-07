<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

use Setting;

class UserProduct extends Model
{
    protected $appends = ['user_product_id','user_product_price_formatted'];

    public function getUserProductIdAttribute() {

        return $this->id;
    }

    public function getUserProductPriceFormattedAttribute() {

        return formatted_amount(Setting::get('is_only_wallet_payment') ? $this->token : $this->price);
    }

    public function user(){

    	return $this->belongsTo(User::class,'user_id');
    }

    public function userProductPictures() {

        return $this->hasMany(UserProductPicture::class,'user_product_id');
    }

    public function orderProducts() {

        return $this->hasMany(OrderProduct::class,'user_product_id');
    }

    public function productCategories() {

        return $this->belongsTo(ProductCategory::class,'product_category_id');
    }

    public function productSubCategories() {

        return $this->belongsTo(ProductSubCategory::class,'product_sub_category_id');
    }

    public function carts() {

        return $this->hasMany(Cart::class,'user_product_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return $query->select(
                'user_products.*',
                'user_products.price as price'
        );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTokenResponse($query) {

        return $query->select(
            'user_products.*',
            'user_products.token as price'
        );
    
    }

    /**
     * Scope a query to only include active product.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('user_products.status', YES);

        return $query;

    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "UP"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UP"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

            Helper::storage_delete_file($model->picture, COMMON_FILE_PATH);

            foreach ($model->userProductPictures as $key => $userProductPicture) {

                $userProductPicture->delete();

            }

            foreach ($model->carts as $key => $cart) {

                $cart->delete();

            }

            $model->orderProducts()->delete();

        });

    }
}
