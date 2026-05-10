<?php

namespace Codellitech\Elevate\AI\Drivers;

use Codellitech\Elevate\Contracts\AIDriver;
use GuzzleHttp\Client;
use Exception;

class GeminiDriver implements AIDriver
{
    protected array $config;

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function prompt(string $prompt, array $options = []): string
    {
        try {
            $client = new Client([
                'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
                'timeout'  => $this->config['timeout'] ?? 60,
                'verify'   => $this->config['verify_ssl'] ?? true,
            ]);

            $apiKey = $this->config['api_key'];
            $model = $this->config['model'] ?? 'gemini-1.5-pro';
            
            $response = $client->post("models/{$model}:generateContent?key={$apiKey}", [
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
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            if (str_contains($e->getMessage(), 'SSL certificate problem')) {
                return "Error: SSL Certificate problem. Please set ELEVATE_SSL_VERIFY=false in your .env";
            }
            return "Error: Connection failed. " . $e->getMessage();
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getModel(): string
    {
        return $this->config['model'] ?? 'gemini-1.5-pro';
    }
}
