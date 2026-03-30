<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate([
            'name' => 'Admin',
            'email' => 'admin@admin.com',
            'role' => 'admin',
            'is_active' => true,
            'password' => Hash::make('password'),
        ]);
    }
}
