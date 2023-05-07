<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioCallRequest extends Model
{
    
    protected $hidden = ['id','unique_id'];

    protected $appends = ['audio_call_request_id','audio_call_request_unique_id', 'username', 'user_displayname','user_picture', 'user_unique_id','modelname','model_displayname','model_picture', 'model_unique_id'];

    protected $guarded = ['id'];

    public function getAudioCallRequestIdAttribute() {

      return $this->id;
    }

    public function getAudioCallRequestUniqueIdAttribute() {

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

    public function audioCallPayments() {

      return $this->hasOne(AudioCallPayment::class, 'audio_call_request_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "ACR"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "ACR"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }

}
