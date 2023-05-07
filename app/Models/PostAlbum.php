<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostAlbum extends Model
{	

	protected $appends = ['post_album_id','post_album_unique_id'];

	protected $hidden = ['id','unique_id'];

	public function getPostAlbumIdAttribute() {

		return $this->id;
	}

	public function getPostAlbumUniqueIdAttribute() {

		return $this->unique_id;
	}

    public function user() {

    	return $this->belongsTo(User::class,'user_id');
    }
}
