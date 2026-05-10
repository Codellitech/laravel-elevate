<?php

namespace Codellitech\Elevate\AI\Drivers;

use Codellitech\Elevate\Contracts\AIDriver;
use GuzzleHttp\Client;
use Exception;

class GeminiDriver implements AIDriver
{
    protected array $config;
    protected Client $client;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
            'timeout'  => $config['timeout'] ?? 60,
            'verify'   => config('elevate.ai.verify_ssl', true),
        ]);
    }

    public function prompt(string $prompt, array $options = []): string
    {
        try {
            $apiKey = $this->config['api_key'];
            $model = $this->config['model'] ?? 'gemini-1.5-pro';
            
            $response = $this->client->post("models/{$model}:generateContent?key={$apiKey}", [
                'json' => [
                    'contents' => [
                        [
                            'parts' => [
                                ['text' => $prompt]
                            ]
                        ]
                    ],
                    'generationConfig' => [
                        'temperature' => $options['temperature'] ?? 0.2,
                    ]
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['candidates'][0]['content']['parts'][0]['text'] ?? '';
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getModel(): string
    {
        return $this->config['model'] ?? 'gemini-1.5-pro';
    }
}
