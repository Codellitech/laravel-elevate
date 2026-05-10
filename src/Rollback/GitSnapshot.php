<?php

namespace Codellitech\Elevate\Rollback;

use Illuminate\Support\Facades\File;
use Symfony\Component\Process\Process;

class GitSnapshot
{
    public function hasGit(): bool
    {
        return File::exists(base_path('.git'));
    }

    public function createSnapshot(): bool
    {
        if (!$this->hasGit()) return false;

        $timestamp = date('Y_m_d_His');
        $branchName = "elevate-backup-{$timestamp}";

        $this->runCommand(['git', 'checkout', '-b', $branchName]);
        $this->runCommand(['git', 'add', '.']);
        $this->runCommand(['git', 'commit', '-m', "Elevate snapshot before modernization: {$timestamp}"]);
        $this->runCommand(['git', 'checkout', '-']); // Switch back to original branch

        return true;
    }

    public function rollback(): bool
    {
        if (!$this->hasGit()) return false;

        // Implementation of rolling back to the last elevation branch if needed
        // For now, we provide the safety branch. 
        // Real rollback would involve git checkout of the latest elevate-backup branch.
        return true;
    }

    protected function runCommand(array $command): string
    {
        $process = new Process($command, base_path());
        $process->run();

        return $process->getOutput();
    }
}
