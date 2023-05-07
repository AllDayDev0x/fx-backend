<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostCommentReply extends Model
{
    protected $fillable = ['post_id', 'user_id', 'post_comment_id', 'reply'];

    protected $hidden = ['id','unique_id'];

    protected $appends = ['post_comment_reply_id','post_comment_reply_unique_id', 'total_comment_reply_likes', 'username', 'user_displayname','user_picture', 'user_unique_id','created','reply_formatted'];
    
    public function getPostCommentReplyIdAttribute() {

        return $this->id;
    }

    public function getPostCommentReplyUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getCreatedAttribute() {

        return $this->created_at->diffForHumans() ?? "";
    }

    public function getReplyFormattedAttribute() {

        // - only for mobile apps.
        $content = $this->reply;
        
        // removed html tags except <a> tag
        $content = strip_tags($content, ['a']);

        // removed class
        $content = preg_replace('/class=".*?"/','', $content);

        // removed values for a tag
        $content = preg_replace('#(<a.*?>).*?(</a>)#', '$1$2', $content);
        
        // explode the values for formatting
        $exploded_values = explode("</a>",$content);

        $comment = [];

        foreach ($exploded_values as $key => $value) {

            $href_url = '<a href="'.\Setting::get('frontend_url');

            $url = '<a href="';

            if (str_contains($value, $href_url)) {

                $format_1 = str_replace($href_url, "!@", $value);
            
                $format_2 = str_replace('">', "@!", $format_1);

                $comment[] = $format_2;

            } else if (str_contains($value, $url)) {

                $format_1 = str_replace($url, "!", $value);
            
                $format_2 = str_replace('" >', "#!", $format_1);

                $comment[] = $format_2;

            } else {

                $comment[] = $value;
                
            }
            
        }

        $comment = implode(' ', $comment);

        return $comment;
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

    public function getTotalCommentReplyLikesAttribute() {

        $post_comment_reply_count = $this->postCommentReplyLikes ? $this->postCommentReplyLikes->count() : 0;

        unset($this->postCommentReplyLikes);

        return $post_comment_reply_count ?? "";
    }

    public function user() {

       return $this->belongsTo(User::class, 'user_id');
    }

    public function post() {

       return $this->belongsTo(Post::class, 'post_id');
    }

    public function postCommentReplyLikes() {

       return $this->hasMany(PostCommentLike::class, 'post_comment_reply_id')->where('status', LIKE);
    }

    /**
     * Scope a query to only include active users.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('post_comment_replies.status', APPROVED);

        return $query;

    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "PCR"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "PCR"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
