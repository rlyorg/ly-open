<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use App\Models\Announcer;
use App\Models\Program;
use App\Models\Category;
use Illuminate\Support\Facades\Log;
use voku\helper\HtmlDomParser;

// run once, only on init.
// TODO: 同步下线
class InitMeta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ly:init';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Init metadata from 729ly.net';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    private $url = "https://729ly.net";
    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $response = Http::get($this->url);
        $dom = HtmlDomParser::str_get_html($response->body());

        foreach ($dom->find('.magazine-category') as $magazine){
            $categoryDom = $magazine->find('.magazine-category-title a', 0);
            $categoryTitle = $categoryDom->getAttribute('title');
            // $categoryUrl = $this->url . $categoryDom->getAttribute('href');
            // $categories[] = $category;
            $categoryModel = Category::updateOrCreate(['name' => $categoryTitle]);
            // dd($categoryModel);
            foreach ($magazine->find('.magazine-item') as $item){
                $program = [];
                $program['name'] = $item->find('.page-header h2', 0)->text();

                
                // https://729ly.net/program/program-lifestyle-wisdom/program-hp
                $url = $this->url . $item->find('.magazine-item-media a', 0)->getAttribute('href');
                $tmpArray = explode('-', $url);
                $code = array_pop($tmpArray);

                // cover images
                // https://729lyprog.net/images/program_banners/bc_prog_banner.png
                // https://cdn.ly.yongbuzhixi.com/images/programs/hp_prog_banner_sq.jpg
                // $program['descripton'] = $item->find('.magazine-item-media img', 0)->getAttribute('data-src');
                
                $programAuthor = trim($item->find('.magazine-item-ct p', 0)->text());
                $programAuthor = str_replace('主持：', '', $programAuthor);
                $programAuthor = str_replace('；嘉宾：', '、', $programAuthor);
                $programAuthor = str_replace('；嘉宾讲员：', '、', $programAuthor);
                $programAuthor = str_replace(" ", '', $programAuthor);

                // ltsdp ltshdp
                $program['alias'] = $code;
                if(Str::endsWith($program['alias'], 'dp')){
                    $program['alias'] = $code . '1';
                    $this->save($program, $programAuthor, $categoryModel);
                    $program['alias'] = $code . '2';
                }

                $this->save($program, $programAuthor, $categoryModel);

                // More about authors URL: 
                foreach ($item->find('.magazine-item-ct p a') as $item){
                    $name = trim($item->getAttribute('title'));
                    $descripton = $this->url . $item->getAttribute('href');
                    Announcer::where("name" , $name)->update(["descripton" => $descripton]);
                }
            }
        }

        // $p = Program::with(['category', 'announcers'])->find('1');

        // return $p->toArray(); // $categories,$authors,

        return 0;
    }
    private function save($program, $programAuthor, $categoryModel)
    {
        
        $programModel = Program::withoutGlobalScopes()->firstOrCreate(['alias'=>$program['alias']], $program);
        if($programModel->wasRecentlyCreated){
            Log::info(__METHOD__, ["wasRecentlyCreated", $program]);
        }
        $programAuthors = explode('、', $programAuthor);

        $announcerModelIds = [];
        foreach ($programAuthors as $name){
            $announcerModel = false;
            if($name) $announcerModel = Announcer::firstOrCreate(["name" => $name]);
            // Announcer has programs <==> role has permissions
            if($announcerModel) $announcerModelIds[] = $announcerModel->id;
        }
        $programModel->announcers()->sync($announcerModelIds);
        // 更新 programModel 的 category_id
        $categoryModel->programs()->save($programModel);
    }
}
