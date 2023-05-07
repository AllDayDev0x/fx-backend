<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPromoCode extends Model
{

    protected $appends = ['user_promo_code_id'];

    protected $hidden = ['id', 'unique_id'];

    public function getUserPromoCodeIdAttribute() {

        return $this->id;
    }

    public function scopeApproved($query) { 
            
        return $query->where('status', APPROVED);
    }

    public function user() {

        return $this->belongsTo(User::class, 'user_id');
        
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            
            $model->attributes['unique_id'] = "UPC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UPC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){
            
        });

    }
}
