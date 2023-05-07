<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{	

	/**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = ['id'];

    protected $appends = ['static_page_id','page_type', 'static_page_unique_id', 'updated_at_formatted'];

    public function getStaticPageIdAttribute() {

        return $this->id;
    }

    public function getStaticPageUniqueIdAttribute() {

        return $this->unique_id;
    }

    public function getPageTypeAttribute() {

        return $this->type;
    }

    public function getUpdatedAtFormattedAttribute() {

        return $this->updated_at->diffForHumans();
    }

    /**
     * Get the Approved details 
     */
    public function scopeApproved($query) {
        
        return $query->where('static_pages.status' , APPROVED);	
    }
}
