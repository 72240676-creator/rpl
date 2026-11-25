<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class MateriModelTest extends TestCase
{
    use RefreshDatabase;

    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'nama' => 'Pengguna Praktikum',
            'email' => 'pengguna@example.com',
            'password' => Hash::make('password123'),
            'nomor_telepon' => '081234567890',
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
            'saldo' => 50000,
            'poin' => 0,
        ], $overrides));
    }

    /**
     * Skenario 1: Validasi form gagal jika format salah / password kurang dari 8 karakter.
     */
    public function test_login_gagal_jika_validasi_tidak_memenuhi(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'format-salah',
            'password' => '123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    /**
     * Skenario 2: Login gagal jika email tidak terdaftar.
     */
    public function test_login_gagal_jika_email_tidak_ditemukan(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'tidakada@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Skenario 3: Login gagal jika password salah.
     */
    public function test_login_gagal_jika_password_salah(): void
    {
        $user = $this->createUser([
            'email' => 'user1@example.com',
            'password' => Hash::make('password123'),
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'user1@example.com',
            'password' => 'salahbanget',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Skenario 4: Login berhasil sebagai Pengemudi (User).
     */
    public function test_login_berhasil_sebagai_user(): void
    {
        $user = $this->createUser([
            'email' => 'user2@example.com',
            'peran' => 'pengemudi',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'user2@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    /**
     * Skenario 5: Login berhasil sebagai Operator.
     */
    public function test_login_berhasil_sebagai_operator(): void
    {
        $operator = $this->createUser([
            'email' => 'operator@example.com',
            'peran' => 'operator',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'operator@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('operator.dashboard'));
        $this->assertAuthenticatedAs($operator);
    }

    /**
     * Skenario 6: Login berhasil sebagai Admin.
     */
    public function test_login_berhasil_sebagai_admin(): void
    {
        $admin = $this->createUser([
            'email' => 'admin@example.com',
            'peran' => 'admin',
        ]);

        $response = $this->post(route('login'), [
            'email' => 'admin@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }
}
