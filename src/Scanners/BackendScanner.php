<?php

namespace Codellitech\Elevate\Scanners;

use Illuminate\Support\Facades\File;

class BackendScanner
{
    public function analyze(): array
    {
        return [
            'laravel_version' => $this->getLaravelVersion(),
            'php_version' => PHP_VERSION,
            'composer_dependencies' => $this->getComposerDependencies(),
            'structure' => $this->analyzeStructure(),
        ];
    }

    protected function getLaravelVersion(): string
    {
        return app()->version();
    }

    protected function getComposerDependencies(): array
    {
        $path = base_path('composer.json');
        if (!File::exists($path)) {
            return [];
        }

        $composer = json_decode(File::get($path), true);
        return array_merge($composer['require'] ?? [], $composer['require-dev'] ?? []);
    }

    protected function analyzeStructure(): array
    {
        return [
            'controllers_count' => count(File::allFiles(app_path('Http/Controllers'))),
            'models_count' => count(File::allFiles(app_path('Models'))),
            'migrations_count' => count(File::allFiles(database_path('migrations'))),
            'routes_count' => count(File::allFiles(base_path('routes'))),
        ];
    }
}
