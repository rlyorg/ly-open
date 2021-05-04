<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Program;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;

class UpdateItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ly:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'get new items from https://729lyprog.net/bc';
    
    private $http;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->http = Http::withOptions(['base_uri' => 'https://729lyprog.net']);
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // onclick="Joomla.tableOrdering('title','asc','');
        $data = [
            'limit' => 100,
            'filter_order' => 'title',
            'filter_order_Dir' => 'asc'
        ];
        Program::active()->each(function($program) use($data){
            $code = $program->alias;
            $endPoint = '/' . $code;

            $response = rescue(fn()=>$this->http->asForm()->post($endPoint, $data), null, false);
            if($response){
                $dom = HtmlDomParser::str_get_html($response->body());
                foreach ($dom->find('.ss-title') as $item){
                    $title = $item->find('a', 0)->text();
                    $description = $item->find('p', 0)->text();
                    if(!$description) $description = $title;
                    $href = $item->parent('tr')->find('.ss-dl a', 0)->getAttribute('href');
                    if(!$href) continue;// 第一个为head不要
                    preg_match('/(\d+).mp3/', $href, $matches);

                    // LTS
                    if(in_array($code, ['ltsnp','ltsdp1','ltshdp1','ltsdp2','ltshdp2'])){
                        preg_match('/([a-z]+\d+).mp3/', $href, $matches);
                        $alias = $matches[1]; //mavbm002
                        $playAt = null;
                    }else{
                        $alias = $code . $matches[1]; //cc210503
                        $playAt = date_create_from_format('ymd H:i:s', $matches[1] . '00:00:00');
                    }
                    
                    $item = Item::firstOrCreate(compact('alias'));
                    $item->update([
                        'program_id' => $program->id,
                        'play_at' => $playAt,
                        // 'title' => $title,
                        'description' => $description,
                    ]);
                    Log::debug(__CLASS__,[$item->alias]);
                }
            }
        });
        
        return 0;
    }
}
