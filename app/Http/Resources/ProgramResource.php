<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProgramResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        // return parent::toArray($request);
        return [
            'id' => $this->id,
            'name' => $this->name,
            'alias' => $this->alias,
            'category' => $this->category->name,
            'announcers' => $this->announcers->pluck('name','id'),
            'begin_at' => $this->begin_at,
            'end_at' => $this->end_at,
            
            'brief' => $this->brief,
            'description' => $this->description,
            'email' => $this->email,
            'sms_keyword' => $this->sms_keyword,
            'phone_open' => $this->phone_open,
        ];
    }
}
