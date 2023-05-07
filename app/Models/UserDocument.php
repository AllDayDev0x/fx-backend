<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDocument extends Model
{
	protected $fillable = ['user_id', 'document_id','document_file'];

	protected $hidden = ['deleted_at', 'id', 'unique_id'];

	protected $appends = ['user_document_id','user_document_unique_id'];

	public function getUserDocumentIdAttribute() {

        return $this->id;
    }

    public function getUserDocumentUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function user() {

    	return $this->belongsTo(User::class, 'user_id');
    }

    public function document() {

    	return $this->belongsTo(Document::class, 'document_id');
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

            $model->attributes['unique_id'] = 'KID'."-".uniqid();

            $model->is_verified = USER_DOCUMENT_NONE;
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = 'KID'."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model) {

        	\Helper::storage_delete_file($model->document_file, DOCUMENTS_PATH);

            \Helper::storage_delete_file($model->document_file_front, DOCUMENTS_PATH);

            \Helper::storage_delete_file($model->document_file_back, DOCUMENTS_PATH);

        });

    }
}
