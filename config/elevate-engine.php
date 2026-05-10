<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Elevate Core Settings
    |--------------------------------------------------------------------------
    |
    | These settings control the core behavior of the modernization engine.
    | We keep these simple and safe to prevent discovery-time crashes.
    |
    */

    'ai_provider' => env('ELEVATE_AI_PROVIDER', 'openai'),

    'paths' => [
        'app',
        'config',
        'database',
        'resources',
        'routes',
    ],

    'exclude' => [
        'vendor',
        'node_modules',
        'storage',
        'bootstrap/cache',
    ],
];
