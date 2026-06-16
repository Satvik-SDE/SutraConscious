<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::where('email', 'sutra.conscious@gmail.com')->delete();

        User::updateOrCreate(
            ['email' => 'sutraconscious@gmail.com'],
            [
                'name' => 'Sutra Conscious',
                'password' => 'Maschendra@12',
                'is_admin' => true,
                'email_verified_at' => now(),
            ],
        );
    }
}
