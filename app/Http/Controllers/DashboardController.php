<?php

namespace App\Http\Controllers;

use App\Enums\MachineStatus;
use App\Models\Machine;

class DashboardController extends Controller
{
    public function index()
    {
        $machines = Machine::orderByRaw("
            CASE status
                WHEN 'security' THEN 1
                WHEN 'error' THEN 2
                WHEN 'updates' THEN 3
                WHEN 'stale' THEN 4
                WHEN 'ok' THEN 5
                ELSE 6
            END
        ")->get();

        $counts = [
            'total' => $machines->count(),
            'ok' => $machines->where('status', MachineStatus::Ok)->count(),
            'updates' => $machines->where('status', MachineStatus::Updates)->count(),
            'security' => $machines->where('status', MachineStatus::Security)->count(),
            'stale' => $machines->where('status', MachineStatus::Stale)->count(),
        ];

        return view('dashboard.index', compact('machines', 'counts'));
    }
}
