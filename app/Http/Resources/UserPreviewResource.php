<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [

            'user_id' => $this->id,
            'user_unique_id' => $this->unique_id,
            'name' => $this->name,
            'username' => $this->username,
            'picture' => $this->picture,
            
        ];
    }
}
