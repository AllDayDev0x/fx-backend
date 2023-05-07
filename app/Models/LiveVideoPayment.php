<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveVideoPayment extends Model
{
    protected $appends = ['live_video_payment_id', 'admin_amount_formatted', 'user_amount_formatted', 'live_video_amount_formatted'];

    protected $hidden = ['id'];

    public function getLiveVideoPaymentIdAttribute() {

        return $this->id;
    }

    public function getAdminAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->admin_token : $this->admin_amount);
    }

    public function getUserAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->user_token : $this->user_amount);
    }

    public function getLiveVideoAmountFormattedAttribute() {

        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->live_video_amount);

    }

    public function user() {

        return $this->belongsTo(User::class,'user_id');
    }

    public function fromUser() {

        return $this->belongsTo(User::class,'live_video_viewer_id');
    }

    public function videoDetails() {

        return $this->belongsTo(LiveVideo::class, 'live_video_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "LVP"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "LVP"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

        });

    }
}
