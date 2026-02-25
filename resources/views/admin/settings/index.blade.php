<x-app-layout>
    <x-slot name="header">Einstellungen</x-slot>

    <div class="max-w-lg">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <form method="POST" action="{{ route('admin.settings.update') }}">
                @csrf
                @method('PUT')

                <div class="space-y-6" x-data="{ enabled: {{ $notificationsEnabled ? 'true' : 'false' }} }">
                    {{-- Global enable toggle --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-3">Browser-Benachrichtigungen</h3>
                        <div class="flex items-center">
                            <input type="checkbox" name="notifications_enabled" id="notifications_enabled"
                                   value="1" x-model="enabled"
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <label for="notifications_enabled" class="ml-2 text-sm text-gray-700">
                                Browser-Benachrichtigungen aktivieren
                            </label>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">
                            Wenn deaktiviert, werden keine Browser-Benachrichtigungen ausgelöst.
                        </p>
                    </div>

                    {{-- Per-status toggles --}}
                    <div :class="{ 'opacity-40 pointer-events-none': !enabled }">
                        <h3 class="text-sm font-semibold text-gray-700 mb-1">Benachrichtigen bei Status</h3>
                        <p class="text-xs text-gray-500 mb-3">
                            Wähle aus, bei welchen Statusänderungen eine Benachrichtigung ausgelöst werden soll.
                        </p>
                        <div class="space-y-3">
                            @foreach([
                                'security' => ['label' => 'Sicherheitsupdates', 'description' => 'Maschine hat sicherheitskritische Updates'],
                                'error'    => ['label' => 'Fehler', 'description' => 'Ein Checker-Fehler wurde gemeldet'],
                                'updates'  => ['label' => 'Updates verfügbar', 'description' => 'Reguläre Updates sind verfügbar'],
                                'stale'    => ['label' => 'Keine Meldung', 'description' => 'Maschine hat sich seit 25 Stunden nicht gemeldet'],
                            ] as $value => $info)
                                <div class="flex items-start">
                                    <div class="flex items-center h-5 mt-0.5">
                                        <input type="checkbox" name="notify_statuses[]"
                                               id="status_{{ $value }}" value="{{ $value }}"
                                               {{ in_array($value, $notifyStatuses) ? 'checked' : '' }}
                                               :disabled="!enabled"
                                               class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                                    </div>
                                    <div class="ml-2">
                                        <label for="status_{{ $value }}" class="text-sm text-gray-700 font-medium">
                                            {{ $info['label'] }}
                                        </label>
                                        <p class="text-xs text-gray-500">{{ $info['description'] }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('notify_statuses')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        @error('notify_statuses.*')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end mt-6">
                    <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                        Einstellungen speichern
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
