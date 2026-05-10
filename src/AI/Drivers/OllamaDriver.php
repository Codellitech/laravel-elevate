<?php

namespace Codellitech\Elevate\AI\Drivers;

use Codellitech\Elevate\Contracts\AIDriver;
use GuzzleHttp\Client;
use Exception;

class OllamaDriver implements AIDriver
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
                'base_uri' => $this->config['host'] ?? 'http://localhost:11434',
                'timeout'  => $this->config['timeout'] ?? 300,
                'verify'   => $this->config['verify_ssl'] ?? true,
            ]);

            $response = $client->post('/api/generate', [
                'json' => [
                    'model' => $this->config['model'] ?? 'deepseek-coder',
                    'prompt' => $prompt,
                    'stream' => false,
                    'options' => [
                        'temperature' => $options['temperature'] ?? 0.2,
                    ]
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['response'] ?? '';
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getModel(): string
    {
        return $this->config['model'] ?? 'deepseek-coder';
    }
}
