<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-3">
            <a href="{{ route('dashboard') }}" class="text-gray-400 hover:text-gray-600">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
            </a>
            {{ $machine->display_name ?? $machine->hostname }}
        </div>
    </x-slot>

    {{-- Machine Header --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <x-status-badge :status="$machine->status" />
                <div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $machine->hostname }}</h2>
                    @if($machine->display_name)
                        <p class="text-sm text-gray-500">{{ $machine->display_name }}</p>
                    @endif
                </div>
            </div>
            <div class="flex flex-wrap gap-6 text-sm text-gray-500">
                <div>
                    <span class="font-medium text-gray-700">Updates:</span>
                    <span class="font-bold {{ $machine->total_updates > 0 ? ($machine->has_security ? 'text-red-600' : 'text-yellow-600') : 'text-green-600' }}">
                        {{ $machine->total_updates }}
                    </span>
                </div>
                <div>
                    <span class="font-medium text-gray-700">Letzter Check:</span>
                    @if($machine->last_report_at)
                        <span title="{{ $machine->last_report_at->format('d.m.Y H:i:s') }}">
                            {{ $machine->last_report_at->diffForHumans() }}
                        </span>
                    @else
                        <span class="text-gray-400">-</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Report Selector --}}
    @if($reports->count() > 1)
        <div class="mb-6">
            <label for="report-select" class="text-sm font-medium text-gray-700 mr-2">Report:</label>
            <select id="report-select"
                    onchange="window.location = this.value"
                    class="rounded-lg border-gray-300 text-sm shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @foreach($reports as $r)
                    <option value="{{ route('machines.report', [$machine, $r->id]) }}"
                            {{ $report && $r->id === $report->id ? 'selected' : '' }}>
                        {{ $r->reported_at->format('d.m.Y H:i') }} ({{ $r->total_updates }} Updates)
                    </option>
                @endforeach
            </select>
        </div>
    @endif

    {{-- Checker Results --}}
    @if($report)
        <div class="space-y-3" x-data="{ openChecker: null }">
            @foreach($report->checkerResults as $checker)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    {{-- Checker Header --}}
                    <button @click="openChecker = openChecker === {{ $checker->id }} ? null : {{ $checker->id }}"
                            class="w-full flex items-center justify-between px-6 py-4 text-left hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5 text-gray-400 transition-transform duration-200"
                                 :class="openChecker === {{ $checker->id }} ? 'rotate-90' : ''"
                                 fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                            </svg>
                            <span class="font-semibold text-gray-900 uppercase text-sm">{{ $checker->name }}</span>
                            @if($checker->update_count > 0)
                                <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-indigo-100 text-indigo-700 text-xs font-bold">
                                    {{ $checker->update_count }}
                                </span>
                            @endif
                            @if($checker->hasError())
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Fehler</span>
                            @endif
                        </div>
                        <span class="text-sm text-gray-500 hidden sm:inline">{{ Str::limit($checker->summary, 60) }}</span>
                    </button>

                    {{-- Checker Details --}}
                    <div x-show="openChecker === {{ $checker->id }}"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         x-cloak>
                        <div class="border-t border-gray-200">
                            {{-- Summary --}}
                            <div class="px-6 py-3 bg-gray-50 text-sm text-gray-600">
                                {{ $checker->summary }}
                            </div>

                            @if($checker->hasError())
                                <div class="px-6 py-3 bg-red-50 text-sm text-red-700">
                                    <strong>Fehler:</strong> {{ $checker->error }}
                                </div>
                            @endif

                            @if($checker->update_hint && $checker->update_count > 0)
                                <div class="px-6 py-3 bg-indigo-50 border-b border-indigo-100">
                                    <p class="text-xs font-medium text-indigo-600 uppercase mb-1">Update-Befehl</p>
                                    <code class="block text-sm text-indigo-900 font-mono bg-indigo-100 rounded px-3 py-2 select-all">{{ $checker->update_hint }}</code>
                                </div>
                            @endif

                            {{-- Package Updates Table --}}
                            @if($checker->packageUpdates->isNotEmpty())
                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Paket</th>
                                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Aktuell</th>
                                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Neu</th>
                                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Typ</th>
                                                <th class="px-6 py-2 text-left text-xs font-medium text-gray-500 uppercase">Priorität</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-100">
                                            @foreach($checker->packageUpdates as $update)
                                                <tr class="hover:bg-gray-50">
                                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $update->name }}</td>
                                                    <td class="px-6 py-3 text-sm text-gray-500 font-mono">{{ $update->current_version ?? '–' }}</td>
                                                    <td class="px-6 py-3 text-sm text-gray-900 font-mono">{{ $update->new_version }}</td>
                                                    <td class="px-6 py-3"><x-type-badge :type="$update->type" /></td>
                                                    <td class="px-6 py-3"><x-priority-badge :priority="$update->priority" /></td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @else
                                <div class="px-6 py-4 text-sm text-gray-500">Keine Updates vorhanden.</div>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-12 text-center text-gray-500">
            <p class="text-lg font-medium">Noch keine Reports vorhanden</p>
            <p class="mt-1 text-sm">Der Update-Watcher hat noch keinen Report gesendet.</p>
        </div>
    @endif
</x-app-layout>
