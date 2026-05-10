<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Codellitech\Elevate\Scanners\ProjectScanner;
use Codellitech\Elevate\AI\AIManager;
use Codellitech\Elevate\Rollback\GitSnapshot;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\table;
use function Laravel\Prompts\progress;
use function Laravel\Prompts\warning;
use function Laravel\Prompts\note;

class ElevateCommand extends Command
{
    protected $signature = 'elevate {--dry-run : Preview changes without applying them}';
    protected $description = 'Elevate your Laravel application to the next level with AI.';

    public function handle(ProjectScanner $scanner, AIManager $ai, GitSnapshot $git)
    {
        $this->displayBranding();

        if (!$git->hasGit()) {
            warning('Git not detected. Snapshots and rollbacks will be unavailable.');
            if (!confirm('Proceed without safety snapshots?')) {
                return;
            }
        } elseif (!$git->isClean() && !$this->option('dry-run')) {
            if (!confirm('Your git working directory is not clean. Continue anyway?')) {
                return;
            }
        }

        $scanResults = spin(
            fn () => $scanner->scan(),
            'Scanning project architecture...'
        );

        $this->displaySummary($scanResults);

        if (!confirm('Proceed with AI-driven modernization?')) {
            outro('Modernization cancelled.');
            return;
        }

        if (!$this->option('dry-run')) {
            spin(
                fn () => $git->snapshot(),
                'Creating safety snapshot...'
            );
        }

        $this->executeModernization($scanResults, $ai);

        outro('Laravel Elevate: Modernization complete! Check the history logs for details.');
    }

    protected function displayBranding()
    {
        $this->line('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        $this->line('<fg=cyan;options=bold>               LARAVEL ELEVATE by Codellitech</>');
        $this->line('<fg=cyan>                   AI Modernization Platform</>');
        $this->line('<fg=cyan>━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━</>');
        intro('Elevating your application architecture...');
    }

    protected function displaySummary(array $results)
    {
        info('Architecture Summary:');
        
        table(
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
            note('No files found to modernize.');
            return;
        }

        $transformer = new \Codellitech\Elevate\Transformers\AITransformer($ai);

        progress(
            label: 'Modernizing application files',
            steps: count($files),
            callback: function ($step) use ($files, $transformer) {
                $file = $files[$step - 1];
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
        );

        note('AI reasoning: Successfully analyzed ' . count($files) . ' files and applied modernization patterns where necessary.');
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
