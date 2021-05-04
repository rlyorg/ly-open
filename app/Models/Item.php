<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Metable\Metable;
use Spatie\Activitylog\Traits\LogsActivity;

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

}
