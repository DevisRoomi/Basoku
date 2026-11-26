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
        $email = strtolower(trim((string) env('ADMIN_EMAIL', 'admin@basoku.com')));
        $password = env('ADMIN_PASSWORD');

        if (app()->isProduction() && empty($password)) {
            $this->command?->warn('ADMIN_PASSWORD tidak disetel di produksi. AdminUserSeeder dilewati demi keamanan.');

            return;
        }

        $admin = User::firstOrNew(['email' => $email]);

        if (! $admin->exists) {
            $admin->name = (string) env('ADMIN_NAME', 'Admin Toko');
            $admin->password = Hash::make((string) ($password ?: 'password'));
            $admin->role = 'admin';
            $admin->save();
        } elseif ($admin->role !== 'admin') {
            $admin->role = 'admin';
            $admin->save();
        }
    }
}
