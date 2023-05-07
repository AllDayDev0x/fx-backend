<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportReason extends Model
{
    protected $hidden = ['id', 'unique_id'];

    protected $appends = ['report_reason_id', 'report_reason_unique_id'];

    public function getReportReasonIdAttribute() {

        return $this->id;
    }

    public function getReportReasonUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function scopeApproved($query) { 
            
        return $query->where('status', APPROVED);
    }

    public static function boot() {

        parent::boot();

        static::creating(function ($model) {
            
            $model->attributes['unique_id'] = "RC"."-".uniqid();
        });

        static::created(function($model) {

            $model->attributes['unique_id'] = "RC"."-".$model->attributes['id']."-".uniqid();

            $model->save();
        
        });

        static::deleting(function ($model){
            
        });

    }
}
