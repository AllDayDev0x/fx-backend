<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VcChatMessage extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['vc_chat_message_id', 'vc_chat_message_unique_id','from_username','from_displayname','from_userpicture','from_user_unique_id','to_username','to_displayname','to_userpicture','to_user_unique_id'];

	public function getVcChatMessageIdAttribute() {

		return $this->id;
	}

	public function getVcChatMessageUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getFromUsernameAttribute() {

		return $this->modelUser->username ?? tr('n_a');
	}

	public function getFromUserPictureAttribute() {

		return $this->modelUser->picture ?? asset('placeholder.jpeg');
	}

	public function getFromDisplaynameAttribute() {

		return $this->modelUser->name ?? tr('n_a');
	}

	public function getFromUserUniqueIdAttribute() {

		return $this->modelUser->unique_id ?? '';
	}

	public function getToUsernameAttribute() {

		return $this->user->username ?? tr('n_a');
	}

	public function getToUserPictureAttribute() {

		return $this->user->picture ?? asset('placeholder.jpeg');
	}

	public function getToDisplaynameAttribute() {

		return $this->user->name ?? tr('n_a');
	}

	public function getToUserUniqueIdAttribute() {

		return $this->user->unique_id ?? '';
	}
	
	public function modelUser() {

	   return $this->belongsTo(User::class, 'model_id');
	}

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CM"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CM"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
