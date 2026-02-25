<?php

namespace App\Console\Commands;

use App\Enums\MachineStatus;
use App\Models\Machine;
use Illuminate\Console\Command;

class MarkStaleMachines extends Command
{
    protected $signature = 'machines:mark-stale {--hours=25 : Hours threshold}';
    protected $description = 'Mark machines as stale if they haven\'t reported within the threshold';

    public function handle(): int
    {
        $hours = (int) $this->option('hours');

        $count = Machine::where('status', '!=', MachineStatus::Stale)
            ->where('last_report_at', '<', now()->subHours($hours))
            ->update(['status' => MachineStatus::Stale]);

        $this->info("Marked {$count} machine(s) as stale.");

        return self::SUCCESS;
    }
}
