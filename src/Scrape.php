<?php

namespace App;

use Symfony\Component\HttpFoundation\JsonResponse;

require __DIR__ . '/../vendor/autoload.php';

class Scrape
{
    /**
     * @param mixed $documentData
     * 
     * @return JsonResponse
     */
    public function writeOutputJson($documentData) : JsonResponse
    {

        $filePath = 'output.json';

        try {
            $result = file_put_contents($filePath, json_encode($documentData));

            // Check if writing to the file was successful
            if (false === $result) {
                throw new \RuntimeException('Failed to write to the file.');
            }

            return new JsonResponse(['message' => 'File written successfully.'], 200);
        } catch (\Exception $e) {
            // Handle the exception and return a JSON response
            return new JsonResponse([
                'error' => 'An error occurred while writing to the file.',
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * @return void
     */
    public function execute() : void
    {
        $pages = 3;

        for($i = 1;$i <= $pages;$i++) {

            $document[$i] = ScrapeHelper::fetchDocument('https://www.magpiehq.com/developer-challenge/smartphones/?page=' . $i);
        }

        $response = $this->writeOutputJson($document);

        $data = json_decode($response->getContent(), true);

        echo $data['message'];

    }
}
$scrape = new Scrape();
$scrape->execute();
