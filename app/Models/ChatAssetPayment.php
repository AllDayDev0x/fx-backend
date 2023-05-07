<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAssetPayment extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['chat_asset_payment_id', 'chat_asset_payment_unique_id', 'amount_formatted', 'admin_amount_formatted', 'user_amount_formatted'];

	public function getChatAssetPaymentIdAttribute() {

		return $this->id;
	}

	public function getChatAssetPaymentUniqueIdAttribute() {

		return $this->unique_id;
	}

    public function chatMessage() {

        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    public function chatAssets() {

	   return $this->hasMany(ChatAsset::class, 'chat_message_id');
	}

    public function fromUser() {

        return $this->belongsTo(User::class,'from_user_id');
    }

    public function toUser() {

        return $this->belongsTo(User::class, 'to_user_id');
    }

    public function getAmountFormattedAttribute() {
 
        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->paid_amount);
     }
 
     public function getAdminAmountFormattedAttribute() {
 
        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->admin_token : $this->admin_amount);
     }
 
     public function getUserAmountFormattedAttribute() {
 
        return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->user_token : $this->user_amount);
     }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CAP"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CAP"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
