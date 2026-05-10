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
        // Absolute safety check: Never run AI logic during discovery or install
        if ($this->isDiscoveryMode()) {
            return;
        }

        // Config Integrity Shield: Prevent array_merge crashes if config is corrupted
        $existing = $this->app['config']->get('elevate');
        if ($existing !== null && !is_array($existing)) {
            $this->app['config']->set('elevate', []);
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/elevate.php', 'elevate');

        $this->app->singleton(AIManager::class, function ($app) {
            return new AIManager($app);
        });

        $this->app->alias(AIManager::class, 'elevate.ai');
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/elevate.php' => config_path('elevate.php'),
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
               str_contains($command, 'composer');
    }
}
