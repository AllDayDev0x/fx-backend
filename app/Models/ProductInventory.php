<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductInventory extends Model
{
    public function userProductDetails() {

    	return $this->belongsTo(UserProduct::class,'user_product_id');
    }
}
