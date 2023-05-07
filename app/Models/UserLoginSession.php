<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class UserLoginSession extends Model
{
    
    protected $hidden = ['id','unique_id'];

    protected $appends = ['user_login_session_id', 'user_login_session_unique_id'];

    public function getUserLoginSessionIdAttribute() {

        return $this->id;
    }

    public function getUserLoginSessionUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function user() {

       return $this->belongsTo(User::class, 'user_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            
            $model->attributes['unique_id'] = "ULS"."-".uniqid();

            $model->attributes['token'] = Helper::generate_token();

            $model->attributes['token_expiry'] = Helper::generate_token_expiry();

        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "ULS"."-".$model->attributes['id']."-".uniqid();

            // $model->attributes['token'] = Helper::generate_token();

            $model->attributes['token_expiry'] = Helper::generate_token_expiry();
        
        });

    }

}
