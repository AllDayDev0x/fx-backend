<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
	protected $hidden = ['id','unique_id'];

	protected $appends = ['amount_formatted','post_id','post_unique_id', 'username', 'user_displayname','user_picture', 'user_unique_id', 'total_likes', 'total_comments','is_verified_badge','verified_badge_file','created', 'content_formatted'];

	public function getAmountFormattedAttribute() {

		return formatted_amount(\Setting::get('is_only_wallet_payment') ? $this->token : $this->amount);
	}

	public function getPostIdAttribute() {

		return $this->id;
	}

	public function getPostUniqueIdAttribute() {

		return $this->unique_id;
	}

	public function getUserUniqueIdAttribute() {

		$user_unique_id = $this->user->unique_id ?? "";

		// unset($this->user);

		return $user_unique_id ?? "";
	}

	public function getUsernameAttribute() {

		$username = $this->user->username ?? "";

		// unset($this->user);

		return $username ?? "";
	}

	public function getUserDisplaynameAttribute() {

		$name = $this->user->name ?? "";

		// unset($this->user);

		return $name ?? "";
	}

	public function getIsVerifiedBadgeAttribute() {

		$is_verified_badge = $this->user->is_verified_badge ?? "";

		// unset($this->user);

		return $is_verified_badge ?? "";
	}

	public function getUserPictureAttribute() {

		$picture = $this->user->picture ?? "";

		// unset($this->user);

		return $picture ?? "";
	}

	public function getTotalLikesAttribute() {
		
	    return $this->hasMany(PostLike::class, 'post_id')->count();

	}

	public function getTotalCommentsAttribute() {

		$total_comments = $this->postComments->count() ?? 0;

		unset($this->postComments);
		
	    return $total_comments;

	}

	public function getVerifiedBadgeFileAttribute() {

        $verified_badge_file = $this->user->is_verified_badge ?? \Setting::get('verified_badge_file');

        // unset($this->user);

        return $verified_badge_file ?? "";
    }

    public function getCreatedAttribute() {

        return $this->created_at->diffForHumans() ?? "";
    }

    public function getContentFormattedAttribute() {
        
        // - only for mobile apps.
        $content = $this->content;
        
        // removed html tags except <a> tag
        $content = strip_tags($content, ['a']);

        // removed class
        $content = preg_replace('/class=".*?"/','', $content);

        // removed values for a tag
        $content = preg_replace('#(<a.*?>).*?(</a>)#', '$1$2', $content);
        
        // explode the values for formatting
        $exploded_values = explode("</a>",$content);

        $formatted = [];

        foreach ($exploded_values as $key => $value) {

            $href_url = '<a href="'.\Setting::get('frontend_url');

            $url = '<a href="';

            if (str_contains($value, $href_url)) {

                $format_1 = str_replace($href_url, "!@", $value);
            
                $format_2 = str_replace('">', "@!", $format_1);

                $formatted[] = $format_2;

            } else if (str_contains($value, $url)) {

                $format_1 = str_replace($url, "!", $value);
            
                $format_2 = str_replace('" >', "#!", $format_1);

                $formatted[] = $format_2;

            } else {

                $formatted[] = $value;
                
            }
            
        }

        $formatted = implode(' ', $formatted);

        return $formatted;

    }

	public function user() {

	   return $this->belongsTo(User::class, 'user_id');
	}

	public function postBookmark() {

	    return $this->belongsTo(PostBookmark::class, 'post_id', 'post_id');
	 }

	public function postFiles() {

	   return $this->hasMany(PostFile::class, 'post_id');
	}

	public function postLikes() {

	   return $this->hasMany(PostLike::class, 'post_id');
	}

	public function postComments() {

	   return $this->hasMany(PostComment::class, 'post_id');
	}

	public function postBookmarks() {

	   return $this->hasMany(PostBookmark::class, 'post_id');
	}

	public function postPayments() {

	   return $this->hasMany(PostPayment::class, 'post_id');
	}

	public function reportPosts() {

		return $this->hasMany(ReportPost::class, 'post_id');
	}

	public function postCategoryDetails() {

	   return $this->hasMany(CategoryDetail::class, 'post_id')->where('type', CATEGORY_TYPE_POST);
	}

	public function postHashtag() {

	   return $this->hasMany(PostHashtag::class, 'post_id');
	}

	/**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePaidApproved($query) {

        $query->where('posts.is_published', YES)->where('posts.status', YES)->where('posts.is_paid_post', YES)->where('posts.amount', '>', 0);

        return $query;

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('posts.is_published', YES)->where('posts.status', YES);

        return $query;

    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOriginalResponse($query) {

        return $query->select(
	            'posts.*',
	            'posts.amount as amount'
	    );
    
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeTokenResponse($query) {

    	return $query->select(
	        'posts.*',
	        'posts.token as amount'
	    );
    
    }

	public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PF"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PF"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){

            $model->postLikes()->delete();

            $model->postComments()->delete();

            $model->postPayments()->delete();

            $model->postBookmarks()->delete();
            
			foreach ($model->postFiles as $key => $postFile) {

                $postFile->delete();

            }
			
			$model->reportPosts()->delete();

			$model->postCategoryDetails()->delete();

			$model->postHashtag()->delete();
            
        });

    }
}
