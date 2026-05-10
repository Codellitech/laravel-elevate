<?php

namespace Codellitech\Elevate\Scanners;

use Illuminate\Support\Facades\File;

class FrontendScanner
{
    public function analyze(): array
    {
        return [
            'bundler' => $this->detectBundler(),
            'css_framework' => $this->detectCssFramework(),
            'js_framework' => $this->detectJsFramework(),
            'modernization_needed' => $this->checkModernizationNeeded(),
        ];
    }

    protected function detectBundler(): string
    {
        if (File::exists(base_path('vite.config.js')) || File::exists(base_path('vite.config.ts'))) {
            return 'vite';
        }

        if (File::exists(base_path('webpack.mix.js'))) {
            return 'mix';
        }

        return 'unknown';
    }

    protected function detectCssFramework(): string
    {
        $packageJson = $this->getPackageJson();
        $dependencies = array_merge($packageJson['dependencies'] ?? [], $packageJson['devDependencies'] ?? []);

        if (isset($dependencies['tailwindcss'])) return 'tailwind';
        if (isset($dependencies['bootstrap'])) return 'bootstrap';

        return 'none';
    }

    protected function detectJsFramework(): string
    {
        $packageJson = $this->getPackageJson();
        $dependencies = array_merge($packageJson['dependencies'] ?? [], $packageJson['devDependencies'] ?? []);

        if (isset($dependencies['vue'])) return 'vue';
        if (isset($dependencies['react'])) return 'react';
        if (isset($dependencies['alpinejs'])) return 'alpine';

        return 'blade';
    }

    protected function checkModernizationNeeded(): bool
    {
        return $this->detectBundler() === 'mix' || $this->detectCssFramework() === 'bootstrap';
    }

    protected function getPackageJson(): array
    {
        $path = base_path('package.json');
        if (!File::exists($path)) {
            return [];
        }

        return json_decode(File::get($path), true);
    }
}
