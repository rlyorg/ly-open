<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Category;

class SyncCategory extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:category';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $url = '';
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->url = 'https://txly2.net/index.php?option=com_vdata&task=get_feeds&type=vd6tags42';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $response = Http::get($this->url);
        if($response->successful()){
            $data = $response->json();
            foreach ($data as $item) {
                Category::where('lycid', $item['id'])->update(['name' => $item['title']]);
            }
        }
        return 0;
    }
}
