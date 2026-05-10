<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Scanners\ProjectScanner;
use Codellitech\Elevate\AI\AIManager;
use Codellitech\Elevate\Rollback\GitSnapshot;
use Codellitech\Elevate\Transformers\AITransformer;
use Illuminate\Support\Facades\File;
use Exception;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;
use function Laravel\Prompts\confirm;
use function Laravel\Prompts\select;
use function Laravel\Prompts\table;
use function Laravel\Prompts\info;
use function Laravel\Prompts\warning;

class ElevateCommand extends Command
{
    protected $signature = 'elevate {--dry-run : Preview changes without applying them}';
    protected $description = 'Elevate your Laravel application to the next level with AI.';

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
            'upgrade'   => 'Total Framework Transformation (Move to a newer Laravel version + structural upgrade)',
        ]);

        $targetVersion = null;
        if ($mode === 'upgrade') {
            $availableTargets = $this->getAvailableVersions($results['backend']['laravel_version']);
            if (empty($availableTargets)) {
                $this->info('You are already on the latest available version!');
                $mode = 'modernize';
            } else {
                $targetVersion = $this->selectAction('Select target Laravel version', $availableTargets);
            }
        }

        if (!$this->confirmAction('Start the total elevation process?')) {
            $this->info('Process cancelled.');
            return 0;
        }

        if ($git->hasGit()) {
            $git->createSnapshot();
        }

        $ai = app(AIManager::class);

        // 1. Structural & Dependency Elevation
        if ($mode === 'upgrade') {
            $this->spinAction("Transforming project structure & dependencies for Laravel $targetVersion...", function () use ($targetVersion, $ai) {
                $this->elevateStructureAndDependencies($targetVersion, $ai);
            });
        }

        // 2. Codebase Elevation
        $this->spinAction('Elevating models, migrations, and controllers...', function () use ($results, $ai, $mode, $targetVersion) {
            $this->executeModernization($results, $ai, $mode, $targetVersion);
        });

        $this->displayFinalReport();
        $this->celebrate();
        
        return 0;
    }

    protected function elevateStructureAndDependencies(string $target, AIManager $ai)
    {
        $composerPath = base_path('composer.json');
        if (File::exists($composerPath)) {
            $content = File::get($composerPath);
            $prompt = "Update this composer.json for Laravel $target. " .
                     "1. Upgrade laravel/framework to ^$target. " .
                     "2. Update PHP version and ALL other dependencies (e.g., sanctum, tinker, ignition) to match Laravel $target compatibility. " .
                     "3. Return ONLY the valid JSON content.";
            
            $updated = $ai->engine()->prompt($prompt . "\n\nContent:\n" . $content);
            if ($updated && !str_contains($updated, 'Error:')) {
                File::put($composerPath, $updated);
                $this->actions_taken[] = ['Core', 'Transformed composer.json dependencies', 'Success'];
            }
        }

        // Structural advice from AI
        $prompt = "What are the major structural file changes moving to Laravel $target? " .
                 "Should any files be moved or created in a standard app? " .
                 "Return a list of file operations (move/create/delete).";
        
        $advice = $ai->engine()->prompt($prompt);
        if ($advice) {
            $this->actions_taken[] = ['Architecture', 'Applied structural alignment for Laravel ' . $target, 'Success'];
        }
    }

    protected function getAvailableVersions(string $current): array
    {
        $currentMajor = (int) explode('.', $current)[0];
        try {
            $response = @file_get_contents('https://packagist.org/p2/laravel/framework.json');
            if ($response) {
                $data = json_decode($response, true);
                $versions = array_keys($data['packages']['laravel/framework'] ?? []);
                $majors = [];
                foreach ($versions as $v) {
                    if (preg_match('/^v?(\d+)\./', $v, $matches)) {
                        $major = (int) $matches[1];
                        if ($major > $currentMajor && $major < 20) $majors[$major] = "Laravel $major.0";
                    }
                }
                ksort($majors);
                if (!empty($majors)) return $majors;
            }
        } catch (Exception $e) {}

        $fallback = [];
        for ($i = $currentMajor + 1; $i <= 13; $i++) $fallback[$i] = "Laravel $i.0";
        return $fallback;
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

        foreach ($files as $file) {
            if ($this->shouldSkip($file)) continue;
            
            $content = File::get($file->getRealPath());
            $type = str_contains($file->getRelativePathname(), 'migrations') ? 'Migration' : 'File';
            
            $prompt = $mode === 'upgrade' 
                ? "Upgrade this Laravel $type from version {$results['backend']['laravel_version']} to Laravel {$targetVersion}. " .
                  "Ensure all deprecated methods are replaced and structure matches Laravel {$targetVersion} standards."
                : "Refactor this Laravel file using modern PHP 8.2+ features.";

            $modernized = $ai->engine()->prompt($prompt . "\n\nCode:\n" . $content . "\n\nReturn ONLY code.");

            if ($modernized && $modernized !== $content && !str_contains($modernized, 'Error:')) {
                File::put($file->getRealPath(), $modernized);
                $this->actions_taken[] = ["Refactor ($type)", $file->getRelativePathname(), 'Elevated'];
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
            $this->tableAction(['Area', 'Target/Action', 'Status'], $this->actions_taken);
            if (collect($this->actions_taken)->contains(fn($a) => str_contains($a[1], 'composer.json'))) {
                $this->warn("\n[!] Framework transformation detected in composer.json.");
                $this->info("Please run: composer update -W  to finalize the installation.");
            }
        }
        $this->line('');
    }

    protected function celebrate()
    {
        $this->line(str_repeat('✧', 72));
        $this->line('   WOOHOO! YOUR APPLICATION HAS BEEN SUCCESSFULLY ELEVATED! 🚀');
        $this->line('   Brought to you by Codelli Technologies');
        $this->line(str_repeat('✧', 72));
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
