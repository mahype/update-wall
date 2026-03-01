<?php

namespace App\Http\Controllers;

use App\Models\Machine;
use App\Models\Report;
use App\Models\Tag;
use Illuminate\Http\Request;

class MachineController extends Controller
{
    public function show(Machine $machine, ?int $reportId = null)
    {
        $report = $reportId
            ? Report::where('machine_id', $machine->id)->findOrFail($reportId)
            : $machine->latestReport;

        $report?->load(['checkerResults' => function ($query) {
            $query->orderByDesc('update_count');
        }, 'checkerResults.packageUpdates' => function ($query) {
            $query->orderByRaw("
                CASE priority
                    WHEN 'critical' THEN 1
                    WHEN 'high' THEN 2
                    WHEN 'normal' THEN 3
                    WHEN 'low' THEN 4
                    ELSE 5
                END
            ");
        }]);

        $reports = $machine->reports()->select('id', 'reported_at', 'total_updates')->limit(20)->get();

        $machine->load('tags');
        $allTags = Tag::orderBy('name')->get();

        return view('machines.show', compact('machine', 'report', 'reports', 'allTags'));
    }

    public function attachTag(Request $request, Machine $machine)
    {
        $request->validate(['tag_id' => 'required|exists:tags,id']);
        $machine->tags()->syncWithoutDetaching([$request->tag_id]);

        return redirect()->route('machines.show', $machine)
            ->with('success', 'Tag zugewiesen.');
    }

    public function detachTag(Request $request, Machine $machine)
    {
        $request->validate(['tag_id' => 'required|exists:tags,id']);
        $machine->tags()->detach($request->tag_id);

        return redirect()->route('machines.show', $machine)
            ->with('success', 'Tag entfernt.');
    }
}
