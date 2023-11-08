<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use App\Models\\Category;
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

                $newLyMetas = [
                    [
                        'code'=>'pm',
                        'title'=>'天路男行客',
                        'brief' => '男人的信仰园地',
                    ],
                    [
                        'code'=>'sz',
                        'title'=>'肋骨咏叹调',
                        'brief' => '女性成长，心意更新',
                    ],
                    [
                        'code'=>'tv',
                        'title'=>'真爱世界',
                        'brief' => '更好的过单身生活',
                    ],
                    [
                        'code'=>'ym',
                        'title'=>'颜明放羊班',
                        'brief' => '快乐中与神相交',
                    ],
                    [
                        'code'=>'fd',
                        'title'=>'泛桌茶经班',
                        'brief' => '以查代茶，以经解经',
                    ],
                    [
                        'code'=>'hr',
                        'title'=>'相约香草山',
                        'brief' => '认识真理，更爱基督',
                    ],
                    [
                        'code'=>'cfbwh',
                        'title'=>'好想健康',
                        'brief' => '病患与家属心声',
                    ],
                    [
                        'code'=>'cedna',
                        'title'=>'动漫查经员',
                        'brief' => '从动漫看人生',
                    ],
                    [
                        'code'=>'cfcbp',
                        'title'=>'美丽见证‧生命大画家',
                        'brief' => '上帝奇妙作为',
                    ],
                    [
                        'code'=>'cfbls',
                        'title'=>'抬头望四季',
                        'brief' => '大自然与地域建筑',
                    ],
                    [
                        'code'=>'cfbsg',
                        'title'=>'Song Song声3',
                        'brief' => '旋律背后的故事',
                    ],
                    [
                        'code'=>'cgaal',
                        'title'=>'爱情几岁',
                        'brief' => '爱要成长',
                    ],
                ];
                foreach ($newLyMetas as $lyMeta) {
                    $program = Program::where('alias', $lyMeta['code'])->first();
                    $program->update(['brief'=>$lyMeta['brief']]);
                }
            }
        });
        return 0;
    }
}
