<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Scanners\ProjectScanner;
use Codellitech\Elevate\AI\AIManager;
use Codellitech\Elevate\Rollback\GitSnapshot;
use Codellitech\Elevate\Transformers\AITransformer;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Http;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;

class ElevateCommand extends Command
{
    protected $signature = 'elevate {--dry-run : Preview changes without applying them}';
    protected $description = 'Elevate your Laravel application to the next level with AI.';

    protected array $versions = ['5.8', '6.0', '7.0', '8.0', '9.0', '10.0', '11.0', '12.0', '13.0', '14.0'];
    protected array $actions_taken = [];

    public function handle()
    {
        $scanner = app(ProjectScanner::class);
        $git = app(GitSnapshot::class);
        
        $this->displayBranding();

        if (!$git->hasGit()) {
            $this->warn('Git not detected. Snapshots and rollbacks will be unavailable.');
            if (!$this->confirmAction('Proceed without safety snapshots?')) return 1;
        }

        $this->info('Analyzing project architecture...');
        $results = $scanner->scan();
        $this->showArchitectureSummary($results);

        $mode = $this->selectAction('What would you like to do?', [
            'modernize' => 'Modernize Existing Code (Refactor syntax & best practices)',
            'upgrade'   => 'Full Framework Upgrade (Move to a newer Laravel version)',
        ]);

        $targetVersion = null;
        if ($mode === 'upgrade') {
            $currentVersion = (float) $results['backend']['laravel_version'];
            $availableTargets = array_filter($this->versions, fn($v) => (float)$v > $currentVersion);
            
            if (empty($availableTargets)) {
                $this->info('You are already on the latest supported version!');
                $mode = 'modernize';
            } else {
                $targetVersion = $this->selectAction('Select target Laravel version', array_combine($availableTargets, array_map(fn($v) => "Laravel $v", $availableTargets)));
            }
        }

        if (!$this->confirmAction('Start the elevation process?')) {
            $this->info('Process cancelled.');
            return 0;
        }

        if ($git->hasGit()) {
            $git->createSnapshot();
        }

        $ai = app(AIManager::class);

        if ($mode === 'upgrade') {
            $this->spinAction("Updating composer.json for Laravel $targetVersion...", function () use ($targetVersion) {
                $this->upgradeComposer($targetVersion);
            });
        }

        $this->spinAction('Processing application files...', function () use ($results, $ai, $mode, $targetVersion) {
            $this->executeModernization($results, $ai, $mode, $targetVersion);
        });

        $this->displayFinalReport();
        $this->outroMessage('Elevation complete! Your application has been successfully transformed.');
        
        return 0;
    }

    protected function upgradeComposer(string $target)
    {
        $path = base_path('composer.json');
        if (!File::exists($path)) return;

        $composer = json_decode(File::get($path), true);
        $composer['require']['laravel/framework'] = "^$target";
        
        if ((float)$target >= 10.0) $composer['require']['php'] = "^8.1|^8.2";
        if ((float)$target >= 11.0) $composer['require']['php'] = "^8.2|^8.3";

        File::put($path, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        $this->actions_taken[] = ['Dependency', "Upgraded laravel/framework to ^$target", 'composer.json'];
    }

    protected function executeModernization(array $results, AIManager $ai, string $mode, ?string $targetVersion)
    {
        $pathKeys = config('elevate.paths', ['app', 'config', 'database', 'resources', 'routes']);
        $files = [];
        
        foreach ($pathKeys as $key) {
            $path = base_path($key);
            if ($key === 'app') $path = app_path();
            
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
            $prompt = $mode === 'upgrade' 
                ? "Upgrade this Laravel file from version {$results['backend']['laravel_version']} to Laravel {$targetVersion}."
                : "Refactor this Laravel file using modern PHP 8.2+ features.";

            $modernized = $ai->engine()->prompt($prompt . "\n\nCode:\n" . $content . "\n\nReturn ONLY code.");

            if ($modernized && $modernized !== $content && !str_contains($modernized, 'Error:')) {
                File::put($file->getRealPath(), $modernized);
                $this->actions_taken[] = ['File Refactor', $file->getRelativePathname(), 'Elevated'];
            }
        }
    }

    protected function shouldSkip($file): bool
    {
        $excludes = config('elevate.exclude', ['vendor', 'node_modules', 'storage', 'bootstrap/cache']);
        foreach ($excludes as $exclude) {
            if (str_contains($file->getRelativePathname(), $exclude)) return true;
        }
        return $file->getExtension() !== 'php';
    }

    protected function displayFinalReport()
    {
        $this->line("\n" . str_repeat('━', 72));
        $this->line('                           ELEVATION REPORT');
        $this->line(str_repeat('━', 72));
        
        if (empty($this->actions_taken)) {
            $this->info('No changes were necessary.');
        } else {
            $this->tableAction(['Type', 'Target/Action', 'Status'], $this->actions_taken);
            
            // Helpful instruction for major upgrades
            if (collect($this->actions_taken)->contains(fn($a) => str_contains($a[1], 'Upgraded laravel/framework'))) {
                $this->warn("\n[!] Framework upgrade detected in composer.json.");
                $this->info("Please run: composer update -W  to finalize the installation.");
            }
        }
        $this->line('');
    }

    protected function showArchitectureSummary(array $results)
    {
        $rows = [
            ['Laravel Version', $results['backend']['laravel_version']],
            ['PHP Version', $results['backend']['php_version']],
            ['Controllers', $results['backend']['structure']['controllers_count']],
            ['Models', $results['backend']['structure']['models_count']],
        ];
        $this->tableAction(['Component', 'Current Value'], $rows);
    }

    protected function displayBranding()
    {
        $this->line(str_repeat('━', 72));
        $this->line('               LARAVEL ELEVATE by Codelli Technologies');
        $this->line('                   Autonomous Migration Platform');
        $this->line(str_repeat('━', 72));
        $this->line('');
    }

    protected function selectAction($message, $options)
    {
        return function_exists('Laravel\Prompts\select') ? select($message, $options) : $this->choice($message, array_values($options), array_key_first($options));
    }

    protected function confirmAction($message)
    {
        return function_exists('Laravel\Prompts\confirm') ? confirm($message) : $this->confirm($message, true);
    }

    protected function spinAction($message, $callback)
    {
        if (function_exists('Laravel\Prompts\spin')) return spin($callback, $message);
        $this->info($message . '...');
        return $callback();
    }

    protected function tableAction($headers, $rows)
    {
        function_exists('Laravel\Prompts\table') ? table($headers, $rows) : $this->table($headers, $rows);
    }

    protected function outroMessage($message)
    {
        if (function_exists('Laravel\Prompts\outro')) {
            outro($message);
        } else {
            $this->info($message);
        }
    }
}
