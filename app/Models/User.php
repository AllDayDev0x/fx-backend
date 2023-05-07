<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

use Setting, DB, Cache;

use App\Helpers\Helper;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected $appends = ['user_id', 'user_unique_id', 'is_notification', 'is_document_verified_formatted', 'total_followers', 'total_followings', 'user_account_type_formatted', 'total_posts', 'total_fav_users', 'total_bookmarks', 'is_subscription_enabled', 'share_link','orders_count','tipped_amount', 'is_user_online', 'is_welcome_steps','video_call_amount_formatted', 'verified_badge_file','total_video_call_requests','total_post_payment','about_formatted','eyes_color_formatted','height_formatted','weight_formatted','audio_call_amount_formatted','wallet_balance_formatted', 'is_stories'];

    public function getIsStoriesAttribute() {

        $is_stories = $this->stories()->CurrentStories()->count();

        unset($this->stories);

        return $is_stories > 0 ? YES : NO;
    }

    public function getIsWelcomeStepsAttribute() {

        return $this->id;
    }

    public function getUserIdAttribute() {

        return $this->id;
    }

    public function getUserUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getIsNotificationAttribute() {

        return $this->is_email_notification ? YES : NO;
    }

    public function getIsUserOnlineAttribute() {

        return Cache::has($this->id) ? YES : NO;
    }

    public function getIsSubscriptionEnabledAttribute() {

        if($this->is_document_verified && $this->has('userBillingAccounts') && $this->is_email_verified) {
            return YES;
        }

        return NO;
    }

    public function getIsDocumentVerifiedFormattedAttribute() {

        return user_document_status_formatted($this->is_document_verified);
    }

    public function getTotalFollowersAttribute() {

        $count = $this->followers->where('status',FOLLOWER_ACTIVE)->count();

        unset($this->followers);
        
        return $count;

    }

    public function getWalletBalanceFormattedAttribute() {

        $user_wallet_balance = $this->userWallets ? $this->userWallets->remaining : 0;
        
        unset($this->userWallets);
        
        return formatted_amount($user_wallet_balance);

    }

    public function getShareLinkAttribute() {

        $share_link = \Setting::get('frontend_url').$this->unique_id;
        
        return $share_link;

    }

    public function getTotalFollowingsAttribute() {

        $count = $this->followings->where('status', YES)->count();

        unset($this->followings);
        
        return $count;

    }

    public function getTotalPostsAttribute() {
        
        $count = $this->posts->count();

        unset($this->posts);
        
        return $count;

    }

    public function getTotalFavUsersAttribute() {
        
        $count = $this->favUsers->count();

        unset($this->favUsers);
        
        return $count;

    }

    public function getVerifiedBadgeFileAttribute() {

        $verified_badge_file = $this->is_verified_badge ? \Setting::get('verified_badge_file') : '';

        // unset($this->user);

        return $verified_badge_file ?? "";
    }

    public function getTotalBookmarksAttribute() {
        
        $count = $this->postBookmarks->count();

        unset($this->postBookmarks);
        
        return $count;

    }

    public function getOrdersCountAttribute() {

        $count = $this->orders->count();

        unset($this->orders);
        
        return $count;

    }

    public function getUserAccountTypeFormattedAttribute() {
        
        return user_account_type_formatted($this->user_account_type);

    }

    public function getAboutFormattedAttribute() {
        
        return $this->about == "null" ? "" : $this->about;

    }

    public function getEyesColorFormattedAttribute() {
        
        return $this->eyes_color ?? "";

    }

    public function getHeightFormattedAttribute() {
        
        return height_formatted($this->height);

    }

    public function getWeightFormattedAttribute() {
        
        return weight_formatted($this->weight);

    }

    public function userBillingAccounts() {

        return $this->hasMany(UserBillingAccount::class, 'user_id');
    }

    public function userDocuments() {

        return $this->hasMany(UserDocument::class, 'user_id');
    }

    public function deliveryAddresses() {

        return $this->hasMany(DeliveryAddress::class,'user_id');
    }

    public function orderPayments() {

        return $this->hasMany(OrderPayment::class,'user_id');
    }

    public function posts() {

        return $this->hasMany(Post::class,'user_id');
    }

    public function postPayments() {

        return $this->hasMany(PostPayment::class,'user_id');
    }

    public function orders() {

        return $this->hasMany(Order::class,'user_id');
    }

    public function userWallets() {

        return $this->hasOne(UserWallet::class, 'user_id');
    }

    public function userWithdrawals() {

        return $this->hasMany(UserWithdrawal::class,'user_id');
    }

    public function referralCode() {

        return $this->hasOne(ReferralCode::class, 'user_id');
    }

    /**
     * Get the UserCard record associated with the user.
     */
    public function userCards() {
        
        return $this->hasMany(UserCard::class, 'user_id');
    }

    /**
     * Get the UserCard record associated with the user.
     */
    public function userSubscription() {
        
        return $this->hasOne(UserSubscription::class, 'user_id');
    }

    public function followers() {
        
        return $this->hasMany(Follower::class, 'user_id')->whereHas('follower');
    }

    public function followings() {
        
        return $this->hasMany(Follower::class, 'follower_id')->whereHas('user');
    }

    
    public function postBookmarks() {
        
        return $this->hasMany(PostBookmark::class, 'user_id');
    }

    public function favUsers() {
        
        return $this->hasMany(FavUser::class, 'user_id')->whereHas('favUser');
    }

    public function postLikes() {

        return $this->hasMany(PostLike::class,'user_id');
    }

    public function postAlbums() {

        return $this->hasMany(PostAlbum::class,'user_id');
    }

    public function postComments() {

        return $this->hasMany(PostComment::class,'user_id');
    }

    public function userTips() {

        return $this->hasMany(UserTip::class,'user_id');
    }

    public function categoryDetails() {

        return $this->hasMany(CategoryDetail::class,'user_id')->where('type', CATEGORY_TYPE_PROFILE);
    }

    public function supportTickets() {

        return $this->hasMany(SupportTicket::class,'user_id');
    }

    public function fromUserSubscriptionPayments() {

        return $this->hasMany(UserSubscriptionPayment::class,'from_user_id');
    }
    
    public function toUserSubscriptionPayments() {

        return $this->hasMany(UserSubscriptionPayment::class,'to_user_id');
    }

    public function reportPosts() {

        return $this->hasMany(ReportPost::class,'block_by');
    }

    public function getTippedAmountAttribute() {

		return formatted_amount($this->userTips()->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'amount') ?? 0.00);
	}

    public function getTotalPostPaymentAttribute() {

        return formatted_amount($this->postPayments()->sum(Setting::get('is_only_wallet_payment') ? 'token' : 'paid_amount') ?? 0.00);
    }

    public function userReferralCode()
    {
        return $this->hasOne(ReferralCode::class, 'user_id');
    }

    public function stories() {

        return $this->hasMany(Story::class,'user_id');
    }  

    public function userProducts() {

        return $this->hasMany(UserProduct::class,'user_id');
    } 

    public function userSession() {

        return $this->hasMany(UserLoginSession::class,'user_id');
    }
    
    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('users.status', USER_APPROVED)->where('is_email_verified', USER_EMAIL_VERIFIED);

        return $query;

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDocumentVerified($query) {

        $query->where('users.is_document_verified', USER_DOCUMENT_APPROVED);

        return $query;

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeCommonResponse($query) {

        return $query->select(
            'users.id as user_id',
            'users.unique_id as user_unique_id',
            'users.*'
            );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOtherResponse($query) {

        return $query->select(
            'users.id as user_id',
            'users.unique_id as user_unique_id',
            'users.*'
            );
    }
    
    public function getVideoCallAmountFormattedAttribute() {

        return formatted_amount((Setting::get('is_only_wallet_payment') ? $this->video_call_token : $this->video_call_amount) ?? 0.00);

    }

    public function getAudioCallAmountFormattedAttribute() {

        return formatted_amount((Setting::get('is_only_wallet_payment') ? $this->audio_call_token : $this->audio_call_amount) ?? 0.00);

    }
    
    public function getTotalVideoCallRequestsAttribute() {

        $count = $this->videoCallRequests->count();

        unset($this->videoCallRequests);
        
        return $count;

    }

    public function videoCallRequests() {

        return $this->hasMany(VideoCallRequest::class, 'user_id');
    }

    public function modelVideoCallRequests() {

        return $this->hasMany(VideoCallRequest::class, 'model_id');
    }

    public function liveVideos() {

        return $this->hasMany(LiveVideo::class, 'user_id');
    }
    
    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            // $model->attributes['name'] = "";

            if($model->attributes['first_name'] && $model->attributes['last_name']) {

                // $model->attributes['name'] = $model->attributes['first_name']." ".$model->attributes['last_name'];
            }

            $model->attributes['unique_id'] = strtolower($model->attributes['username']) ? : rand(1,10000).rand(1,10000);

            if($model->attributes['username'] == '') {

                $model->attributes['username'] = $model->attributes['unique_id'];

            }

            if($model->attributes['username']) {

                $model->attributes['unique_id'] = $model->attributes['username'];
            }

            $model->attributes['is_email_verified'] = USER_EMAIL_VERIFIED;

            if (Setting::get('is_account_email_verification') == YES && envfile('MAIL_USERNAME') && envfile('MAIL_PASSWORD')) { 

                if($model->attributes['login_by'] == 'manual') {

                    $model->generateEmailCode();

                }

            }

            $model->attributes['payment_mode'] = COD;

            $model->attributes['token'] = Helper::generate_token();

            $model->attributes['token_expiry'] = Helper::generate_token_expiry();

            $model->attributes['status'] = USER_APPROVED;

            if(in_array($model->attributes['login_by'], ['facebook', 'google', 'apple', 'linkedin', 'instagram'] )) {
                
                $model->attributes['password'] = \Hash::make($model->attributes['social_unique_id']);
            }

        });

        static::created(function($model) {

            $model->attributes['user_account_type'] = USER_FREE_ACCOUNT;
            
            $model->attributes['user_account_type'] = USER_FREE_ACCOUNT;

            $model->attributes['is_email_notification'] = $model->attributes['is_push_notification'] = YES;

            $model->attributes['unique_id'] = strtolower($model->attributes['username']) ? : rand(1,10000).rand(1,10000);

            if($model->attributes['username'] == '') {

                $model->attributes['username'] = $model->attributes['unique_id'];

            }
            
            $model->save();
        
        });

        static::updating(function($model) {

            $model->attributes['unique_id'] = strtolower($model->attributes['username']) ? : rand(1,10000).rand(1,10000);

            $model->attributes['website'] = isset($model->attributes['website']) && $model->attributes['website'] ? formatUrl($model->attributes['website']) : "";

            $model->attributes['amazon_wishlist'] =  isset($model->attributes['amazon_wishlist']) &&  $model->attributes['amazon_wishlist'] ? formatUrl($model->attributes['amazon_wishlist']) : "";

            $model->attributes['facebook_link'] =  isset($model->attributes['facebook_link']) &&  $model->attributes['facebook_link'] ? formatUrl($model->attributes['facebook_link']) : "";

            $model->attributes['instagram_link'] =  isset($model->attributes['instagram_link']) &&  $model->attributes['instagram_link'] ? formatUrl($model->attributes['instagram_link']) : "";

            $model->attributes['twitter_link'] =  isset($model->attributes['twitter_link']) &&  $model->attributes['twitter_link'] ? formatUrl($model->attributes['twitter_link']) : "";

            $model->attributes['linkedin_link'] =  isset($model->attributes['linkedin_link']) &&  $model->attributes['linkedin_link'] ? formatUrl($model->attributes['linkedin_link']) : "";

            $model->attributes['pinterest_link'] =  isset($model->attributes['pinterest_link']) &&  $model->attributes['pinterest_link'] ? formatUrl($model->attributes['pinterest_link']) : "";

            $model->attributes['youtube_link'] =  isset($model->attributes['youtube_link']) &&  $model->attributes['youtube_link'] ? formatUrl($model->attributes['youtube_link']) : "";

            $model->attributes['twitch_link'] =  isset($model->attributes['twitch_link']) &&  $model->attributes['twitch_link'] ? formatUrl($model->attributes['twitch_link']) : "";
            
            $model->attributes['snapchat_link'] =  isset($model->attributes['snapchat_link']) &&  $model->attributes['snapchat_link'] ? formatUrl($model->attributes['snapchat_link']) : "";

        });

        static::deleting(function ($model){

            Helper::storage_delete_file($model->picture, PROFILE_PATH_USER);

            $model->userCards()->delete();

            $model->userDocuments()->delete();
            
            $model->userBillingAccounts()->delete();

            $model->postLikes()->delete();

            $model->postAlbums()->delete();

            $model->postComments()->delete();

            $model->userTips()->delete();
                
            $model->categoryDetails()->delete();

            foreach ($model->posts as $key => $post) {
                $post->delete();
            }

            // Deleting an user post payments creating discrepancy in content creator's revenue graph. Instead of deleting records, updating user_id to 0.           

            foreach ($model->postPayments as $key => $postPayment) {
                $postPayment->update(['user_id' => 0]);
            }

            foreach ($model->orders as $key => $order) {
                $order->delete();
            }

            $model->deliveryAddresses()->delete();

            $model->userWallets()->delete();
            
            $model->referralCode()->delete();
            
            $model->userWithdrawals()->delete();

            $model->followers()->delete();

            $model->followings()->delete();

            $model->postBookmarks()->delete();

            $model->favUsers()->delete();
            
            $model->userSubscription()->delete();
            
            $model->supportTickets()->delete();

            // Deleting an user paid subscription payments creating discrepancy in content creator's revenue graph. Instead of deleting records, updating from_user_id to 0.

            foreach ($model->fromUserSubscriptionPayments as $key => $fromUserSubscriptionPayment) {
                $fromUserSubscriptionPayment->update(['from_user_id' => 0]);
            }

            $model->toUserSubscriptionPayments()->delete();

            $model->reportPosts()->delete();

            $model->videoCallRequests()->delete();

            $model->modelVideoCallRequests()->delete();

            $model->liveVideos()->delete();

            $model->userSession()->delete();

            foreach ($model->stories as $key => $story) {
                $story->delete();
            }

            foreach ($model->userProducts as $key => $userProduct) {
                $userProduct->delete();
            }

            \App\Models\ChatUser::where('from_user_id', $model->id)->orWhere('to_user_id', $model->id)->delete();

            \App\Models\ChatUser::where('to_user_id', $model->id)->delete();

            \App\Models\ChatMessage::where('from_user_id', $model->id)->orWhere('to_user_id', $model->id)->delete();
            
            \App\Models\ChatMessage::where('to_user_id', $model->id)->delete();

            $model->stories()->delete();


        });

    }

    /**
     * Generates Token and Token Expiry
     * 
     * @return bool returns true if successful. false on failure.
     */

    protected function generateEmailCode() {

        $this->attributes['verification_code'] = Helper::generate_email_code();

        $this->attributes['verification_code_expiry'] = Helper::generate_email_expiry();

        // Check Email verification controls and email configurations

        if(Setting::get('is_account_email_verification') == YES && Setting::get('is_email_notification') == YES && Setting::get('is_email_configured') == YES) {

            if($this->attributes['login_by'] != 'manual') {

                $this->attributes['is_email_verified'] = USER_EMAIL_VERIFIED;

            } else {

                $this->attributes['is_email_verified'] = USER_EMAIL_NOT_VERIFIED;
            }

        } else { 

            $this->attributes['is_email_verified'] = USER_EMAIL_VERIFIED;
        }

        return true;
    
    }
}
