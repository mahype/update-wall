<x-app-layout>
    <x-slot name="header">API-Tokens</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-sm text-gray-600">Tokens für die Authentifizierung von Update-Watcher Clients.</p>
        <a href="{{ route('admin.tokens.create') }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Neuer Token
        </a>
    </div>

    {{-- Show newly created token --}}
    @if(session('plain_token'))
        <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-6">
            <p class="text-sm font-medium text-green-800 mb-2">Token wurde erstellt. Bitte jetzt kopieren — er wird nicht erneut angezeigt:</p>
            <div x-data="{ copied: false }" class="flex items-center gap-2">
                <code class="flex-1 bg-white px-4 py-2 rounded-lg border border-green-300 text-sm font-mono text-green-900 break-all select-all">{{ session('plain_token') }}</code>
                <button @click="navigator.clipboard.writeText('{{ session('plain_token') }}'); copied = true; setTimeout(() => copied = false, 2000)"
                        class="shrink-0 px-3 py-2 bg-green-600 text-white text-sm rounded-lg hover:bg-green-700 transition">
                    <span x-show="!copied">Kopieren</span>
                    <span x-show="copied" x-cloak>Kopiert!</span>
                </button>
            </div>
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        @if($tokens->isEmpty())
            <div class="p-12 text-center text-gray-500">
                <p class="text-lg font-medium">Keine Tokens vorhanden</p>
                <p class="mt-1 text-sm">Erstellen Sie einen Token, damit sich Update-Watcher authentifizieren können.</p>
            </div>
        @else
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Erstellt von</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Maschinen</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Zuletzt benutzt</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($tokens as $token)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $token->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $token->user->name }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">{{ $token->machines_count }}</td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $token->last_used_at ? $token->last_used_at->diffForHumans() : 'Nie' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($token->revoked_at)
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-700">Widerrufen</span>
                                @elseif($token->expires_at?->isPast())
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">Abgelaufen</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-700">Aktiv</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm space-x-2">
                                @if(!$token->revoked_at)
                                    <form method="POST" action="{{ route('admin.tokens.revoke', $token) }}" class="inline">
                                        @csrf
                                        <button type="submit"
                                                class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-yellow-200 bg-yellow-50 text-yellow-700 hover:bg-yellow-100 transition"
                                                onclick="return confirm('Token wirklich widerrufen?')">Widerrufen</button>
                                    </form>
                                @endif
                                <form method="POST" action="{{ route('admin.tokens.destroy', $token) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit"
                                            class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg border border-red-200 bg-red-50 text-red-700 hover:bg-red-100 transition"
                                            onclick="return confirm('Token unwiderruflich löschen?')">Löschen</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
</x-app-layout>
