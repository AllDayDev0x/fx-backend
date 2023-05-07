<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserTip extends Model
{
    protected $fillable = ['to_user_id', 'from_user_id', 'amount'];

    protected $hidden = ['id', 'unique_id'];

	protected $appends = ['user_tip_id', 'user_tip_unique_id', 'from_username', 'from_user_picture', 'to_username', 'to_user_picture', 'amount_formatted','admin_amount_formatted','user_amount_formatted'];
	
	public function getUserTipIdAttribute() {

		return $this->id;
	}

	public function getUserTipUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getAmountFormattedAttribute() {

		return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->amount);
	}

	public function getAdminAmountFormattedAttribute() {

		return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->admin_token : $this->admin_amount);
	}

	public function getUserAmountFormattedAttribute() {

		return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->user_token : $this->user_amount);
	}

	public function getFromUsernameAttribute() {

		return $this->fromUser->name ?? "";
	}

	public function getFromUserPictureAttribute() {

		return $this->fromUser->picture ?? "";
	}

	public function getToUsernameAttribute() {

		return $this->toUser->name ?? "";
	}

	public function getToUserPictureAttribute() {

		return $this->toUser->picture ?? "";
	}

	public function fromUser() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function toUser() {

	   return $this->belongsTo(User::class, 'to_user_id');
	}

	public function post() {

	   return $this->belongsTo(Post::class, 'post_id');
	}

	 /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */

    public function scopeCommonResponse($query) {
		
		return $query;
		
    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "UT-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "UT-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });
	}
	

	 /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserPaid($query, $from_user_id, $to_user_id) {

        $query->where('user_tips.user_id', $from_user_id)->where('user_tips.to_user_id', $to_user_id)->where('user_tips.status', PAID);

        return $query;

    }
}
