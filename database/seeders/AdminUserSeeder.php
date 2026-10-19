<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@basoku.com'],
            [
                'name' => 'Admin Toko',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
    }
}
