<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveVideoChatMessage extends Model
{
    protected $hidden = ['id','unique_id'];

	protected $appends = ['lv_chat_message_id', 'lv_chat_message_unique_id', 'from_username', 'from_displayname', 'from_userpicture', 'from_user_unique_id', 'created'];

	protected $guarded = ['id'];

	public function getLvChatMessageIdAttribute() {

		return $this->id;
	}

	public function getLvChatMessageUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getFromUsernameAttribute() {

		$key = $this->fromUser->username ?? tr('n_a');

		unset($this->fromUser);

		return $key;
	}

	public function getFromUserPictureAttribute() {

		$key = $this->fromUser->picture ?? asset('placeholder.jpeg');

		unset($this->fromUser);

		return $key;
	}

	public function getFromDisplaynameAttribute() {

		$key = $this->fromUser->name ?? tr('n_a');

		unset($this->fromUser);

		return $key;

	}

	public function getFromUserUniqueIdAttribute() {

		$key = $this->fromUser->unique_id ?? '';

		unset($this->fromUser);

		return $key;

	}

	public function getCreatedAttribute() {

		return $this->created_at ? $this->created_at->diffForHumans() : "";

	}

	public function fromUser() {

	   return $this->belongsTo(User::class, 'from_user_id');
	}

	public function liveVideo() {

	   return $this->belongsTo(LiveVideo::class, 'live_video_id');
	}
}
