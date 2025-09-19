<?php

namespace App\Console\Commands;

use App\Services\AuditLogger;
use Illuminate\Console\Command;

class CleanAuditLogs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:clean {--days=90 : Number of days to keep audit logs}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old audit logs to maintain database performance';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $daysToKeep = (int) $this->option('days');
        
        if ($daysToKeep < 1) {
            $this->error('Days to keep must be at least 1.');
            return 1;
        }

        $this->info("Cleaning audit logs older than {$daysToKeep} days...");
        
        $deletedCount = AuditLogger::cleanOldLogs($daysToKeep);
        
        if ($deletedCount > 0) {
            $this->info("Successfully deleted {$deletedCount} old audit log records.");
        } else {
            $this->info('No old audit logs found to clean.');
        }
        
        return 0;
    }
}
