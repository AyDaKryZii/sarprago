<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Staff (3)
        for ($i = 1; $i <= 3; $i++) {
            User::create([
                'name'     => "Staff $i",
                'email'    => "staff$i@example.com",
                'password' => Hash::make('password'),
                'role'     => 'staff',
            ]);
        }

        // User / Peminjam (30)
        for ($i = 1; $i <= 30; $i++) {
            User::create([
                'name'     => "User $i",
                'email'    => "user$i@example.com",
                'password' => Hash::make('password'),
                'role'     => 'user',
            ]);
        }
    }
}
