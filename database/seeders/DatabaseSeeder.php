<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Memanggil seeder admin yang sudah Anda buat
        $this->call([
            AdminUserSeeder::class,
            // LocationSeeder::class, // Jika ada seeder lain bisa ditambahkan di sini
        ]);
    }
}