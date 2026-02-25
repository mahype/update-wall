<x-app-layout>
    <x-slot name="header">Anfragelogs</x-slot>

    <div class="mb-6">
        <p class="text-sm text-gray-600">Alle eingehenden API-Anfragen an <code class="text-xs bg-gray-100 px-1 py-0.5 rounded">POST /api/v1/report</code> — erfolgreiche Reports und fehlgeschlagene Authentifizierungen.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($logs->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <p class="text-lg font-medium">Noch keine Anfragen protokolliert</p>
                <p class="text-sm mt-1">Logs werden ab jetzt bei jeder API-Anfrage gespeichert.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zeit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">IP</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Token</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Hostname</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Detail</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($logs as $log)
                        <tr class="hover:bg-gray-50 cursor-pointer" onclick="window.location='{{ route('admin.logs.show', $log) }}'">
                            <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap" title="{{ $log->created_at->format('d.m.Y H:i:s') }}">
                                {{ $log->created_at->diffForHumans() }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->status === 'success')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Erfolg</span>
                                @elseif($log->status === 'auth_failed')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Auth-Fehler</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ $log->status }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-sm font-mono text-gray-600">{{ $log->ip }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $log->token?->name ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-900">{{ $log->hostname ?? '—' }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                @php
                                    $detailLabels = [
                                        'missing_token'   => 'Kein Token',
                                        'token_not_found' => 'Token unbekannt',
                                        'token_revoked'   => 'Token widerrufen',
                                        'token_expired'   => 'Token abgelaufen',
                                        'new_machine'     => 'Neue Maschine',
                                        'existing_machine'=> 'Bekannte Maschine',
                                    ];
                                @endphp
                                {{ $detailLabels[$log->detail] ?? ($log->detail ?? '—') }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            @if($logs->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $logs->links() }}
                </div>
            @endif
        @endif
    </div>
</x-app-layout>
