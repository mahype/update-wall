<x-app-layout>
    <x-slot name="header">Benutzer</x-slot>

    <div class="mb-6 flex justify-between items-center">
        <p class="text-sm text-gray-600">Benutzer verwalten, die sich an der Update Wall anmelden können.</p>
        <a href="{{ route('admin.users.create') }}"
           class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            <svg class="w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Neuer Benutzer
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Name</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">E-Mail</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Rolle</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Erstellt</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $user->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->email }}</td>
                        <td class="px-6 py-4">
                            @if($user->is_admin)
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-indigo-100 text-indigo-700">Admin</span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-700">Benutzer</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $user->created_at->format('d.m.Y') }}</td>
                        <td class="px-6 py-4 text-right text-sm space-x-2">
                            <a href="{{ route('admin.users.edit', $user) }}"
                               class="text-indigo-600 hover:text-indigo-800 font-medium">Bearbeiten</a>
                            @if($user->id !== auth()->id())
                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="inline">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800 font-medium"
                                            onclick="return confirm('Benutzer wirklich löschen?')">Löschen</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-app-layout>
