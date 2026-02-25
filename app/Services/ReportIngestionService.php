<?php

namespace App\Services;

use App\Enums\MachineStatus;
use App\Models\ApiToken;
use App\Models\Machine;
use App\Models\PackageUpdate;
use App\Models\Report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportIngestionService
{
    public function ingest(array $data, ApiToken $apiToken): Report
    {
        return DB::transaction(function () use ($data, $apiToken) {
            $machine = Machine::firstOrCreate(
                ['hostname' => $data['hostname']],
                ['api_token_id' => $apiToken->id]
            );

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
                    'update_count' => count($updates),
                    'created_at' => now(),
                ]);

                if (! empty($updates)) {
                    $inserts = array_map(fn ($u) => [
                        'checker_result_id' => $checker->id,
                        'name' => $u['name'],
                        'current_version' => $u['current_version'],
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

            return $report;
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
