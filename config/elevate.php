<?php

return [

    /*
    |--------------------------------------------------------------------------
    | AI Settings
    |--------------------------------------------------------------------------
    |
    | Configure the AI providers used for code analysis and modernization.
    |
    */

    'ai' => [
        'default_provider' => env('ELEVATE_AI_PROVIDER', 'openai'),
        'fallback_provider' => env('ELEVATE_FALLBACK_PROVIDER', 'claude'),

        'providers' => [
            'openai' => [
                'api_key' => env('OPENAI_API_KEY'),
                'model' => env('OPENAI_MODEL', 'gpt-4-turbo'),
                'timeout' => 60,
                'retry' => 3,
            ],

            'claude' => [
                'api_key' => env('ANTHROPIC_API_KEY'),
                'model' => env('ANTHROPIC_MODEL', 'claude-3-opus-20240229'),
                'timeout' => 60,
                'retry' => 3,
            ],

            'gemini' => [
                'api_key' => env('GEMINI_API_KEY'),
                'model' => env('GEMINI_MODEL', 'gemini-1.5-pro'),
                'timeout' => 60,
                'retry' => 3,
            ],

            'ollama' => [
                'host' => env('OLLAMA_HOST', 'http://localhost:11434'),
                'model' => env('OLLAMA_MODEL', 'deepseek-coder'),
                'timeout' => 300,
            ],

            'openrouter' => [
                'api_key' => env('OPENROUTER_API_KEY'),
                'model' => env('OPENROUTER_MODEL', 'openai/gpt-4-turbo'),
            ],

            'deepseek' => [
                'api_key' => env('DEEPSEEK_API_KEY'),
                'model' => 'deepseek-chat',
            ],

            'groq' => [
                'api_key' => env('GROQ_API_KEY'),
                'model' => 'llama3-70b-8192',
            ],
        ],

        'temperature' => 0.2,
        'token_limit' => 4096,
    ],

    /*
    |--------------------------------------------------------------------------
    | Modernization Paths
    |--------------------------------------------------------------------------
    |
    | Define where the modernization engine should look for code.
    |
    */

    'paths' => [
        'app' => app_path(),
        'config' => config_path(),
        'database' => database_path(),
        'resources' => resource_path(),
        'routes' => base_path('routes'),
        'tests' => base_path('tests'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Excluded Paths
    |--------------------------------------------------------------------------
    |
    | Paths to be excluded from scanning and modification.
    |
    */

    'exclude' => [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
        'public',
    ],

    /*
    |--------------------------------------------------------------------------
    | Integrations
    |--------------------------------------------------------------------------
    |
    | Settings for automatic integrations.
    |
    */

    'integrations' => [
        'whatsapp_otp' => [
            'provider' => env('WHATSAPP_PROVIDER', 'twilio'),
            'table_name' => 'whatsapp_otps',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Safety & Rollback
    |--------------------------------------------------------------------------
    |
    | Configuration for backups and safety checks.
    |
    */

    'safety' => [
        'git_snapshot' => true,
        'dry_run' => false,
        'backup_path' => storage_path('elevate/backups'),
        'history_path' => storage_path('elevate/history'),
    ],

];
