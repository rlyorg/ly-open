<?php

namespace App\Console\Commands;

use App\Models\Item;
use App\Models\Program;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;
use Carbon\Carbon;

class SyncItem extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ly:sync {date?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';
    
    private $http;
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
    // get items from https://txly2.net/index.php?option=com_vdata&task=get_feeds&type=vd6usermons42&column=sermon_publish_up&value=2022-02-16
    public function handle()
    {
            // $date = date('Y-m-d');//2022-02-16
            $date = $this->argument('date') ?? date('Y-m-d');

            $url = 'https://txly2.net/index.php?option=com_vdata&task=get_feeds&type=vd6usermons42&column=sermon_publish_up&value='.$date;
            // bookmark_id => program_id
            $seriesMap = [
                "23-23" => "ltsnp", // 良友圣经学院（启航课程）
                "18-18" => "ltsdp1", // 良友圣经学院（圣工学士课程I：本科文凭课程）1
                "19-19" => "ltsdp2", // 良友圣经学院（圣工学士课程I：本科文凭课程）2
                "21-21" => "ltshdp1", // 良友圣经学院（圣工学士课程II：进深文凭课程）1
                "22-22" => "ltshdp2", // 良友圣经学院（圣工学士课程II：进深文凭课程）2
            ];

            $response = rescue(fn()=>Http::get($url), null, false);
            $map = Program::pluck('id','alias');
            Log::error(__CLASS__,[$map]);
            if($response->ok() && $data = $response->json()){
                foreach ($data as $item) {
                    $playAt = Carbon::parse($item['sermon_publish_up']);
                    $seriesId = $item['bookmark_id']; //193 ：23-23
                    // if(Str::startsWith($item['alias'], 'ma')){
                    if($item['tag_id'] == '12') { //LTS
                        // $item['tag_id'] = 12 ;// Object { id: "12", title: "课程训练" }
                        preg_match('/([a-z]+\d+).mp3/', $item['url'], $matches);
                        $alias = $matches[1]; //mavbm002
                        $programCode = $seriesMap[$seriesId]; // ltsnp

                    }else{
                        $programCode = $item['series_alias']; //cc
                        $alias = $item['series_alias'] . $playAt->format('ymd') ;//cc220217
                    }
                    // $map[$alias] = $seriesId; // hp => 489
                    $programId = $map[$programCode];
                    $pItem = Item::firstOrCreate(compact('alias'));
                    $updateData = [
                        'program_id' => $programId,
                        'play_at' => $playAt,
                        'description' => strip_tags($item['sermon_notes']),
                    ];
                    $pItem->update($updateData);
                }
            }
        
        return 0;
    }
}
