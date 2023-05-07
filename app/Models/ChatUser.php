<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChatUser extends Model
{
    protected $fillable = ['from_user_id', 'to_user_id'];

    protected $hidden = ['id','unique_id'];

	protected $appends = ['chat_user_id', 'chat_user_unique_id', 'from_username', 'from_displayname', 'from_userpicture', 'to_username', 'to_displayname', 'to_userpicture'];

	public function getChatUserIdAttribute() {

		return $this->id;
	}

	public function getChatUserUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getFromUsernameAttribute() {

		return $this->fromUser->username ?? tr('n_a');
	}

	public function getFromUserPictureAttribute() {

		return $this->fromUser->picture ?? asset('placeholder.jpeg');
	}

	public function getFromDisplaynameAttribute() {

		return $this->fromUser->name ?? tr('n_a');
	}

	public function getToUsernameAttribute() {

		return $this->toUser->username ?? tr('n_a');
	}

	public function getToUserPictureAttribute() {

		return $this->toUser->picture ?? asset('placeholder.jpeg');
	}

	public function getToDisplaynameAttribute() {

		return $this->toUser->name ?? tr('n_a');
	}

	public function fromUser() {

	   return $this->belongsTo(User::class, 'from_user_id');
	}

	public function toUser() {

	   return $this->belongsTo(User::class, 'to_user_id');
	}
}
