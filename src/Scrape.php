<?php

namespace App;

require 'vendor/autoload.php';

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\DomCrawler\Crawler;
class Scrape
{
    private array $products = [];

    function to_mb($str){
        $text = str_replace(['-', ' '], '', strtolower($str));
        return substr($text, -2) == 'mb' ? substr($text, 0, strlen($text) - 2) : floatval(substr($text, 0, strlen($text) - 2)) * 1000;
    }

    public function run(): void
    {
        $browser = new HttpBrowser(HttpClient::create());
        $browser->request('GET', 'https://www.magpiehq.com/developer-challenge/smartphones');
        $crawler = new Crawler($browser->getResponse()->getContent());
        $products = $crawler->filter('div.bg-white.p-4.rounded-md')->each(function($node, $i){
            $availability = str_replace('Availability: ', '', $node->filter('div.my-4.text-sm.block.text-center')->text());
            return [
                "title" => $node->filter('.product-name')->text(),
                "price" => floatval((str_replace('£', '', $node->filter('div.my-8.block.text-center.text-lg')->text()))),
                "imageUrl" => 'https://www.magpiehq.com/developer-challenge/images'.str_replace('..', '',$node->filter('img')->attr('src')),
                "CapacityMB" => $this->to_mb($node->filter('.product-capacity')->text()),
                "color" => $node->filter('.rounded-full')->attr('data-colour'),
                "availabilityText" => $availability,
                "isAvailable" => substr(strtolower(str_replace(' ', '', $availability)),0, 2) == 'in' ? true : false,
                "shippingText" => $node->filter('div.my-4.text-sm.block.text-center')->first()->text(),
                "shippingDate" => $node->filter('div.my-4.text-sm.block.text-center')->last()->text(),

            ];
        });
        array_push($this->products, [
            "page_1" => $products
        ]);
        file_put_contents('output.json', json_encode($this->products));
    }
}

$scrape = new Scrape();
$scrape->run();
