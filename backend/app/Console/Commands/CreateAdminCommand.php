<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminCommand extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Creates an admin user.';

    public function handle(): void
    {
        $email = $this->ask('email');
        $name = $this->ask('name');
        $password = $this->ask('password');

        $emailValidator = Validator::make(compact('email'), [
            'email' => ['required', 'email', 'max:255'],
        ]);

        if ($emailValidator->fails()) {
            $this->error($emailValidator->errors());
            return;
        }

        User::query()->create([
            'email' => $email,
            'name' => $name,
            'password' => Hash::make($password),
        ]);

        $this->info('Admin is created successfully.');
    }
}
