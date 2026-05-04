<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@tradex.local'],
            [
                'name' => 'Admin',
                'password' => Hash::make('trade@#!123'),
                'is_admin' => true,
            ]
        );
    }
}
