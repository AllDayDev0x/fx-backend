<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryAddress extends Model
{

	protected $hidden = ['id', 'unique_id'];

	protected $appends = ['delivery_address_id', 'delivery_address_unique_id'];

    public function getDeliveryAddressIdAttribute() {

        return $this->id;
    }

    public function getDeliveryAddressUniqueIdAttribute() {

        return $this->unique_id;
    }

	public function user() {

		return $this->belongsTo(User::class, 'user_id');
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "DA"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "DA"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

        });

    }
}
