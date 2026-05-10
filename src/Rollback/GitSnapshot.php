<?php

namespace Codellitech\Elevate\Rollback;

use Symfony\Component\Process\Process;

class GitSnapshot
{
    public function hasGit(): bool
    {
        $process = new Process(['git', '--version']);
        $process->run();
        
        if (!$process->isSuccessful()) {
            return false;
        }

        $process = new Process(['git', 'rev-parse', '--is-inside-work-tree']);
        $process->run();

        return $process->isSuccessful();
    }

    public function isClean(): bool
    {
        if (!$this->hasGit()) {
            return true; 
        }

        $process = new Process(['git', 'status', '--porcelain']);
        $process->run();

        return empty(trim($process->getOutput()));
    }

    public function snapshot(): bool
    {
        $branchName = 'elevate-backup-' . date('YmdHis');
        
        $process = new Process(['git', 'checkout', '-b', $branchName]);
        $process->run();

        return $process->isSuccessful();
    }

    public function rollback(string $branch): bool
    {
        $process = new Process(['git', 'checkout', $branch]);
        $process->run();

        return $process->isSuccessful();
    }
}
