<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PromoCode extends Model
{
    protected $appends = ['promo_code_id','user_displayname'];

    protected $hidden = ['id', 'unique_id'];

    public function getPromoCodeIdAttribute() {

        return $this->id;
    }

    public function scopeApproved($query) { 
            
        return $query->where('status', APPROVED);
    }

    public function user() {

        return $this->belongsTo(User::class, 'user_id');
        
    }

    public function getUserDisplaynameAttribute() {

        $name = $this->user->name ?? "";

        // unset($this->user);

        return $name ?? "";
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
            
        });

    }
}
