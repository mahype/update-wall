<x-guest-layout>
    <div class="mb-4 text-center">
        <h2 class="text-lg font-semibold text-gray-800">Update Wall installieren</h2>
        <p class="mt-1 text-sm text-gray-600">
            Dies ist die Erstinstallation. Der erste Nutzer wird automatisch zum Administrator.
        </p>
    </div>

    @if ($errors->has('general'))
        <div class="mb-4 p-3 bg-red-50 border border-red-300 text-red-700 text-sm rounded">
            {{ $errors->first('general') }}
        </div>
    @endif

    <form method="POST" action="{{ route('install.store') }}">
        @csrf

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- E-Mail -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('E-Mail-Adresse')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="email" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Passwort -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Passwort')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Passwort bestätigen -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Passwort bestätigen')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                {{ __('Installation starten') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
