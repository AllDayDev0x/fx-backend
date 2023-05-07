<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Setting;

class LiveVideo extends Model
{	
	protected $hidden = ['id','unique_id'];

    protected $appends = ['live_video_id', 'live_video_unique_id','amount_formatted', 'payment_type_text','created_at_formatted','status_formatted','username', 'user_displayname','user_picture', 'user_unique_id', 'admin_amount_formatted','user_amount_formatted','is_verified_badge', 'viewer_cnt_formatted', 'share_link'];

    protected $guarded = ['id'];

    public function getLiveVideoIdAttribute() {

        return $this->id;
    }

    public function getLiveVideoUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getAmountFormattedAttribute() {

        return formatted_amount(Setting::get('is_only_wallet_payment') ? $this->token : $this->amount);
    }

    public function getAdminAmountFormattedAttribute() {

        return formatted_amount($this->payments()->where('status',PAID)->sum(Setting::get('is_only_wallet_payment') ? 'admin_token' : 'admin_amount') ?? 0.00);
    }

    public function getUserAmountFormattedAttribute() {

        return formatted_amount($this->payments()->where('status',PAID)->sum(Setting::get('is_only_wallet_payment') ? 'user_token' : 'user_amount') ?? 0.00);
    }

    public function getPaymentTypeTextAttribute() {

        return formatted_live_payment_text($this->payment_status);
    }

    public function getStatusFormattedAttribute() {

        return live_video_status($this->status);
    }

    public function getCreatedAtFormattedAttribute() {
        return $this->asDateTime($this->created_at)->format('Y-m-d h:i A');
    }

    public function getIsVerifiedBadgeAttribute() {

        $is_verified_badge = $this->user->is_verified_badge ?? "";

        unset($this->user);

        return $is_verified_badge ?? "";
    }

    public function getUserUniqueIdAttribute() {

        $user_unique_id = $this->user->unique_id ?? "";

        unset($this->user);

        return $user_unique_id ?? "";
    }

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

    public function getUserPictureAttribute() {

        $picture = $this->user->picture ?? "";

        unset($this->user);

        return $picture ?? "";
    }

    public function getViewerCntFormattedAttribute() {

        return number_format_short($this->viewer_cnt);
    }

    public function getShareLinkAttribute() {

        return Setting::get('frontend_url').'/join/'.$this->unique_id;
    }

    public function setUniqueIdAttribute($value){

		$this->attributes['unique_id'] = uniqid(str_replace(' ', '-', $value));

	}

	public function payments() {

		return $this->hasMany('App\Models\LiveVideoPayment');
		
	}

	public function user() {

       return $this->belongsTo(User::class, 'user_id');
    }

	/**
     * Load viewers using relation model
     */
    public function getViewers()
    {
        return $this->hasMany('App\Models\Viewer', 'video_id', 'id');
    }

    /**
     * Load viewers using relation model
     */
    public function getVideosPayments()
    {
        return $this->hasMany('App\Models\LiveVideoPayment', 'live_video_id', 'id');
    }


    /**
     * Boot function for using with User Events
     *
     * @return void
     */
    protected static function boot()
    {
        parent::boot();

         //delete your related models here, for example
        static::deleting(function($model)
        {

            if ($model->getVideosPayments->count() > 0) {

                foreach($model->getVideosPayments as $videoPayments)
                {
                    $videoPayments->delete();
                } 

            }

            // if($model->snapshot) {

            //     Helper::delete_picture($model->snapshot, "uploads/rooms");

            // }

        });
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCurrentLive($query) {

        return $query->where('live_videos.is_streaming', YES)
                ->where('live_videos.status', VIDEO_STREAMING_ONGOING);

    }

    /**
     * Scope a query to get only public live videos.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublicVideos($query) {

        return $query->where(['type' => TYPE_PUBLIC]);

    }/**
     * Scope a query to get only private live videos.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePrivateVideos($query) {

        return $query->where(['type' => TYPE_PRIVATE]);

    }

}
