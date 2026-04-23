<?php
// app/Console/Commands/CleanExpiredNotifications.php

namespace App\Console\Commands;

use App\Models\Notification;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanExpiredNotifications extends Command
{
    protected $signature = 'notifications:clean
                            {--days=30 : Delete read notifications older than X days}
                            {--force : Skip confirmation}
                            {--dry-run : Show what would be deleted without actually deleting}';

    protected $description = 'Clean up expired and old notifications';

    public function handle()
    {
        $this->info('Cleaning up notifications...');
        $daysOld = (int) $this->option('days');
        $force = $this->option('force');
        $dryRun = $this->option('dry-run');

        $stats = [];

        // 1. Delete expired notifications (based on expires_at)
        $expiredCount = Notification::expired()->count();
        $stats['expired'] = $expiredCount;

        if ($expiredCount > 0) {
            $this->warn("Found {$expiredCount} expired notifications");

            if (!$dryRun) {
                if ($force || $this->confirm("Delete {$expiredCount} expired notifications?")) {
                    $deleted = Notification::expired()->delete();
                    $this->info("✓ Deleted {$deleted} expired notifications");
                    $stats['expired_deleted'] = $deleted;
                }
            } else {
                $this->info("[DRY RUN] Would delete {$expiredCount} expired notifications");
                $stats['expired_deleted'] = $expiredCount;
            }
        }

        // 2. Delete old read notifications
        $cutoffDate = now()->subDays($daysOld);
        $oldReadCount = Notification::where('read', true)
            ->where('created_at', '<', $cutoffDate)
            ->count();
        $stats['old_read'] = $oldReadCount;

        if ($oldReadCount > 0) {
            $this->warn("Found {$oldReadCount} read notifications older than {$daysOld} days");

            if (!$dryRun) {
                if ($force || $this->confirm("Delete {$oldReadCount} old read notifications?")) {
                    $deleted = Notification::where('read', true)
                        ->where('created_at', '<', $cutoffDate)
                        ->delete();
                    $this->info("✓ Deleted {$deleted} old read notifications");
                    $stats['old_read_deleted'] = $deleted;
                }
            } else {
                $this->info("[DRY RUN] Would delete {$oldReadCount} old read notifications");
                $stats['old_read_deleted'] = $oldReadCount;
            }
        }

        // 3. Delete notifications without user (orphaned)
        $orphanedCount = Notification::whereDoesntHave('user')->count();
        $stats['orphaned'] = $orphanedCount;

        if ($orphanedCount > 0) {
            $this->warn("Found {$orphanedCount} orphaned notifications (no user)");

            if (!$dryRun) {
                if ($force || $this->confirm("Delete {$orphanedCount} orphaned notifications?")) {
                    $deleted = Notification::whereDoesntHave('user')->delete();
                    $this->info("✓ Deleted {$deleted} orphaned notifications");
                    $stats['orphaned_deleted'] = $deleted;
                }
            } else {
                $this->info("[DRY RUN] Would delete {$orphanedCount} orphaned notifications");
                $stats['orphaned_deleted'] = $orphanedCount;
            }
        }

        // Summary
        $this->newLine();
        $this->info("📊 Cleanup Summary:");
        $this->table(
            ['Category', 'Found', 'Deleted/Would Delete'],
            [
                ['Expired Notifications', $stats['expired'], $stats['expired_deleted'] ?? 0],
                ['Old Read Notifications (>'.$daysOld.' days)', $stats['old_read'], $stats['old_read_deleted'] ?? 0],
                ['Orphaned Notifications', $stats['orphaned'], $stats['orphaned_deleted'] ?? 0],
                ['TOTAL', $expiredCount + $oldReadCount + $orphanedCount,
                    ($stats['expired_deleted'] ?? 0) + ($stats['old_read_deleted'] ?? 0) + ($stats['orphaned_deleted'] ?? 0)]
            ]
        );

        // Log the cleanup
        Log::info('Notification cleanup completed', [
            'stats' => $stats,
            'days_old' => $daysOld,
            'dry_run' => $dryRun
        ]);

        return 0;
    }
}
