<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderPayment extends Model
{
    protected $appends = ['delivery_price_formatted','total_formatted','sub_total_formatted','tax_price_formatted','sub_total_formatted'];


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

    public function user() {

        return $this->belongsTo(User::class,'user_id');
    }

    public function userOrder() {

        return $this->belongsTo(Order::class,'order_id');
    }
}
