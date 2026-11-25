<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthLoginScenarioTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Helper untuk membuat user uji coba dengan peran tertentu.
     */
    private function createUser(array $overrides = []): User
    {
        return User::create(array_merge([
            'nama' => 'Pengguna Uji',
            'email' => 'pengguna_test_' . uniqid() . '@example.com',
            'password' => Hash::make('password123'),
            'nomor_telepon' => '081234567890',
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
            'saldo' => 50000,
            'poin' => 10,
        ], $overrides));
    }

    /**
     * Skenario 1: Validasi form gagal jika format email salah atau password kurang dari 8 karakter.
     */
    public function test_login_gagal_jika_validasi_input_tidak_memenuhi(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'format-email-salah',
            'password' => '123', // kurang dari 8 karakter
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email', 'password']);
        $this->assertGuest();
    }

    /**
     * Skenario 2: Login gagal jika email tidak terdaftar di sistem.
     */
    public function test_login_gagal_jika_email_tidak_ditemukan(): void
    {
        $response = $this->from(route('login'))->post(route('login'), [
            'email' => 'email_palsu_tidak_ada@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Skenario 3: Login gagal jika password yang dimasukkan salah.
     */
    public function test_login_gagal_jika_password_salah(): void
    {
        $user = $this->createUser([
            'email' => 'driver_salah_pwd@example.com',
            'password' => Hash::make('password_benar_123'),
        ]);

        $response = $this->from(route('login'))->post(route('login'), [
            'email' => $user->email,
            'password' => 'password_keliru_999',
        ]);

        $response->assertRedirect(route('login'));
        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Skenario 4: Login berhasil sebagai Pengemudi (Driver) dan diarahkan ke Dashboard User.
     */
    public function test_login_berhasil_sebagai_pengemudi(): void
    {
        $driver = $this->createUser([
            'email' => 'driver_sukses@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
        ]);

        $response = $this->post(route('login'), [
            'email' => $driver->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('dashboard'));
        $this->assertAuthenticatedAs($driver);
    }

    /**
     * Skenario 5: Login berhasil sebagai Operator dan diarahkan ke Dashboard Operator.
     */
    public function test_login_berhasil_sebagai_operator(): void
    {
        $operator = $this->createUser([
            'email' => 'operator_sukses@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'operator',
        ]);

        $response = $this->post(route('login'), [
            'email' => $operator->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('operator.dashboard'));
        $this->assertAuthenticatedAs($operator);
    }

    /**
     * Skenario 6: Login berhasil sebagai Admin dan diarahkan ke Dashboard Admin.
     */
    public function test_login_berhasil_sebagai_admin(): void
    {
        $admin = $this->createUser([
            'email' => 'admin_sukses@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
        ]);

        $response = $this->post(route('login'), [
            'email' => $admin->email,
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($admin);
    }
}
