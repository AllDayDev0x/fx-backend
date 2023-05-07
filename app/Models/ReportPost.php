<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReportPost extends Model
{
    //

    protected $fillable = ['post_id', 'block_by','reason'];

    protected $hidden = ['id'];

	protected $appends = ['report_post_id','report_reason'];
	
	public function getReportPostIdAttribute() {

		return $this->id;
	}

    public function blockeduser() {

		return $this->belongsTo(User::class,'block_by');
    }
    
    public function post() {

		return $this->belongsTo(Post::class,'post_id');
	}

	public function getReportReasonAttribute() {

		return $this->belongsTo(ReportReason::class,'report_reason_id');
    }
	
}
