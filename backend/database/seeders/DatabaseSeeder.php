<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'is_admin' => true,
            'password' => Hash::make('password'),
        ]);

        User::factory()->create([
            'name' => 'User',
            'email' => 'user@example.com',
            'password' => Hash::make('password'),
        ]);
    }
}
