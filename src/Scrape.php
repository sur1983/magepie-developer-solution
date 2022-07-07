<?php

namespace App;

require 'vendor/autoload.php';

class Scrape
{
    private array $products = [];
    public function run(): void
    {
        $initial_page = ScrapeHelper::fetchDocument('https://www.magpiehq.com/developer-challenge/smartphones');
        $page1 = ScrapeHelper::extract_page_products($initial_page);
        $initial_page->filter('#pages div a')->each(function($link, $i){
            $url = 'https://www.magpiehq.com/developer-challenge'.str_replace('..','',$link->attr('href'));
            if($i !== 0){
                $page_data = ScrapeHelper::fetchDocument($url);
                $this->products = array_merge($this->products, ScrapeHelper::extract_page_products($page_data));
            }
        });
        $this->products = array_merge($this->products, $page1);
        $merged_variants = [];
        foreach ($this->products as $dataset) {
            foreach ($dataset as $data) {
                $merged_variants[] = $data;
            }
        };
        $data = array_unique($merged_variants, SORT_REGULAR);
        file_put_contents('output.json', str_replace('\\', '', json_encode($data)));
    }
}

$scrape = new Scrape();
$scrape->run();
