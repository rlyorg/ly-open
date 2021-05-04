<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ItemResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if($this->play_at){
            $path = '/ly/audio/'. $this->play_at->format('Y') .'/' . $this->program->alias . '/' . $this->alias . '.mp3';
        }else{
            //TODO /ly/audio/mavbm/mavbm002.mp3
            $path = '/ly/audio/matodo/' . $this->alias . '.mp3';
        }
        return [
            'id' => $this->id,
            'description' => $this->description,
            // 'alias' => $this->alias,
            // 'category' => $this->program->name,
            // 'play_at' => $this->play_at,
            'description' => $this->description,
            'path' => $path,
        ];
    }
}
