<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'sutra.conscious@gmail.com'],
            [
                'name' => 'Sutra Conscious',
                'password' => 'sutra@2026',
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
