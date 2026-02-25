<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateUserCommand extends Command
{
    protected $signature = 'user:create
                            {--name= : User name}
                            {--email= : User email}
                            {--password= : User password}
                            {--admin : Make user an admin}';

    protected $description = 'Create a new user account';

    public function handle(): int
    {
        $name = $this->option('name') ?? $this->ask('Name');
        $email = $this->option('email') ?? $this->ask('E-Mail');
        $password = $this->option('password') ?? $this->secret('Passwort');
        $isAdmin = $this->option('admin') || $this->confirm('Administrator?', false);

        if (User::where('email', $email)->exists()) {
            $this->error("E-Mail \"{$email}\" ist bereits vergeben.");
            return self::FAILURE;
        }

        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => $isAdmin,
        ]);

        $this->info("Benutzer \"{$user->name}\" (ID: {$user->id}) wurde erstellt.");

        return self::SUCCESS;
    }
}
