<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Codellitech\Elevate\Scanners\ProjectScanner;
use Codellitech\Elevate\AI\AIManager;
use Codellitech\Elevate\Rollback\GitSnapshot;

class ElevateCommand extends Command
{
    protected $signature = 'elevate {--dry-run : Preview changes without applying them}';
    protected $description = 'Elevate your Laravel application to the next level with AI.';

    public function handle(ProjectScanner $scanner, AIManager $ai, GitSnapshot $git)
    {
        $this->displayBranding();

        if (!$git->hasGit()) {
            $this->warn('Git not detected. Snapshots and rollbacks will be unavailable.');
            if (!$this->confirmAction('Proceed without safety snapshots?')) {
                return;
            }
        } elseif (!$git->isClean() && !$this->option('dry-run')) {
            if (!$this->confirmAction('Your git working directory is not clean. Continue anyway?')) {
                return;
            }
        }

        $scanResults = $this->spinAction(
            fn () => $scanner->scan(),
            'Scanning project architecture...'
        );

        $this->displaySummary($scanResults);

        if (!$this->confirmAction('Proceed with AI-driven modernization?')) {
            $this->outroAction('Modernization cancelled.');
            return;
        }

        if (!$this->option('dry-run')) {
            $this->spinAction(
                fn () => $git->snapshot(),
                'Creating safety snapshot...'
            );
        }

        $this->executeModernization($scanResults, $ai);

        $this->outroAction('Laravel Elevate: Modernization complete! Check the history logs for details.');
    }

    protected function displayBranding()
    {
        $this->line('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $this->line('<fg=cyan;options=bold>               LARAVEL ELEVATE by Codellitech</>');
        $this->line('<fg=cyan>                   AI Modernization Platform</>');
        $this->line('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        
        if (function_exists('Laravel\Prompts\intro')) {
            \Laravel\Prompts\intro('Elevating your application architecture...');
        } else {
            $this->info('Elevating your application architecture...');
        }
    }

    protected function displaySummary(array $results)
    {
        $this->info('Architecture Summary:');
        
        if (function_exists('Laravel\Prompts\table')) {
            \Laravel\Prompts\table(
                ['Component', 'Status/Value'],
                [
                    ['Laravel Version', $results['backend']['laravel_version']],
                    ['PHP Version', $results['backend']['php_version']],
                    ['Bundler', $results['frontend']['bundler']],
                    ['CSS Framework', $results['frontend']['css_framework']],
                    ['JS Framework', $results['frontend']['js_framework']],
                    ['Modernization Needed', $results['frontend']['modernization_needed'] ? 'Yes' : 'No'],
                ]
            );
        } else {
            $this->table(
                ['Component', 'Status/Value'],
                [
                    ['Laravel Version', $results['backend']['laravel_version']],
                    ['PHP Version', $results['backend']['php_version']],
                    ['Bundler', $results['frontend']['bundler']],
                    ['CSS Framework', $results['frontend']['css_framework']],
                    ['JS Framework', $results['frontend']['js_framework']],
                    ['Modernization Needed', $results['frontend']['modernization_needed'] ? 'Yes' : 'No'],
                ]
            );
        }
    }

    protected function executeModernization(array $results, AIManager $ai)
    {
        $paths = config('elevate.paths', [app_path()]);
        $files = [];
        
        foreach ($paths as $path) {
            if (File::isDirectory($path)) {
                $files = array_merge($files, File::allFiles($path));
            } elseif (File::exists($path)) {
                $files[] = new \Symfony\Component\Finder\SplFileInfo($path, $path, $path);
            }
        }

        if (empty($files)) {
            $this->noteAction('No files found to modernize.');
            return;
        }

        $transformer = new \Codellitech\Elevate\Transformers\AITransformer($ai);

        if (function_exists('Laravel\Prompts\progress')) {
            \Laravel\Prompts\progress(
                label: 'Modernizing application files',
                steps: $files,
                callback: function ($file) use ($transformer) {
                    return $this->processFile($file, $transformer);
                }
            );
        } else {
            $this->info('Modernizing application files...');
            foreach ($files as $file) {
                $this->line($this->processFile($file, $transformer));
            }
        }

        $this->noteAction('AI reasoning: Successfully analyzed ' . count($files) . ' files and applied modernization patterns where necessary.');
    }

    protected function processFile($file, $transformer)
    {
        $path = $file->getRealPath();
        
        if ($this->shouldSkip($path)) {
            return "Skipped: " . $file->getRelativePathname();
        }

        $content = File::get($path);
        $modernized = $transformer->transform($content, $path);

        if ($modernized && $modernized !== $content && !$this->option('dry-run')) {
            File::put($path, $modernized);
            return "Modernized: " . $file->getRelativePathname();
        }

        return "Checked: " . $file->getRelativePathname();
    }

    protected function confirmAction(string $message): bool
    {
        if (function_exists('Laravel\Prompts\confirm')) {
            return \Laravel\Prompts\confirm($message);
        }
        return $this->confirm($message, true);
    }

    protected function spinAction(\Closure $callback, string $message)
    {
        if (function_exists('Laravel\Prompts\spin')) {
            return \Laravel\Prompts\spin($callback, $message);
        }
        $this->info($message);
        return $callback();
    }

    protected function outroAction(string $message)
    {
        if (function_exists('Laravel\Prompts\outro')) {
            \Laravel\Prompts\outro($message);
        } else {
            $this->info($message);
        }
    }

    protected function noteAction(string $message)
    {
        if (function_exists('Laravel\Prompts\note')) {
            \Laravel\Prompts\note($message);
        } else {
            $this->line("<fg=yellow>{$message}</>");
        }
    }

    protected function shouldSkip(string $path): bool
    {
        $excludes = config('elevate.exclude', []);
        foreach ($excludes as $exclude) {
            if (str_contains($path, DIRECTORY_SEPARATOR . $exclude . DIRECTORY_SEPARATOR)) {
                return true;
            }
        }
        return false;
    }
}
