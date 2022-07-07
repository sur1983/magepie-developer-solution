<?php

namespace App;

use Symfony\Component\BrowserKit\HttpBrowser;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpClient\HttpClient;

class ScrapeHelper
{
    public static function fetchDocument(string $url): Crawler{
        $browser = new HttpBrowser(HttpClient::create());
        $browser->request('GET', $url);
        return new Crawler($browser->getResponse()->getContent());
    }
    public static function to_mb($str){
        $text = str_replace(['-', ' '], '', strtolower($str));
        return substr($text, -2) == 'mb' ? substr($text, 0, strlen($text) - 2) : floatval(substr($text, 0, strlen($text) - 2)) * 1000;
    }

    public static function color_variants($product_node){
        
        return $product_node->filter('div.flex-wrap.justify-center.-mx-2 .px-2')->each(function($node) use ($product_node){
            // global $availability;
            // global ;
            $date_string = trim(str_replace([
                'Available', 'Delivery', 'on', 'from', 'by', 
                "Free Shipping", "Availability: Out of Stock",
                "Free Delivery ", "Delivers ", "Unavailable for delivery"
            ], '', $product_node->filter('div.my-4.text-sm.block.text-center')->last()->text()));
            return [
                "title" => $product_node->filter('.product-name')->text(),
                "price" => floatval((str_replace('£', '', $product_node->filter('div.my-8.block.text-center.text-lg')->text()))),
                "imageUrl" => 'https://www.magpiehq.com/developer-challenge/images'.str_replace('..', '',$product_node->filter('img')->attr('src')),
                "CapacityMB" => ScrapeHelper::to_mb($product_node->filter('.product-capacity')->text()),
                "color" => $node->filter('.rounded-full')->attr('data-colour'),
                "availabilityText" => str_replace('Availability: ', '', $product_node->filter('div.my-4.text-sm.block.text-center')->text()),
                "isAvailable" => substr(strtolower(str_replace(' ', '', str_replace('Availability: ', '', $product_node->filter('div.my-4.text-sm.block.text-center')->text()))),0, 2) == 'in' ? true : false,
                "shippingText" => $product_node->filter('div.my-4.text-sm.block.text-center')->first()->text(),
                "shippingDate" => strlen(strtotime($date_string)) > 0 ? date('Y-m-d', strtotime($date_string)) : $date_string,
    
            ];
        });
    }

    public static function extract_page_products($html_data): array{
        return $html_data->filter('div.bg-white.p-4.rounded-md')->each(function($node, $i){
            return ScrapeHelper::color_variants($node);
        });
    }
}
