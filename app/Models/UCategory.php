<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Helpers\Helper;

class UCategory extends Model
{
	protected $hidden = ['id', 'unique_id'];

	protected $appends = ['u_category_id','u_category_unique_id','total_users'];

    public function getUCategoryIdAttribute() {

        return $this->id;
    }

    public function getUCategoryUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getTotalUsersAttribute() {
        
        return $this->hasMany(UserCategory::class,'u_category_id')->whereHas('user')->distinct('user_id')->count();
    }

    public function userCategories() {

        return $this->hasMany(UserCategory::class,'u_category_id')->groupBy('user_id');
    }

    /**
     * Scope a query to only include active members.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('u_categories.status', APPROVED);

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
            $model->attributes['unique_id'] = "uC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "uC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function($model) {
        
            Helper::storage_delete_file($model->picture, COMMON_FILE_PATH);

        });

    }
}
