<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BellNotification extends Model
{
    protected $fillable = ['user_id', 'fav_user_id'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['bell_notification_id', 'bell_notification_unique_id', 'from_username', 'from_userpicture', 'from_user_unique_id', 'from_displayname', 'to_username', 'to_userpicture', 'to_displayname'];
	
	public function getBellNotificationIdAttribute() {

		return $this->id;
	}

	public function getBellNotificationUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getFromUserUniqueIdAttribute() {

		return $this->fromUser->user_unique_id ?? "";
	}

	public function getFromUsernameAttribute() {

		return $this->fromUser->username ?? "";
	}

	public function getFromUserpictureAttribute() {

		return $this->fromUser->picture ?? "";
	}

	public function getFromDisplaynameAttribute() {

		return $this->fromUser->name ?? "";
	}
	
	public function getToUsernameAttribute() {

		return $this->ToUser->username ?? "";
	}

	public function getToUserpictureAttribute() {

		return $this->ToUser->picture ?? "";
	}

	public function getToDisplaynameAttribute() {

		return $this->ToUser->name ?? "";
	}

	public function ToUser() {

	   return $this->belongsTo(User::class, 'to_user_id');
	}

	public function fromUser() {

	   return $this->belongsTo(User::class, 'from_user_id');
	}

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "BN-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "BN-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
