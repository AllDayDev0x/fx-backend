<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatAsset extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['chat_asset_id', 'chat_asset_unique_id'];

	public function getChatAssetIdAttribute() {

		return $this->id;
	}

	public function getChatAssetUniqueIdAttribute() {

		return $this->unique_id;
	}

    public function chatMessage() {

        return $this->belongsTo(ChatMessage::class, 'chat_message_id');
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return $query->select(
            'chat_assets.*',
            'chat_assets.file as asset_file',
        );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeBlurResponse($query) {

        return $query->select(
            'chat_assets.*',
            'chat_assets.blur_file as asset_file',
        );
    
    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CA"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CA"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
