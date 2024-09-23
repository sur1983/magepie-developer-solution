<?php

namespace App;

class Product
{
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
