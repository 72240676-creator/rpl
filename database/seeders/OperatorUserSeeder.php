<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class OperatorUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'operator@gmail.com'],
            [
                'nama' => 'Operator EV',
                'nomor_telepon' => '081234567891',
                'password' => Hash::make('operator123'),
                'peran' => 'operator',
                'status_akun' => 'aktif',
            ]
        );
    }
}