<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoryDetail extends Model
{
    use HasFactory;

    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['category_detail_id','category_detail_unique_id', 'username'];

    public function getCategoryDetailIdAttribute() {

        return $this->id;
    }

    public function getCategoryDetailUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getUserNameAttribute() {

        return $this->user->name ?? "";
    }

    public function category(){

        return $this->belongsTo(Category::class,'category_id');
    }

    public function user(){

        return $this->belongsTo(User::class,'user_id');
    }

    public function post(){

        return $this->belongsTo(Post::class,'post_id');
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            $model->attributes['unique_id'] = "CD"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "CD"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

    }
}
