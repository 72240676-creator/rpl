<?php

namespace Database\Seeders;

<<<<<<< HEAD
=======
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
>>>>>>> 0ca36b79a6200b4e5f27905baa1fbc4ba7c56ca3
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
<<<<<<< HEAD
=======
    use WithoutModelEvents;

>>>>>>> 0ca36b79a6200b4e5f27905baa1fbc4ba7c56ca3
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
<<<<<<< HEAD
        // Memanggil seeder admin yang sudah Anda buat
        $this->call([
            AdminUserSeeder::class,
            // LocationSeeder::class, // Jika ada seeder lain bisa ditambahkan di sini
        ]);
    }
}
=======
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }
}
>>>>>>> 0ca36b79a6200b4e5f27905baa1fbc4ba7c56ca3
