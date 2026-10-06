<?php

namespace Database\Seeders;

use App\Models\User;
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
        // Memanggil seeder admin yang sudah Anda buat
        $this->call([
            AdminUserSeeder::class,
            // LocationSeeder::class, // Jika ada seeder lain bisa ditambahkan di sini
        ]);

        // Atau jika ingin tetap menggunakan factory bawaan, bisa ditaruh di bawahnya:
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}