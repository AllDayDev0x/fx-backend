<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderProduct extends Model
{
    protected $appends = ['delivery_price_formatted','sub_total_formatted','tax_price_formatted','sub_total_formatted','per_quantity_price_formatted','product_name'];


    public function getTotalFormattedAttribute() {

    	return formatted_amount($this->total);
    }

    public function getTaxPriceFormattedAttribute() {

    	return formatted_amount($this->tax_price);
    }

    public function getSubTotalFormattedAttribute() {

    	return formatted_amount($this->sub_total);
    }

    public function getDeliveryPriceFormattedAttribute() {

    	return formatted_amount($this->delivery_price);
    }

    public function getPerQuantityPriceFormattedAttribute() {

        return formatted_amount($this->per_quantity_price);
    }

    public function getProductNameAttribute() {

        return $this->name;
    }

    public function userProductDetails() {

    	return $this->belongsTo(UserProduct::class,'user_product_id');
    }

    public function userOrder() {

        return $this->belongsTo(Order::class,'order_id');
    }

    public function user(){

        return $this->belongsTo(User::class,'user_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "OPR"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "OPR"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

        });

    }
}
