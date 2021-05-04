<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Metable\Metable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Announcer extends Model
{
    protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    
    use HasFactory;
	use SoftDeletes;
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = [ 'none'];
    protected static $logOnlyDirty = true;

    use Metable;


    public function programs(): BelongsToMany
    {
        return $this->belongsToMany(Programs::class, 'announcer_has_programs'); //, 'announcer_id', 'program_id'
    }

}

