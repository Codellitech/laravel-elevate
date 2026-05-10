<?php

namespace Codellitech\Elevate\AI\Drivers;

use Codellitech\Elevate\Contracts\AIDriver;
use GuzzleHttp\Client;
use Exception;

class OpenAIDriver implements AIDriver
{
    protected array $config;
    protected Client $client;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $config['base_uri'] ?? 'https://api.openai.com/v1/',
            'timeout'  => $config['timeout'] ?? 60,
            'verify'   => $config['verify_ssl'] ?? true,
        ]);
    }

    public function prompt(string $prompt, array $options = []): string
    {
        try {
            $response = $this->client->post('chat/completions', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->config['api_key'],
                    'Content-Type'  => 'application/json',
                ],
                'json' => [
                    'model' => $this->config['model'] ?? 'gpt-4-turbo',
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
        return $this->config['model'] ?? 'gpt-4-turbo';
    }
}
