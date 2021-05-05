<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Plank\Metable\Metable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Builder;

class Program extends Model
{
	protected $guarded = ['id', 'created_at', 'updated_at', 'deleted_at'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'begin_at', 'end_at'];

    use HasFactory;
	use SoftDeletes;
    use LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logAttributesToIgnore = [ 'none'];
    protected static $logOnlyDirty = true;

    use Metable;


    public function announcers()
    {
        return $this->belongsToMany(Announcer::class, 'announcer_has_programs');// , 'program_id', 'announcer_id'
    }


    public function scopeActive($query)
    {
        return $query->whereNull('end_at');
    }

    protected static function booted()
    {
        static::addGlobalScope('online', function (Builder $builder) {
            $builder->whereNotIn('alias', ['bsm','kbk','ugn','lisu','mgg']);
        });
    }


    // $p->category->name;
    public function category(){
    	return $this->belongsTo(Category::class);
    }

    // /images/programs/bc_prog_banner.jpg
    // /images/program_banners/bc_prog_banner.png
    
    public function getAvatarAttribute(){
        // https://729lyprog.net
        return "/images/programs/bc_prog_banner.png";
    }

    public function getCoverAttribute(){
        //  https://cdn.ly.yongbuzhixi.com
    	return "/images/program_banners/bc_prog_banner.png";
    }      
}
