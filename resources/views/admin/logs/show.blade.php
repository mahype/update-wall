<x-app-layout>
    <x-slot name="header">Anfrage-Detail</x-slot>

    <div class="mb-4">
        <a href="{{ route('admin.logs.index') }}" class="text-sm text-indigo-600 hover:text-indigo-800">&larr; Zurück zur Übersicht</a>
    </div>

    {{-- Metadaten --}}
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-6">
        <dl class="grid grid-cols-2 md:grid-cols-3 gap-4">
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Zeit</dt>
                <dd class="mt-1 text-sm text-gray-900" title="{{ $log->created_at->format('d.m.Y H:i:s') }}">
                    {{ $log->created_at->diffForHumans() }}
                    <span class="text-gray-400 text-xs ml-1">({{ $log->created_at->format('d.m.Y H:i:s') }})</span>
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Status</dt>
                <dd class="mt-1">
                    @if($log->status === 'success')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">Erfolg</span>
                    @elseif($log->status === 'auth_failed')
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">Auth-Fehler</span>
                    @else
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-yellow-100 text-yellow-800">{{ $log->status }}</span>
                    @endif
                </dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">IP-Adresse</dt>
                <dd class="mt-1 text-sm font-mono text-gray-900">{{ $log->ip }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Token</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $log->token?->name ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Hostname</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $log->hostname ?? '—' }}</dd>
            </div>
            <div>
                <dt class="text-xs font-medium text-gray-500 uppercase">Detail</dt>
                <dd class="mt-1 text-sm text-gray-900">
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
                </dd>
            </div>
        </dl>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- Payload --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700">Request-Payload</h2>
            </div>
            <div class="p-4">
                @if($log->payload)
                    <pre class="text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap break-words font-mono leading-relaxed">{{ json_encode($log->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-sm text-gray-400 italic">Kein Payload gespeichert</p>
                @endif
            </div>
        </div>

        {{-- Response --}}
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                <h2 class="text-sm font-semibold text-gray-700">Server-Antwort</h2>
            </div>
            <div class="p-4">
                @if($log->response)
                    <pre class="text-xs text-gray-800 overflow-x-auto whitespace-pre-wrap break-words font-mono leading-relaxed">{{ json_encode($log->response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                @else
                    <p class="text-sm text-gray-400 italic">Keine Antwort gespeichert</p>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
