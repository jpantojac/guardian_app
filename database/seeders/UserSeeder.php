<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'Admin User',
            'email' => 'admin@guardianapp-project.org',
            'password' => 'Password123!',
            'role' => 'admin',
            'is_active' => true,
            'consent_at' => now(),
        ]);

        User::create([
            'name' => 'Moderator User',
            'email' => 'moderator@guardianapp-project.org',
            'password' => 'Password123!',
            'role' => 'moderator',
            'is_active' => true,
            'consent_at' => now(),
        ]);

        User::create([
            'name' => 'Citizen User',
            'email' => 'ciudadano@guardianapp-project.org',
            'password' => 'Password123!',
            'role' => 'user',
            'is_active' => true,
            'consent_at' => now(),
        ]);
    }
}
