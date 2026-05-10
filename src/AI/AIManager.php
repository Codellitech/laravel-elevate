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
    public function getDefaultDriver()
    {
        return $this->config->get('elevate.ai.default_provider', 'openai');
    }

    public function createOpenAIDriver()
    {
        $config = $this->config->get('elevate.ai.providers.openai', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new OpenAIDriver($config);
    }

    public function createGeminiDriver()
    {
        $config = $this->config->get('elevate.ai.providers.gemini', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new GeminiDriver($config);
    }

    public function createClaudeDriver()
    {
        $config = $this->config->get('elevate.ai.providers.claude', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new ClaudeDriver($config);
    }

    public function createOllamaDriver()
    {
        $config = $this->config->get('elevate.ai.providers.ollama', []);
        $config['verify_ssl'] = $this->config->get('elevate.ai.verify_ssl', true);
        return new OllamaDriver($config);
    }

    public function createOpenRouterDriver()
    {
        $config = $this->config->get('elevate.ai.providers.openrouter');
        return new OpenRouterDriver($config);
    }

    public function createDeepSeekDriver()
    {
        $config = $this->config->get('elevate.ai.providers.deepseek', []);
        return new OpenAIDriver(array_merge($config, [
            'base_uri' => 'https://api.deepseek.com/v1/'
        ]));
    }

    public function createGroqDriver()
    {
        $config = $this->config->get('elevate.ai.providers.groq', []);
        return new OpenAIDriver(array_merge($config, [
            'base_uri' => 'https://api.groq.com/openai/v1/'
        ]));
    }

    public function createMistralDriver()
    {
        $config = $this->config->get('elevate.ai.providers.mistral', []);
        return new OpenAIDriver(array_merge($config, [
            'base_uri' => 'https://api.mistral.ai/v1/'
        ]));
    }

    public function createCohereDriver()
    {
        $config = $this->config->get('elevate.ai.providers.cohere', []);
        return new OpenAIDriver(array_merge($config, [
            'base_uri' => 'https://api.cohere.ai/v1/'
        ]));
    }

    /**
     * Get a driver with fallback logic.
     *
     * @param string|null $driver
     * @return \Codellitech\Elevate\Contracts\AIDriver
     */
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
}
