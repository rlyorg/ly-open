<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\Category;
use App\Models\Program;

class SyncProgram extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:program';

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
        $this->url = 'https://txly2.net/index.php?option=com_vdata&task=get_feeds&type=vd6useries42&column=tag_id&value=';
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        Category::each(function($category){
            $cid = $category->lycid;
            $response = Http::get($this->url . $cid);
            if($response->successful()){
                $data = $response->json();
                foreach ($data as $item) {
                    $program = Program::where('alias', $item['alias'])->first();
                    $description = str_replace('\r\n&gt;&gt;&gt; 节目资源', '', strip_tags($item['series_description']));
                    if(!$program){
                        Program::create([
                            'name' =>  $item['title'],
                            'alias' =>  $item['alias'],
                            'brief' =>  $item['series_brief'],
                            'description' => $description,
                            'email' =>  $item['emailto'],
                            'sms_keyword' =>  $item['smsto'],
                            'phone_open' =>  $item['phoneto'],
                            'category_id' =>  $category->id,
                            'begin_at' =>  now(),
                        ]);
                    }else{
                        $program->update([
                            'name' =>  $item['title'],
                            'brief' =>  $item['series_brief'],
                            'description' => $description,
                            'email' =>  $item['emailto'],
                            'sms_keyword' =>  $item['smsto'],
                            'phone_open' =>  $item['phoneto'],
                            'category_id' =>  $category->id,
                        ]); //同步更新
                    }
                }
            }
        });
        return 0;
    }
}
