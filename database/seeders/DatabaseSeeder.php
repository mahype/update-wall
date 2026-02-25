<?php

namespace Database\Seeders;

use App\Enums\MachineStatus;
use App\Models\ApiToken;
use App\Models\CheckerResult;
use App\Models\Machine;
use App\Models\PackageUpdate;
use App\Models\Report;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        $admin = User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'is_admin' => true,
        ]);

        // API Token
        $tokenResult = ApiToken::createFor($admin, 'demo-token');
        $this->command->info("Demo API-Token: {$tokenResult['plain_text']}");

        // Demo machines with various statuses
        $this->createDemoMachine($tokenResult['token'], 'webserver-prod', MachineStatus::Security, [
            ['checker' => 'apt', 'summary' => '5 updates available (2 security)', 'updates' => [
                ['name' => 'libssl3', 'current_version' => '3.0.2-1', 'new_version' => '3.0.2-2', 'type' => 'security', 'priority' => 'critical'],
                ['name' => 'openssl', 'current_version' => '3.0.2-1', 'new_version' => '3.0.2-2', 'type' => 'security', 'priority' => 'high'],
                ['name' => 'nginx', 'current_version' => '1.24.0', 'new_version' => '1.26.0', 'type' => 'regular', 'priority' => 'normal'],
                ['name' => 'curl', 'current_version' => '7.88.1', 'new_version' => '8.5.0', 'type' => 'regular', 'priority' => 'normal'],
                ['name' => 'git', 'current_version' => '2.39.2', 'new_version' => '2.43.0', 'type' => 'regular', 'priority' => 'low'],
            ]],
            ['checker' => 'npm', 'summary' => 'No updates available', 'updates' => []],
        ]);

        $this->createDemoMachine($tokenResult['token'], 'db-server', MachineStatus::Ok, [
            ['checker' => 'apt', 'summary' => 'All packages are up to date', 'updates' => []],
            ['checker' => 'docker', 'summary' => 'All images are up to date', 'updates' => []],
        ]);

        $this->createDemoMachine($tokenResult['token'], 'app-staging', MachineStatus::Updates, [
            ['checker' => 'apt', 'summary' => '3 updates available', 'updates' => [
                ['name' => 'php8.2-fpm', 'current_version' => '8.2.15', 'new_version' => '8.2.17', 'type' => 'regular', 'priority' => 'normal'],
                ['name' => 'redis-server', 'current_version' => '7.0.11', 'new_version' => '7.2.4', 'type' => 'regular', 'priority' => 'normal'],
                ['name' => 'composer', 'current_version' => '2.6.5', 'new_version' => '2.7.1', 'type' => 'regular', 'priority' => 'low'],
            ]],
            ['checker' => 'npm', 'summary' => '2 updates available', 'updates' => [
                ['name' => 'vite', 'current_version' => '5.0.0', 'new_version' => '5.1.4', 'type' => 'regular', 'priority' => 'normal'],
                ['name' => 'tailwindcss', 'current_version' => '3.3.0', 'new_version' => '3.4.1', 'type' => 'regular', 'priority' => 'low'],
            ]],
        ]);

        $this->createDemoMachine($tokenResult['token'], 'mail-server', MachineStatus::Ok, [
            ['checker' => 'apt', 'summary' => 'All packages are up to date', 'updates' => []],
        ]);

        $this->createDemoMachine($tokenResult['token'], 'backup-server', MachineStatus::Ok, [
            ['checker' => 'apt', 'summary' => 'All packages are up to date', 'updates' => []],
        ]);

        $this->command->info('Demo data seeded successfully.');
    }

    private function createDemoMachine(ApiToken $token, string $hostname, MachineStatus $status, array $checkers): void
    {
        $totalUpdates = 0;
        $hasSecurity = false;

        foreach ($checkers as $checker) {
            $totalUpdates += count($checker['updates']);
            foreach ($checker['updates'] as $update) {
                if ($update['type'] === 'security') {
                    $hasSecurity = true;
                }
            }
        }

        $machine = Machine::create([
            'hostname' => $hostname,
            'api_token_id' => $token->id,
            'last_report_at' => now()->subMinutes(rand(2, 120)),
            'total_updates' => $totalUpdates,
            'has_security' => $hasSecurity,
            'status' => $status,
        ]);

        $report = Report::create([
            'machine_id' => $machine->id,
            'reported_at' => $machine->last_report_at,
            'total_updates' => $totalUpdates,
            'has_security' => $hasSecurity,
            'created_at' => $machine->last_report_at,
        ]);

        foreach ($checkers as $checkerData) {
            $checkerResult = CheckerResult::create([
                'report_id' => $report->id,
                'name' => $checkerData['checker'],
                'summary' => $checkerData['summary'],
                'update_count' => count($checkerData['updates']),
                'created_at' => $machine->last_report_at,
            ]);

            foreach ($checkerData['updates'] as $update) {
                PackageUpdate::create([
                    'checker_result_id' => $checkerResult->id,
                    ...$update,
                ]);
            }
        }
    }
}
