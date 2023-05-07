<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

use Illuminate\Notifications\Notifiable;
	
class SupportMember extends Authenticatable
{
    use Notifiable;

     /**
     * Scope a query to only include active members.
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeApproved($query) {

        $query->where('support_members.status', APPROVED);

        return $query;

    }


    public static function boot() {

        parent::boot();

        static::creating(function ($model) {

            $model->attributes['name'] = $model->attributes['first_name']." ".$model->attributes['last_name'];

            $model->attributes['username'] = routefreestring($model->attributes['name']);

        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "SID"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::updating(function($model) {

            $model->attributes['name'] = $model->attributes['last_name']." ".$model->attributes['name'];

            $model->attributes['username'] = routefreestring($model->attributes['name']);


        });

        static::deleting(function ($model){

            Helper::storage_upload_file($model->picture, SUPPORT_MEMBER_FILE_PATH);

        });

    }

}
