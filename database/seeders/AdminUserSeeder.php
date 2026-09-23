<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'nama' => 'Super Admin',
            'email' => 'admin@gmail.com',
            'nomor_telepon' => '081234567890',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);
    }
}