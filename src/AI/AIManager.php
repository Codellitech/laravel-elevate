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
            $fallback = env('ELEVATE_FALLBACK_PROVIDER', 'claude');
            if ($fallback && $fallback !== $driver) {
                return $this->driver($fallback);
            }
            throw $e;
        }
    }

    public function getDefaultDriver()
    {
        return env('ELEVATE_AI_PROVIDER', 'openai');
    }

    protected function createOpenAIDriver()
    {
        return new OpenAIDriver([
            'api_key' => env('OPENAI_API_KEY'),
            'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
            'timeout' => 60,
            'verify_ssl' => env('ELEVATE_SSL_VERIFY', !app()->environment('local')),
        ]);
    }

    protected function createGeminiDriver()
    {
        return new GeminiDriver([
            'api_key' => env('GEMINI_API_KEY'),
            'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
            'timeout' => 60,
            'verify_ssl' => env('ELEVATE_SSL_VERIFY', !app()->environment('local')),
        ]);
    }

    protected function createClaudeDriver()
    {
        return new ClaudeDriver([
            'api_key' => env('ANTHROPIC_API_KEY'),
            'model' => env('ANTHROPIC_MODEL', 'claude-3-opus-20240229'),
            'timeout' => 60,
            'verify_ssl' => env('ELEVATE_SSL_VERIFY', !app()->environment('local')),
        ]);
    }

    protected function createOllamaDriver()
    {
        return new OllamaDriver([
            'host' => env('OLLAMA_HOST', 'http://localhost:11434'),
            'model' => env('OLLAMA_MODEL', 'deepseek-coder'),
            'timeout' => 300,
            'verify_ssl' => env('ELEVATE_SSL_VERIFY', !app()->environment('local')),
        ]);
    }

    protected function createOpenRouterDriver()
    {
        return new OpenRouterDriver([
            'api_key' => env('OPENROUTER_API_KEY'),
            'model' => env('OPENROUTER_MODEL', 'openai/gpt-4-turbo'),
            'timeout' => 60,
            'verify_ssl' => env('ELEVATE_SSL_VERIFY', !app()->environment('local')),
        ]);
    }
}
