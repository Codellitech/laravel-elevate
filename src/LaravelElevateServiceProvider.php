<?php

namespace Codellitech\Elevate;

use Illuminate\Support\ServiceProvider;
use Codellitech\Elevate\Commands\ElevateCommand;
use Codellitech\Elevate\Commands\IntegrateCommand;
use Codellitech\Elevate\Commands\RollbackCommand;
use Codellitech\Elevate\AI\AIManager;

class LaravelElevateServiceProvider extends ServiceProvider
{
    public function register()
    {
        // 1. Determine if we are in discovery mode
        if ($this->isDiscoveryMode()) {
            return;
        }

        // 2. Load the new, hardened config file (renamed to break caches)
        $this->mergeConfigFrom(__DIR__ . '/../config/elevate-engine.php', 'elevate');

        // 3. Register the AI Manager as a singleton
        $this->app->singleton(AIManager::class, function ($app) {
            return new AIManager($app);
        });

        $this->app->alias(AIManager::class, 'elevate.ai');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/elevate-engine.php' => config_path('elevate-engine.php'),
            ], 'elevate-config');

            $this->commands([
                ElevateCommand::class,
                IntegrateCommand::class,
                RollbackCommand::class,
            ]);
        }
    }

    protected function isDiscoveryMode(): bool
    {
        $argv = $_SERVER['argv'] ?? [];
        $command = implode(' ', $argv);
        
        return str_contains($command, 'package:discover') || 
               str_contains($command, 'vendor:publish') || 
               str_contains($command, 'composer') ||
               str_contains($command, 'config:cache');
    }
}
