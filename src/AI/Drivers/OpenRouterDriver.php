<?php

namespace Codellitech\Elevate\AI\Drivers;

use Codellitech\Elevate\Contracts\AIDriver;
use GuzzleHttp\Client;
use Exception;

class OpenRouterDriver implements AIDriver
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
                'base_uri' => 'https://openrouter.ai/api/v1/',
                'timeout'  => $this->config['timeout'] ?? 60,
                'verify'   => $this->config['verify_ssl'] ?? true,
            ]);

            $response = $client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config['api_key'],
                ],
                'json' => [
                    'model' => $this->config['model'] ?? 'openai/gpt-4-turbo',
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $options['temperature'] ?? 0.2,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['choices'][0]['message']['content'] ?? '';
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getModel(): string
    {
        return $this->config['model'] ?? 'openai/gpt-4-turbo';
    }
}
