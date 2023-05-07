<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VerificationCode extends Model
{

    protected $hidden = ['id','unique_id'];

    protected $appends = ['verification_code_id','verification_code_unique_id'];

    public function getVerificationCodeIdAttribute() {

      return $this->id;
    }

    public function getVerificationCodeUniqueIdAttribute() {

      return $this->unique_id;
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "VC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
