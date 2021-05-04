<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasSchemalessAttributes;
use Spatie\Activitylog\Traits\LogsActivity;
use Plank\Metable\Metable;

class Category extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];

    use HasFactory;
	use SoftDeletes;
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = [ 'none'];
    protected static $logOnlyDirty = true;

    use Metable;


    public function programs()
    {
        return $this->hasMany(Program::class);
    }
}
