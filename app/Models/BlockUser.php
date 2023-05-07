<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlockUser extends Model
{
    //

    protected $fillable = ['block_by', 'blocked_to','reason'];

    protected $hidden = ['id'];

	protected $appends = ['block_user_id','blocked_username','blocked_by','username', 'user_displayname'];
	
    public function getUsernameAttribute() {

        $username = $this->user->username ?? "";

        unset($this->user);

        return $username ?? "";
    }

    public function getUserDisplaynameAttribute() {

        $name = $this->user->name ?? "";

        unset($this->user);

        return $name ?? "";
    }

	public function getBlockUserIdAttribute() {

		return $this->id;
    }
      
    public function getBlockedUsernameAttribute() {

    	return $this->blockeduser->name ?? '';
    }
  
    public function getBlockedByAttribute() {

		return $this->user->name ?? '';
	}

    public function user() {

		return $this->belongsTo(User::class,'block_by');
    }

    public function blockeduser() {

		return $this->belongsTo(User::class,'blocked_to');
    }
	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('block_users.status', APPROVED);

        return $query;

    }

	
}
