<?php

namespace Codellitech\Elevate\AI\Drivers;

use Codellitech\Elevate\Contracts\AIDriver;
use GuzzleHttp\Client;
use Exception;

class ClaudeDriver implements AIDriver
{
    protected array $config;
    protected Client $client;

    public function __construct(array $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => 'https://api.anthropic.com/v1/',
            'timeout'  => $config['timeout'] ?? 60,
            'verify'   => config('elevate.ai.verify_ssl', true),
        ]);
    }

    public function prompt(string $prompt, array $options = []): string
    {
        try {
            $response = $this->client->post('messages', [
                'headers' => [
                    'x-api-key'         => $this->config['api_key'],
                    'anthropic-version' => '2023-06-01',
                    'content-type'      => 'application/json',
                ],
                'json' => [
                    'model'      => $this->config['model'] ?? 'claude-3-opus-20240229',
                    'max_tokens' => 4096,
                    'messages'   => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => $options['temperature'] ?? 0.2,
                ],
            ]);

            $data = json_decode($response->getBody()->getContents(), true);
            return $data['content'][0]['text'] ?? '';
        } catch (Exception $e) {
            return "Error: " . $e->getMessage();
        }
    }

    public function getModel(): string
    {
        return $this->config['model'] ?? 'claude-3-opus-20240229';
    }
}
