<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Program;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

// run once, only on init.
class ProcessAmsItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ly:ams';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process items from AMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $programs = Program::pluck('id','alias')->all();
        Item::chunk(1000, function ($items) use ($programs) {
            foreach ($items as $item) {
                if(Str::startsWith($item->alias, 'ma')) continue; // 不处理lts
                preg_match('/(\D+)(\d+)/', $item->alias, $matches);
                $item->program_id = $programs[$matches[1]]??'1';
                $item->play_at = date_create_from_format('ymd H:i:s', $matches[2] . '00:00:00');
                $item->description = strip_tags($item->description);
                $item->save();
            }
        });
        return 0;
    }
}
