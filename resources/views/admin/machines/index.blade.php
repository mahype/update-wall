<x-app-layout>
    <x-slot name="header">Maschinen-Verwaltung</x-slot>

    <div class="mb-6">
        <p class="text-sm text-gray-600">Alle registrierten Maschinen. Maschinen werden automatisch beim ersten Report erstellt.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($machines->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <p class="text-lg font-medium">Keine Maschinen registriert</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hostname</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Token</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Reports</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Letzter Check</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($machines as $machine)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4"><x-status-badge :status="$machine->status" /></td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                <a href="{{ route('machines.show', $machine) }}" class="hover:text-indigo-600">{{ $machine->hostname }}</a>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $machine->apiToken?->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $machine->reports_count }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $machine->last_report_at ? $machine->last_report_at->diffForHumans() : '-' }}
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <form method="POST" action="{{ route('admin.machines.destroy', $machine) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium"
                                            onclick="return confirm('Maschine und alle Reports löschen?')">Löschen</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
