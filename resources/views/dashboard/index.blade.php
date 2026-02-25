<x-app-layout>
    <x-slot name="header">Dashboard</x-slot>

    <div x-data="{
        filter: 'all',
        search: '',
        get filteredMachines() {
            return this.$refs.machineRows ? Array.from(this.$refs.machineRows.querySelectorAll('tr[data-status]')) : [];
        }
    }">
        {{-- Summary Cards --}}
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
            <x-stat-card label="Gesamt" :value="$counts['total']" color="indigo"
                         @click="filter = 'all'" ::class="filter === 'all' ? 'ring-2 ring-indigo-500' : ''" />
            <x-stat-card label="Aktuell" :value="$counts['ok']" color="green"
                         @click="filter = 'ok'" ::class="filter === 'ok' ? 'ring-2 ring-green-500' : ''" />
            <x-stat-card label="Updates" :value="$counts['updates']" color="yellow"
                         @click="filter = 'updates'" ::class="filter === 'updates' ? 'ring-2 ring-yellow-500' : ''" />
            <x-stat-card label="Sicherheit" :value="$counts['security']" color="red"
                         @click="filter = 'security'" ::class="filter === 'security' ? 'ring-2 ring-red-500' : ''" />
            <x-stat-card label="Keine Meldung" :value="$counts['stale']" color="gray"
                         @click="filter = 'stale'" ::class="filter === 'stale' ? 'ring-2 ring-gray-400' : ''" />
        </div>

        {{-- Search --}}
        <div class="mb-4">
            <input type="text" x-model="search" placeholder="Hostname suchen..."
                   class="w-full sm:w-80 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
        </div>

        {{-- Machine Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            @if($machines->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <svg class="mx-auto h-12 w-12 text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 14.25h13.5m-13.5 0a3 3 0 01-3-3m3 3a3 3 0 100 6h13.5a3 3 0 100-6m-16.5-3a3 3 0 013-3h13.5a3 3 0 013 3m-19.5 0a4.5 4.5 0 01.9-2.7L5.737 5.1a3.375 3.375 0 012.7-1.35h7.126c1.062 0 2.062.5 2.7 1.35l2.587 3.45a4.5 4.5 0 01.9 2.7m0 0a3 3 0 01-3 3m0 3h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008zm-3 6h.008v.008h-.008v-.008zm0-6h.008v.008h-.008v-.008z" />
                    </svg>
                    <p class="text-lg font-medium">Noch keine Maschinen registriert</p>
                    <p class="mt-1 text-sm">Maschinen werden automatisch beim ersten Update-Report registriert.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hostname</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Updates</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Letzter Check</th>
                            </tr>
                        </thead>
                        <tbody x-ref="machineRows" class="bg-white divide-y divide-gray-200">
                            @foreach($machines as $machine)
                                <tr data-status="{{ $machine->status->value }}"
                                    data-hostname="{{ strtolower($machine->hostname) }}"
                                    x-show="(filter === 'all' || '{{ $machine->status->value }}' === filter) && (search === '' || '{{ strtolower($machine->hostname) }}'.includes(search.toLowerCase()))"
                                    class="hover:bg-gray-50 cursor-pointer transition"
                                    onclick="window.location='{{ route('machines.show', $machine) }}'">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <x-status-badge :status="$machine->status" />
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $machine->display_name ?? $machine->hostname }}</div>
                                        @if($machine->display_name)
                                            <div class="text-xs text-gray-500">{{ $machine->hostname }}</div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-semibold {{ $machine->total_updates > 0 ? ($machine->has_security ? 'text-red-600' : 'text-yellow-600') : 'text-green-600' }}">
                                            {{ $machine->total_updates }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 hidden sm:table-cell">
                                        @if($machine->last_report_at)
                                            <span title="{{ $machine->last_report_at->format('d.m.Y H:i:s') }}">
                                                {{ $machine->last_report_at->diffForHumans() }}
                                            </span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
