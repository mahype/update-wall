<?php

namespace App\Services;

use App\Enums\MachineStatus;
use App\Models\ApiToken;
use App\Models\Machine;
use App\Models\PackageUpdate;
use App\Models\Report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReportIngestionService
{
    public function ingest(array $data, ApiToken $apiToken): array
    {
        return DB::transaction(function () use ($data, $apiToken) {
            $machine = Machine::firstOrCreate(
                ['hostname' => $data['hostname']],
                ['api_token_id' => $apiToken->id]
            );

            if ($machine->wasRecentlyCreated) {
                Log::info('New machine registered', ['hostname' => $machine->hostname, 'id' => $machine->id]);
            } else {
                Log::debug('Report received for existing machine', ['hostname' => $machine->hostname, 'id' => $machine->id]);
            }

            $isNewMachine = $machine->wasRecentlyCreated;

            $report = $machine->reports()->create([
                'reported_at' => Carbon::parse($data['timestamp']),
                'total_updates' => $data['total_updates'],
                'has_security' => $data['has_security'],
                'raw_payload' => $data,
                'created_at' => now(),
            ]);

            foreach ($data['checkers'] as $checkerData) {
                $updates = $checkerData['updates'] ?? [];

                $checker = $report->checkerResults()->create([
                    'name' => $checkerData['name'],
                    'summary' => $checkerData['summary'],
                    'error' => $checkerData['error'] ?? null,
                    'update_hint' => $checkerData['update_hint'] ?? null,
                    'update_command' => $checkerData['update_command'] ?? null,
                    'update_count' => count($updates),
                    'created_at' => now(),
                ]);

                if (! empty($updates)) {
                    $inserts = array_map(fn ($u) => [
                        'checker_result_id' => $checker->id,
                        'name' => $u['name'],
                        'current_version' => $u['current_version'] ?? null,
                        'new_version' => $u['new_version'],
                        'type' => $u['type'],
                        'priority' => $u['priority'],
                        'source' => $u['source'] ?? null,
                        'phasing' => $u['phasing'] ?? null,
                    ], $updates);

                    PackageUpdate::insert($inserts);
                }
            }

            $this->updateMachineStatus($machine, $data);

            return ['report' => $report, 'is_new_machine' => $isNewMachine];
        });
    }

    private function updateMachineStatus(Machine $machine, array $data): void
    {
        $status = match (true) {
            $data['has_security'] => MachineStatus::Security,
            $data['total_updates'] > 0 => MachineStatus::Updates,
            default => MachineStatus::Ok,
        };

        $machine->update([
            'last_report_at' => Carbon::parse($data['timestamp']),
            'total_updates' => $data['total_updates'],
            'has_security' => $data['has_security'],
            'status' => $status,
        ]);
    }
}
