<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

class ClearOldLogs extends Command
{
    protected $signature = 'log:clear {--days=30 : Number of days to keep logs}';
    protected $description = 'Clear old log files';

    public function handle(): int
    {
        $days = $this->option('days');
        $logPath = storage_path('logs');
        $files = File::files($logPath);

        $deleted = 0;
        $cutoffDate = now()->subDays($days);

        foreach ($files as $file) {
            if ($file->getMTime() < $cutoffDate->timestamp) {
                File::delete($file->getPathname());
                $deleted++;
                $this->line("Deleted: {$file->getFilename()}");
            }
        }

        $this->info("Deleted {$deleted} old log files.");

        return SymfonyCommand::SUCCESS;
    }
}
