<?php

namespace App\Http\Controllers;

use App\Enums\MachineStatus;
use App\Models\Machine;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Summary counts — always unfiltered
        $counts = [
            'total'    => Machine::count(),
            'ok'       => Machine::where('status', 'ok')->count(),
            'updates'  => Machine::where('status', 'updates')->count(),
            'security' => Machine::where('status', 'security')->count(),
            'stale'    => Machine::where('status', 'stale')->count(),
        ];

        // Page size from cookie, clamped 5–100
        $perPage = (int) ($request->query('per_page') ?? $request->cookie('dashboard_per_page', 20));
        $perPage = min(max($perPage, 5), 100);

        // Build query with status-priority ordering
        $query = Machine::query()
            ->orderByRaw("
                CASE status
                    WHEN 'security' THEN 1
                    WHEN 'error' THEN 2
                    WHEN 'updates' THEN 3
                    WHEN 'stale' THEN 4
                    WHEN 'ok' THEN 5
                    ELSE 6
                END
            ");

        // Status filter
        $status = $request->query('status');
        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        // Search filter
        $search = $request->query('search');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('hostname', 'like', "%{$search}%")
                  ->orWhere('display_name', 'like', "%{$search}%");
            });
        }

        // Tag filter (AND logic — machine must have ALL selected tags)
        $tagIds = array_filter(array_map('intval', (array) $request->query('tags', [])));
        if (!empty($tagIds)) {
            foreach ($tagIds as $tagId) {
                $query->whereHas('tags', fn ($q) => $q->where('tags.id', $tagId));
            }
        }

        // Eager-load tags, paginate, preserve query string
        $machines = $query->with('tags')->paginate($perPage)->withQueryString();

        // All tags for filter UI
        $allTags = Tag::orderBy('name')->get();

        // Active filter state for the view
        $activeFilters = [
            'status'   => $status ?? 'all',
            'search'   => $search ?? '',
            'tags'     => $tagIds,
            'per_page' => $perPage,
        ];

        return view('dashboard.index', compact('machines', 'counts', 'allTags', 'activeFilters'));
    }

    public function status(): JsonResponse
    {
        $machines = Machine::select('id', 'hostname', 'display_name', 'status')
            ->get()
            ->map(fn ($m) => [
                'id'           => $m->id,
                'name'         => $m->display_name ?? $m->hostname,
                'status'       => $m->status->value,
                'status_label' => $m->status->label(),
            ]);

        return response()->json(['machines' => $machines]);
    }
}
