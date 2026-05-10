<?php

namespace Codellitech\Elevate\AI;

use Illuminate\Support\Manager;
use Codellitech\Elevate\AI\Drivers\OpenAIDriver;
use Codellitech\Elevate\AI\Drivers\GeminiDriver;
use Codellitech\Elevate\AI\Drivers\ClaudeDriver;
use Codellitech\Elevate\AI\Drivers\OllamaDriver;
use Codellitech\Elevate\AI\Drivers\OpenRouterDriver;
use InvalidArgumentException;

class AIManager extends Manager
{
    public function engine($driver = null)
    {
        try {
            return $this->driver($driver);
        } catch (InvalidArgumentException $e) {
            $fallback = $this->config->get('elevate.ai.fallback_provider');
            if ($fallback && $fallback !== $driver) {
                return $this->driver($fallback);
            }
            throw $e;
        }
    }

    public function getDefaultDriver()
    {
        return $this->config->get('elevate.ai.default_provider', 'openai');
    }

    protected function createOpenAIDriver()
    {
        $config = $this->config->get('elevate.ai.providers.openai', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new OpenAIDriver($config);
    }

    protected function createGeminiDriver()
    {
        $config = $this->config->get('elevate.ai.providers.gemini', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new GeminiDriver($config);
    }

    protected function createClaudeDriver()
    {
        $config = $this->config->get('elevate.ai.providers.claude', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new ClaudeDriver($config);
    }

    protected function createOllamaDriver()
    {
        $config = $this->config->get('elevate.ai.providers.ollama', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new OllamaDriver($config);
    }

    protected function createOpenRouterDriver()
    {
        $config = $this->config->get('elevate.ai.providers.openrouter', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new OpenRouterDriver($config);
    }

    protected function createDeepSeekDriver()
    {
        $config = $this->config->get('elevate.ai.providers.deepseek', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new OpenAIDriver(array_merge($config, [
            'base_uri' => 'https://api.deepseek.com/v1/'
        ]));
    }

    protected function createGroqDriver()
    {
        $config = $this->config->get('elevate.ai.providers.groq', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new OpenAIDriver(array_merge($config, [
            'base_uri' => 'https://api.groq.com/openai/v1/'
        ]));
    }
}
