<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AudioCallPayment extends Model
{
    
    protected $hidden = ['id','unique_id'];

     protected $appends = ['audio_call_payment_id','username', 'user_picture', 'modelname', 'model_picture', 'paid_amount_formatted','admin_amount_formatted','user_amount_formatted'];
 
     public function getAudioCallPaymentIdAttribute() {
 
         return $this->id;
     }
 
     public function getPaidAmountFormattedAttribute() {
 
        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->paid_amount);
     }
 
     public function getAdminAmountFormattedAttribute() {
 
        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->admin_token : $this->admin_amount);
     }
 
     public function getUserAmountFormattedAttribute() {
 
        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->user_token : $this->user_amount);
     }
     
 
     public function getUserPictureAttribute() {
 
         return $this->fromUser->picture ?? "";
     }

     public function getUsernameAttribute() {
 
         return $this->user->name ?? "";
     }
 
     public function getModelnameAttribute() {
 
         return $this->model->name ?? "";
     }
 
     public function getModelPictureAttribute() {
 
         return $this->model->picture ?? "";
     }
 
     public function user() {
 
        return $this->belongsTo(User::class, 'user_id');
     }
 
     public function model() {
 
        return $this->belongsTo(User::class, 'model_id');
     }
 
     public function Audiocallrequest() {
 
        return $this->belongsTo(AudioCallRequest::class, 'audio_call_request_id');
     }
 
     /**
      * Scope a query to only include active users.
      *
      * @return \Illuminate\Database\Eloquent\Builder
      */
     public function scopePaidApproved($query) {
 
         $query->where('audio_call_payments.status', PAID)->where('audio_call_payments.paid_amount', '>', 0);
 
         return $query;
 
     }

     public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "ACP"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "ACP"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }


}
