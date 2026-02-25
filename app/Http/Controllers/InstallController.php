<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class InstallController extends Controller
{
    public function show()
    {
        return view('install.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255'],
            'password'              => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if (empty(config('app.key'))) {
            Artisan::call('key:generate', ['--force' => true]);
        }

        touch(database_path('database.sqlite'));

        try {
            Artisan::call('migrate', ['--force' => true]);

            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
                'is_admin' => true,
            ]);

            $user->email_verified_at = now();
            $user->save();

            Auth::login($user);
        } catch (\Exception $e) {
            return back()->withErrors(['general' => 'Installation fehlgeschlagen: ' . $e->getMessage()]);
        }

        return redirect()->route('dashboard')->with('success', 'Installation erfolgreich!');
    }
}
