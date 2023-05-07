<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class Category extends Model
{
	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

    protected $appends = ['category_id', 'category_unique_id', 'total_users', 'total_post','total_vod'];

    public function getCategoryIdAttribute() {

        return $this->id;
    }

    public function getCategoryUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getTotalUsersAttribute() {
        
        return $this->hasMany(CategoryDetail::class,'category_id')->where('type', CATEGORY_TYPE_PROFILE)->whereHas('user')->distinct('user_id')->count();
    }

    public function userCategoryDetails() {

        return $this->hasMany(CategoryDetail::class,'category_id')->where('type', CATEGORY_TYPE_PROFILE)->groupBy('user_id');
    }

    public function getTotalPostAttribute() {
        
        return $this->postCategoryDetails()->whereHas('post')->count();
    }

    public function getTotalVodAttribute() {
        
        $total_vod = $this->vodCategoryDetails()->whereHas('vod')->count() ?? 0;

        unset( $this->vodCategoryDetails);

        return $total_vod;
        
    }

    public function postCategoryDetails() {

        return $this->hasMany(CategoryDetail::class,'category_id')->where('type', CATEGORY_TYPE_POST);
    }

    public function vodCategoryDetails() {

        return $this->hasMany(VodCategory::class, 'category_id');
    }

    /**
     * Scope a query to only include active members.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('categories.status', APPROVED);

        return $query;

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
            $model->attributes['unique_id'] = "C"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "C"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function($model) {
        
            Helper::storage_delete_file($model->picture, COMMON_FILE_PATH);

            $model->userCategoryDetails()->delete();
            
            $model->postCategoryDetails()->delete();

        });

    }

}
