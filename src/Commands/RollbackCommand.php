<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Rollback\GitSnapshot;
use function Laravel\Prompts\select;
use function Laravel\Prompts\intro;
use function Laravel\Prompts\outro;
use function Laravel\Prompts\spin;

class RollbackCommand extends Command
{
    protected $signature = 'elevate:rollback {branch? : The backup branch to rollback to}';
    protected $description = 'Rollback changes made by Elevate.';

    public function handle(GitSnapshot $git)
    {
        intro('Elevate Rollback Engine');

        $branch = $this->argument('branch') ?: select(
            'Which backup would you like to restore?',
            ['master' => 'Main Branch', 'elevate-backup-latest' => 'Latest Backup']
        );

        spin(
            fn () => $git->rollback($branch),
            'Restoring application state...'
        );

        outro("Application rolled back to {$branch}!");
    }
}
