<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name'     => 'Ayyubi',
            'email'    => 'Ayyubi@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'user',
        ]);

        User::create([
            'name'     => 'Danish',
            'email'    => 'Danish@gmail.com',
            'password' => Hash::make('password'),
            'role'     => 'staff',
        ]);
    }
}
