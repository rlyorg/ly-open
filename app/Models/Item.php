<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Metable\Metable;
use Spatie\Activitylog\Traits\LogsActivity;
use Laravel\Scout\Searchable;

class Item extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'play_at'];
	
    use HasFactory;
	use SoftDeletes;
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = [ 'none'];
    protected static $logOnlyDirty = true;

    use Metable;

    public function program(){
        return $this->belongsTo(Program::class);
    }

    use Searchable;
    // public function toSearchableArray()
    // {
    //     $array = $this->toArray();
    
    //     // $array = $this->transform($array);
    
    //     // $array['id'] = $this->id;
    //     // $array['alias'] = $this->alias;
    //     $array['program'] = $this->program->name;
    //     $array['date'] = $this->getDate();
    //     unset($array['program_id']);
    //     unset($array['created_at']);
    //     unset($array['updated_at']);
    //     unset($array['deleted_at']);
    //     // $array['description'] = "【{$this->program->name}-{$this->getDate()}】{$this->description} ";
    
    //     return $array;
    // }
    // https://laravel.com/docs/8.x/scout#modifying-the-import-query
    protected function makeAllSearchableUsing($query)
    {
        return $query->with('program');
    }
    
    // https://laravel.com/docs/8.x/scout#soft-deleting
    // public function shouldBeSearchable()
    // {
    //     return is_null($this->deleted_at);
    // }

    public function getDate()
    {
        if($this->play_at){
            $playAt = $this->play_at->format('ymd');
        }else{
            preg_match('/(\D+)(\d+)/', $this->alias, $matchs); //mavbm
            $playAt = $matchs[2];
        }
        return $playAt;
    }
}
