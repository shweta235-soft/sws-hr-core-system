<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $roles = ['Admin', 'HR', 'Accounts', 'Manager', 'Employee'];

        foreach ($roles as $role) {
            User::create([
                'name' => "Test " . $role,
                'email' => strtolower($role) . "@hrms.com",
                'password' => Hash::make('password123'),
                'role' => $role,
            ]);
        }
    }
}
