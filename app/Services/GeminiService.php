<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeminiService
{
    private $client;
    private $apiKey;
    private $baseUrl = 'https://generativelanguage.googleapis.com/v1beta/models';

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = 'AIzaSyAJXm8GswHsj-QWw5JHv2HXEl1QDNQ2TFo';
    }

    public function convertToDracula($imagePath)
    {
            // Read the image file
//            $imageData = Storage::get($imagePath);

        $fullPath = Storage::disk('public')->path($imagePath);

        $base64Image = base64_encode(file_get_contents($fullPath));

            // Prepare the request payload for image generation
            $payload = [
                'contents' => [
                    [
                        'parts' => [
                            [
                                'text' => 'Strictly maintain the facial features and likeness of the person in the uploaded image. The output should be the same person, with the addition of scary, gothic-style makeup, pale skin, and sharp vampire teeth. The person should be wearing a high-collared, blood-red lined cloak. The portrait should be in the style of a 19th-century oil painting with a horror-themed background like a haunted castle or a foggy graveyard. The output image must have a 9:16 portrait ratio.'
                            ],
                            [
                                'inlineData' => [
                                    'mimeType' => 'image/jpeg',
                                    'data' => $base64Image
                                ]
                            ]
                        ]
                    ]
                ]
            ];

            // Make the API request to the image generation endpoint
            $response = $this->client->post("{$this->baseUrl}/gemini-2.5-flash-image-preview:generateContent?key={$this->apiKey}", [
                'json' => $payload,
                'headers' => [
                    'Content-Type' => 'application/json',
                ]
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            // Check if we got an image response
            if (isset($responseData['candidates'][0]['content']['parts'][1]['inlineData']['data'])) {
                $generatedImageData = $responseData['candidates'][0]['content']['parts'][1]['inlineData']['data'];

                // Save the generated image
                $processedImagePath = 'processed/' . uniqid() . '_dracula.jpg';
                Storage::disk('public')->put($processedImagePath, base64_decode($generatedImageData));

                return [
                    'success' => true,
                    'processed_image_path' => $processedImagePath,
                    'full_response' => $responseData
                ];
            }

            return [
                'success' => false,
                'error' => 'No image generated',
                'response' => $responseData
            ];

    }

    public function generateDraculaImage($imagePath)
    {
        // Use the convertToDracula method for actual AI image generation
        return $this->convertToDracula($imagePath);
    }
}
