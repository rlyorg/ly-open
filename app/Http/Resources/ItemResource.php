<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProgramResource;

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
            $playAt = $this->play_at->format('ymd');
        }else{
            preg_match('/(\D+)(\d+)/', $this->alias, $matchs); //mavbm
            $path = '/ly/audio/'. $matchs[1] .'/' . $this->alias . '.mp3';
            $playAt = $matchs[2];
        }
        return [
            'id' => $this->id,
            'description' => $this->description,
            'alias' => $this->alias,
            'program_id' => $this->program->id,
            // 'category' => $this->program->category->name,
            // 'program' => new ProgramResource($this->program),
            'program_name' => $this->program->name,
            'code' => $this->program->alias,
            'play_at' => $playAt,
            'description' => $this->description,
            'path' => $path,
        ];
    }
}
