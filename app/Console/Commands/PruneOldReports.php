<?php

namespace App\Console\Commands;

use App\Models\Report;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PruneOldReports extends Command
{
    protected $signature = 'reports:prune {--days=90 : Delete reports older than N days}';
    protected $description = 'Delete old reports while keeping at least the latest report per machine';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $latestReportIds = DB::table('reports')
            ->selectRaw('MAX(id) as id')
            ->groupBy('machine_id')
            ->pluck('id');

        $count = Report::where('created_at', '<', now()->subDays($days))
            ->whereNotIn('id', $latestReportIds)
            ->delete();

        $this->info("Deleted {$count} old report(s).");

        return self::SUCCESS;
    }
}
