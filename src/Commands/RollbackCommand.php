<?php

namespace Codellitech\Elevate\Commands;

use Illuminate\Console\Command;
use Codellitech\Elevate\Rollback\GitSnapshot;

class RollbackCommand extends Command
{
    protected $signature = 'elevate:rollback';
    protected $description = 'Rollback the last elevation using Git snapshots.';

    public function handle()
    {
        $git = app(GitSnapshot::class);

        if (!$git->hasGit()) {
            $this->error('Git not detected. Rollback unavailable.');
            return 1;
        }

        $this->info('Rolling back to pre-elevation state...');
        
        if ($git->rollback()) {
            $this->info('Rollback successful!');
        } else {
            $this->error('Rollback failed. Please check your git status manually.');
        }

        return 0;
    }
}
