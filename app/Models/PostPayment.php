<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostPayment extends Model
{
	protected  $appends = ['paid_amount_formatted','admin_amount_formatted','user_amount_formatted'];

    protected $fillable = ['user_id'];

    public function getPaidAmountFormattedAttribute() {

    	return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->paid_amount);
    }

    public function user() {

    	return $this->belongsTo(User::class,'user_id');
    }

    public function postDetails() {

    	return $this->belongsTo(Post::class, 'post_id');
    }


    public function getAdminAmountFormattedAttribute() {

    	return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->admin_token : $this->admin_amount);
    }


    public function getUserAmountFormattedAttribute() {

    	return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->user_token : $this->user_amount);
    }


    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeUserPaid($query, $user_id, $post_id) {

        $query->where('post_payments.post_id', $post_id)->where('post_payments.user_id', $user_id)->where('post_payments.status', PAID);

        return $query;

    }
}
