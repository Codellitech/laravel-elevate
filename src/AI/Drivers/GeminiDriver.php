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
            // Industry Standard: Auto-disable SSL on local environments to prevent cURL 60
            $verify = $this->config['verify_ssl'] ?? (app()->environment('local') ? false : true);

            $client = new Client([
                'base_uri' => 'https://generativelanguage.googleapis.com/v1beta/',
                'timeout'  => $this->config['timeout'] ?? 60,
                'verify'   => $verify,
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
            return "Error: Connection failed. If you are on local, ensure ELEVATE_SSL_VERIFY=false. " . $e->getMessage();
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getModel(): string
    {
        return $this->config['model'] ?? 'gemini-1.5-pro';
    }
}
