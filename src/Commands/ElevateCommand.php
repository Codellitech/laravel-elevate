<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Scanners\ProjectScanner;
use Codellitech\Elevate\AI\AIManager;
use Codellitech\Elevate\Rollback\GitSnapshot;
use Codellitech\Elevate\Transformers\AITransformer;
use Illuminate\Support\Facades\File;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\table;

class ElevateCommand extends Command
{
    protected $signature = 'elevate {--dry-run : Preview changes without applying them}';
    protected $description = 'Elevate your Laravel application to the next level with AI.';

    public function handle()
    {
        // Resolve dependencies lazily ONLY when the command is actually running
        $scanner = app(ProjectScanner::class);
        $git = app(GitSnapshot::class);
        $ai = app(AIManager::class);

        $this->displayBranding();

        if (!$git->hasGit()) {
            $this->warn('Git not detected. Snapshots and rollbacks will be unavailable.');
            if (!$this->confirmAction('Proceed without safety snapshots?')) {
                return 1;
            }
        }

        $this->info('Scanning project architecture...');
        $results = $scanner->scan();

        $this->showArchitectureSummary($results);

        if (!$this->confirmAction('Do you want to proceed with AI modernization?')) {
            $this->info('Modernization cancelled.');
            return 0;
        }

        if ($git->hasGit()) {
            $git->createSnapshot();
        }

        $this->spinAction('Elevating your code...', function () use ($results, $ai) {
            $this->executeModernization($results, $ai);
        });

        $this->outroAction('Modernization complete! Your application has been elevated.');
        
        return 0;
    }

    protected function executeModernization(array $results, AIManager $ai)
    {
        $pathKeys = config('elevate.paths', ['app']);
        $files = [];
        
        $pathMap = [
            'app' => app_path(),
            'config' => config_path(),
            'database' => database_path(),
            'resources' => resource_path(),
            'routes' => base_path('routes'),
            'tests' => base_path('tests'),
        ];

        foreach ($pathKeys as $key) {
            $path = $pathMap[$key] ?? base_path($key);
            
            if (File::isDirectory($path)) {
                $files = array_merge($files, File::allFiles($path));
            } elseif (File::exists($path)) {
                $files[] = new \Symfony\Component\Finder\SplFileInfo($path, $path, $path);
            }
        }

        $transformer = new AITransformer($ai);

        foreach ($files as $file) {
            if ($this->shouldSkip($file)) continue;
            
            $content = File::get($file->getRealPath());
            $modernized = $transformer->transform($content, $file->getRelativePathname());

            if ($modernized && $modernized !== $content) {
                File::put($file->getRealPath(), $modernized);
            }
        }
    }

    protected function shouldSkip($file): bool
    {
        $excludes = config('elevate.exclude', []);
        $path = $file->getRelativePathname();

        foreach ($excludes as $exclude) {
            if (str_contains($path, $exclude)) return true;
        }

        return $file->getExtension() !== 'php';
    }

    protected function showArchitectureSummary(array $results)
    {
        $this->info("\nArchitecture Summary:\n");

        $rows = [
            ['Laravel Version', $results['backend']['laravel_version']],
            ['PHP Version', $results['backend']['php_version']],
            ['Controllers', $results['backend']['structure']['controllers_count']],
            ['Models', $results['backend']['structure']['models_count']],
            ['Migrations', $results['backend']['structure']['migrations_count']],
            ['Frontend Bundler', $results['frontend']['bundler']],
            ['CSS Framework', $results['frontend']['css_framework']],
        ];

        $this->tableAction(['Component', 'Status/Value'], $rows);
    }

    protected function displayBranding()
    {
        $this->line(str_repeat('━', 72));
        $this->line('               LARAVEL ELEVATE by Codelli Technologies');
        $this->line('                   AI Modernization Platform');
        $this->line(str_repeat('━', 72));
        $this->line('');
        $this->info('  Elevating your application architecture...');
        $this->line('');
    }

    // Legacy Fallback UI Wrappers
    protected function introAction()
    {
        if (function_exists('Laravel\Prompts\intro')) {
            intro('Laravel Elevate');
        } else {
            $this->info('Laravel Elevate - Modernization Platform');
        }
    }

    protected function outroAction($message)
    {
        if (function_exists('Laravel\Prompts\outro')) {
            outro($message);
        } else {
            $this->info($message);
        }
    }

    protected function spinAction($message, $callback)
    {
        if (function_exists('Laravel\Prompts\spin')) {
            return spin($callback, $message);
        } else {
            $this->info($message . ' (please wait)');
            return $callback();
        }
    }

    protected function confirmAction($message)
    {
        if (function_exists('Laravel\Prompts\confirm')) {
            return confirm($message);
        } else {
            return $this->confirm($message, true);
        }
    }

    protected function tableAction($headers, $rows)
    {
        if (function_exists('Laravel\Prompts\table')) {
            table($headers, $rows);
        } else {
            $this->table($headers, $rows);
        }
    }
}
