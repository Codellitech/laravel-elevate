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
}
