<?php

namespace Codellitech\Elevate\Scanners;

use Illuminate\Support\Facades\File;

class ProjectScanner
{
    protected BackendScanner $backend;
    protected FrontendScanner $frontend;

    public function __construct(BackendScanner $backend, FrontendScanner $frontend)
    {
        $this->backend = $backend;
        $this->frontend = $frontend;
    }

    public function scan(): array
    {
        return [
            'backend' => $this->backend->analyze(),
            'frontend' => $this->frontend->analyze(),
            'infrastructure' => $this->scanInfrastructure(),
        ];
    }

    protected function scanInfrastructure(): array
    {
        return [
            'docker' => File::exists(base_path('Dockerfile')) || File::exists(base_path('docker-compose.yml')),
            'sail' => File::exists(base_path('vendor/bin/sail')),
            'vapor' => File::exists(base_path('vapor.yml')),
            'forge' => File::exists(base_path('.forge')),
            'github_actions' => File::isDirectory(base_path('.github/workflows')),
        ];
    }
}
