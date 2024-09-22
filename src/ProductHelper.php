<?php

namespace App;

use GuzzleHttp\Client;
use Symfony\Component\Debug\Debug;
use GuzzleHttp\Exception\GuzzleException;
use Symfony\Component\DomCrawler\Crawler;
use GuzzleHttp\Exception\RequestException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ProductHelper
{
    /**
     * @param string $url
     * 
     * @return array|null
     */
    public static function fetchDocument(string $url) : ?array
    {
        Debug::enable(E_RECOVERABLE_ERROR & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED, false);

        $response = static::childDomFilter($url);

        $data = json_decode($response->getContent(), true); // Decodes JSON to an associative array

        if('200' == isset($data['status'])) {

            $data1 = static::parseProductData($data['data']);
        } else {

            $data1 = $data;

        }

        return $data1;

    }


    /**
     * @param string $url
     * 
     * @return JsonResponse
     */
    public static function childDomFilter(string $url) : JsonResponse
    {

        try {

            $products = [];

            $client = new Client;

            $response = $client->get($url);
            $statusCode = $response->getStatusCode();

            $docs = new Crawler($response->getBody()->getContents(), $url);

            $product = $docs->filter('#products');

            $products['title'] =  $product->filter('.product-name')->each(fn (Crawler $node, $i) =>  $node->text());
            $products['price'] =  $product->filter('.text-lg')->each(fn (Crawler $node, $i) =>  $node->text());
            $products['image'] =  $product->filter('img')->each(function (Crawler $node, $i) {
                $imageurl = $node->eq(0)->attr('src');
                return str_replace('..', 'https://www.magpiehq.com/developer-challenge', $imageurl);
            });

            $products['capacityMB'] =  $product->filter('.product-capacity')->each(function (Crawler $node, $i) {

                if(str_contains($node->text(), 'GB')) {
                    return  $capacity = substr(preg_replace('/\s+/', '', $node->text()), 0, -2) * 1000;
                } else {
                    return $capacity = substr(preg_replace('/\s+/', '', $node->text()), 0, -2);
                }

            });

            $products['colour'] =  $product->filter('.-mx-2')->each(function (Crawler $node, $i) {
                $colour = array();
                return $node->filter('span')->each(function (Crawler $node1, $i) {
                    $colour = $node1->attr('data-colour');
                    return $colour;
                });


            });
            $products['availabilityText'] =  $product->filter('.text-lg')->each(function (Crawler $node, $i) {
                $availabilitytext = str_replace('Availability:', '', $node->nextAll()->text());

                return trim($availabilitytext);

            });

            $products['isAvailable'] =  $product->filter('.text-lg')->each(function (Crawler $node, $i) {
                $availabilitytext = str_replace('Availability:', '', $node->nextAll()->text());
                if('In Stock' === trim($availabilitytext)) {
                    return 'true';
                } else {
                    return 'false';
                }

            });

            $products['shippingText'] =  $product->filter('.text-lg')->each(function (Crawler $node, $i) {
                $shippingText = $node->nextAll()->nextAll()->getNode(0)->nodeValue;

                return trim($shippingText);

            });


            $products['shippingDate'] =  $product->filter('.text-lg')->each(function (Crawler $node, $i) {
                $shippingText = $node->nextAll()->nextAll()->getNode(0)->nodeValue;

                if(!empty($shippingText)) {
                    preg_match_all('/(\b\d{1,2}\D{0,3})?\b(?:Jan(?:uary)?|Feb(?:ruary)?|Mar(?:ch)?|Apr(?:il)?|May|Jun(?:e)?|Jul(?:y)?|Aug(?:ust)?|Sep(?:tember)?|Oct(?:ober)?|(Nov|Dec)(?:ember)?)\D?(\d{1,2}\D?)?\D?((19[7-9]\d|20\d{2})|\d{2})/', $shippingText, $matches);
                    if(!empty($matches[0][0])) {
                        return date('Y-m-d', strtotime($matches[0][0]));
                    } else {

                        preg_match_all('/\d{4}-\d{2}-\d{2}/', $shippingText, $matches);
                        return $matches[0][0];
                    }

                }


            });

            return new JsonResponse([
                           'status' => $statusCode,
                           'data' => $products,
                       ], $statusCode);

        } catch (RequestException $e) {
            // Handle specific Guzzle request exceptions
            return new JsonResponse([
                'error' => 'Request failed',
                'message' => $e->getMessage(),
                'url' => $url,
            ], $e->getResponse() ? $e->getResponse()->getStatusCode() : 500);
        } catch (GuzzleException $e) {
            // Handle other Guzzle exceptions
            return new JsonResponse([
                'error' => 'Guzzle error occurred',
                'message' => $e->getMessage(),
            ], 500);
        } catch (\Exception $e) {
            // Handle any other exceptions
            return new JsonResponse([
                'error' => 'An unexpected error occurred',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @param mixed $products
     * 
     * @return array|null
     */
    public static function parseProductData($products) : ?array
    {


        $productData = [];

        try {

            foreach ($products as $product) {

                for($i = 0;$i < count($product);$i++) {

                    $productData[$i] = [
                      'title' => $products['title'][$i],
                      'price' => $products['price'][$i],
                      'image' => $products['image'][$i],
                      'capacityMB' => $products['capacityMB'][$i],
                      'colour' => $products['colour'][$i],
                      'availabilityText' => $products['availabilityText'][$i],
                      'isAvailable' => $products['isAvailable'][$i],
                      'shippingText' => $products['shippingText'][$i],
                      'shippingDate' => $products['shippingDate'][$i]

                    ];
                }
            }

            return $productData;

        } catch (Exception $e) {
            echo 'Caught exception: ',  $e->getMessage(), "\n";
        }
    }
}
