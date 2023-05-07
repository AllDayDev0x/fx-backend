<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoCallRequest extends Model
{
    //
    protected $hidden = ['id','unique_id'];

    protected $appends = ['video_call_request_id','video_call_request_unique_id', 'username', 'user_displayname','user_picture', 'user_unique_id','modelname','model_displayname','model_picture', 'model_unique_id',];

    protected $guarded = ['id'];

    public function getVideoCallRequestIdAttribute() {

      return $this->id;
    }

    public function getVideoCallRequestUniqueIdAttribute() {

      return $this->unique_id;
    }

    public function getUsernameAttribute() {

      $username = $this->user->username ?? "";

      unset($this->user);

      return $username ?? "";
    }

    public function getModelnameAttribute() {

      $name = $this->model->username ?? "";

      unset($this->model);

      return $name ?? "";
    }

    public function getUserUniqueIdAttribute() {

      $user_unique_id = $this->user->unique_id ?? "";

      unset($this->user);

      return $user_unique_id ?? "";
    }

    public function getUserDisplaynameAttribute() {

      $name = $this->user->name ?? "";

      unset($this->user);

      return $name ?? "";
    }


    public function getUserPictureAttribute() {

      $picture = $this->user->picture ?? "";

      unset($this->user);

      return $picture ?? "";
    }

    public function getModelUniqueIdAttribute() {

      $user_unique_id = $this->model->unique_id ?? "";

      unset($this->model);

      return $user_unique_id ?? "";
    }



    public function getModelDisplaynameAttribute() {

      $name = $this->model->name ?? "";

      unset($this->model);

      return $name ?? "";
    }


    public function getModelPictureAttribute() {

      $picture = $this->model->picture ?? "";

      unset($this->model);

      return $picture ?? "";
    }

    
    public function user() {

      return $this->belongsTo(User::class, 'user_id');
    }

    public function model() {

      return $this->belongsTo(User::class, 'model_id');
    }

    public function videoCallPayments() {

      return $this->hasOne(VideoCallPayment::class, 'video_call_request_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "VCR"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "VCR"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
