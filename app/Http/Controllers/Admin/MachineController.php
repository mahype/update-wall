<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Machine;

class MachineController extends Controller
{
    public function index()
    {
        $machines = Machine::with('apiToken')
            ->withCount('reports')
            ->orderBy('hostname')
            ->get();

        return view('admin.machines.index', compact('machines'));
    }

    public function destroy(Machine $machine)
    {
        $hostname = $machine->hostname;
        $machine->delete();

        return redirect()->route('admin.machines.index')
            ->with('success', "Maschine \"{$hostname}\" wurde gelöscht.");
    }
}
