<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{

    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['cart_id', 'cart_unique_id', 'per_quantity_price_formatted', 'sub_total_formatted'];

    public function getCartIdAttribute() {

        return $this->id;
    }

    public function getCartUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getPerQuantityPriceFormattedAttribute() {

        return formatted_amount($this->per_quantity_price);
    }

    public function getSubTotalFormattedAttribute() {

        return formatted_amount($this->sub_total);
    }

    public function order() {

        return $this->belongsTo(Order::class,'order_id');

    }

     public function user_product() {

        return $this->belongsTo(UserProduct::class,'user_product_id');

    }

    /**
     * Scope a query to basic subscription details
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBaseResponse($query) {

        return $query->leftJoin('user_products', 'user_products.id', '=', 'carts.user_product_id')
            ->select(
                \DB::raw('carts.*, SUM(carts.total) as actual_total'),
                'user_products.user_id as model_id',
                'carts.*'
            )
            ->groupBy('user_products.user_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['unique_id'] = uniqid();

        });
        
        static::created(function ($model) {

            $model->attributes['unique_id'] = "CT"."-".$model->attributes['id']."-".uniqid();
        });

    }
}
