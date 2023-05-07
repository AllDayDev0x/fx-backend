<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserCard extends Model
{
    protected $appends = ['picture'];

    public function getPictureAttribute() {

        return get_card_picture($this->card_type);
    }
}