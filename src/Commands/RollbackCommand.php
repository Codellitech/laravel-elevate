<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Rollback\GitSnapshot;

class RollbackCommand extends Command
{
    protected $signature = 'elevate:rollback {branch? : The backup branch to rollback to}';
    protected $description = 'Rollback changes made by Elevate.';

    public function handle(GitSnapshot $git)
    {
        $this->introAction('Elevate Rollback Engine');

        $options = [
            'master' => 'Main Branch',
            'elevate-backup-latest' => 'Latest Backup',
        ];

        $branch = $this->argument('branch');

        if (!$branch) {
            if (function_exists('Laravel\Prompts\select')) {
                $branch = \Laravel\Prompts\select('Which backup would you like to restore?', $options);
            } else {
                $branch = $this->choice('Which backup would you like to restore?', $options, 'master');
            }
        }

        $this->spinAction(
            fn () => $git->rollback($branch),
            'Restoring application state...'
        );

        $this->outroAction("Application rolled back to {$branch}!");
    }

    protected function introAction(string $message)
    {
        if (function_exists('Laravel\Prompts\intro')) {
            \Laravel\Prompts\intro($message);
        } else {
            $this->info($message);
        }
    }

    protected function outroAction(string $message)
    {
        if (function_exists('Laravel\Prompts\outro')) {
            \Laravel\Prompts\outro($message);
        } else {
            $this->info($message);
        }
    }

    protected function spinAction(\Closure $callback, string $message)
    {
        if (function_exists('Laravel\Prompts\spin')) {
            return \Laravel\Prompts\spin($callback, $message);
        }
        $this->info($message);
        return $callback();
    }
}
