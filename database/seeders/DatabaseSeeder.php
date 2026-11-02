<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Daftarkan semua seeder di sini agar dieksekusi secara otomatis
        $this->call([
            AdminUserSeeder::class,
            LocationSeeder::class,
            DummyChargingSeeder::class,
        ]);
    }
}