<?php

namespace App;

use Symfony\Component\DomCrawler\Crawler;

require 'vendor/autoload.php';

class Scrape
{
    private array $products = [];

    private string $baseUrl = 'https://www.magpiehq.com/developer-challenge';

    private string $productsPage = 'https://www.magpiehq.com/developer-challenge/smartphones';

    public function run(): void
    {
        $document = ScrapeHelper::fetchDocument($this->productsPage);

        $lastPage = 1;
        try {
            $lastPage = (int) $document->filter('#pages > div')->children()->last()->text();
        } catch (\InvalidArgumentException $exception) {
            // don't need any action
            // this should mean that there is only one page
        }

        $this->scrapeProductsFromPage($document);

        if ($lastPage > 1) {
            for ($i = 2; $i <= $lastPage; $i++) {
                $document = ScrapeHelper::fetchDocument($this->productsPage . '?page=' . $i);
                $this->scrapeProductsFromPage($document);
            }
        }

        file_put_contents('output.json', json_encode(array_values($this->products)));
    }

    private function scrapeProductsFromPage(Crawler $document): void
    {
        $products = $document->filter('#products > div')->children('.product');

        foreach ($products as $product) {
            $product = new Crawler($product);

            $colours = $product->filter('.border.border-black.rounded-full.block');


            foreach ($colours as $colour) {
                $colour = new Crawler($colour);

                $productData = $this->getProductData($product);

                $productData['colour'] = $colour->attr('data-colour');

                $uniqueKey = $productData['colour'] . $productData['title'];

                $this->products[$uniqueKey] = $productData;
            }
        }
    }

    private function getProductData(Crawler $product): array
    {
        $imgSrc = $product->filter('img')->first()->attr('src');

        $capacity = $product->filter('h3 > .product-capacity')->text();

        if (str_contains('MB', $capacity)) {
            $capacity = intval($capacity);
        } else {
            $capacity = intval($capacity) * 1000;
        }

        $availabilityAndShipping = $product->filter('.my-4.text-sm.block.text-center');

        $availabilityText = ltrim($availabilityAndShipping->first()->text(), 'Availability: ');

        $shippingText = 'N/A';
        $shippingDate = 'N/A';

        if ($availabilityAndShipping->count() > 1) {
            $shippingText = $availabilityAndShipping->last()->text();

            if ($date = $this->extractDate($shippingText)) {
                $shippingDate = date('Y-m-d', strtotime($date));
            }
        }

        $productData = [
            "title" => $product->filter('h3')->text(),
            "price"=> (float) ltrim($product->filter('.my-8.block.text-center.text-lg')->text(), '£'),
            "imageUrl"=> $this->baseUrl . ltrim($imgSrc, '.'),
            "capacityMB"=> $capacity,
            "colour"=> "",
            "availabilityText"=> $availabilityText,
            "isAvailable"=> str_contains($availabilityText, "In Stock"),
            "shippingText"=> $shippingText,
            "shippingDate"=> $shippingDate,
        ];

        return $productData;
    }

    // extract the first date matching the pattern from a given string
    private function extractDate(string $string): ?string
    {
        $pattern = "/\b(?:\d{4}-\d{2}-\d{2}|\d{1,2} (?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) \d{4}|\d{1,2} (?:Jan|Feb|Mar|Apr|May|Jun|Jul|Aug|Sep|Oct|Nov|Dec) \d{1,2}(?:th|st|nd|rd)? \d{4}|tomorrow)\b/i";
        if (preg_match($pattern, $string, $matches)) {
            return $matches[0];
        } else {
            return null;
        }
    }
}

$scrape = new Scrape();
$scrape->run();
