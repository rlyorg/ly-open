<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\ProgramResource;
// use Illuminate\Support\Str;

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
        if(!in_array($this->program->alias, ['ltsnp', 'ltsdp1', 'ltsdp2', 'ltshdp1', 'ltshdp2'])){
        // if(!Str::startsWith($this->alias, 'ma')){
            $path = '/ly/audio/'. $this->play_at->format('Y') .'/' . $this->program->alias . '/' . $this->alias . '.mp3';
            $playAt = $this->play_at->format('ymd');
        }else{
            preg_match('/(\D+)(\d+)/', $this->alias, $matchs); //mavbm
            $path = '/ly/audio/'. $matchs[1] .'/' . $this->alias . '.mp3';
            if(!$this->play_at){
                $playAt = $matchs[2];
            }else{
                $playAt = $this->play_at->format('ymd');
            }
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
            //TODO remove path later
            'link' => config('app.url'). $path,
        ];
    }
}
