<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use voku\helper\HtmlDomParser;

class ProgramSeeder extends Seeder
{

	private $url = "https://729ly.net";

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $response = Http::get($this->url);
        $dom = HtmlDomParser::str_get_html($response->body());

        $categories = [];
        foreach ($dom->find('.magazine-category') as $magazine){
        	$category = $magazine->find('.magazine-category-title a', 0);
			$categoryTitle = $category->getAttribute('title');
			$categoryUrl = $this->url . $category->getAttribute('href');

			// $response = Http::get($categoryUrl);
			// $dom = HtmlDomParser::str_get_html($response->body());
	        foreach ($dom->find('.magazine-item') as $item){
	        	$program['Url'] = $this->url . $item->find('.magazine-item-media a', 0)->getAttribute('href');
	        	$program['Cover'] = $item->find('.magazine-item-media img', 0)->getAttribute('src');
	        	$program['Title'] = $item->find('.magazine-item-media h2', 0)->text();
	        	$programAuthor = $item->find('.magazine-item-ct p', 0)->text();
	        	$program['Authors'] = explode('、', str_replace('主持：', '', $programAuthor));
	        	// authors
	        	foreach ($dom->find('.magazine-item-ct p a') as $item){
	        		$AuthorUrl = $this->url . $item->getAttribute('href');
	        		$AuthorName = $item->getAttribute('title');
	        	}

	        }

        }
    }
}
