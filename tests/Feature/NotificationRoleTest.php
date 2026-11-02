<?php

namespace Tests\Feature;

use App\Models\Charger;
use App\Models\Location;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class NotificationRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_sees_operational_notifications_and_returns_to_admin_dashboard(): void
    {
        $admin = $this->createUser('admin', 'notification-admin@example.com');
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Perawatan',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'status' => 'perawatan',
        ]);
        Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'CH-ADMIN-OFFLINE',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'is_online' => false,
            'status' => 'rusak',
        ]);

        $response = $this->actingAs($admin)->get(route('notifications.index'));

        $response->assertOk()
            ->assertSee(route('admin.dashboard'), false)
            ->assertSee('Dashboard Super Admin')
            ->assertSee('Charger CH-ADMIN-OFFLINE perlu diperiksa')
            ->assertSee('SPKLU Perawatan')
            ->assertSee('Status lokasi SPKLU Perawatan berubah');
    }

    public function test_driver_sees_personal_notifications_and_returns_to_user_dashboard(): void
    {
        $driver = $this->createUser('pengemudi', 'notification-driver@example.com');
        Notification::create([
            'user_id' => $driver->id_user,
            'title' => 'Pembayaran berhasil',
            'message' => 'Pembayaran sesi Anda berhasil.',
            'is_read' => false,
        ]);

        $response = $this->actingAs($driver)->get(route('notifications.index'));

        $response->assertOk()
            ->assertSee(route('dashboard'), false)
            ->assertSee('Dashboard')
            ->assertSee('Pembayaran berhasil')
            ->assertSee('Pembayaran sesi Anda berhasil.');
    }

    public function test_driver_with_low_balance_gets_a_top_up_recommendation(): void
    {
        $driver = $this->createUser('pengemudi', 'notification-low-balance@example.com', 10000);

        $response = $this->actingAs($driver)->get(route('notifications.index'));

        $response->assertOk()
            ->assertSee('Saldo Anda mulai menipis')
            ->assertSee('Saldo Anda saat ini Rp 10.000')
            ->assertSee(route('topup.index'), false)
            ->assertSee('Isi saldo sekarang')
            ->assertSee('Saldo perlu diisi')
            ->assertSee('data-notification-time')
            ->assertDontSee('seconds ago');
    }

    public function test_driver_gets_personalized_station_and_points_recommendations(): void
    {
        $driver = $this->createUser('pengemudi', 'notification-recommendations@example.com', 50000, 5);

        $response = $this->actingAs($driver)->get(route('notifications.index'));

        $response->assertOk()
            ->assertSee('Temukan SPKLU di sekitar Anda')
            ->assertSee(route('locations.index'), false)
            ->assertSee('Lihat lokasi SPKLU')
            ->assertSee('Poin Anda bisa ditukar')
            ->assertSee('5 poin')
            ->assertSee(route('points.index'), false)
            ->assertSee('Tukar poin')
            ->assertDontSee('Saldo Anda mulai menipis');
    }

    private function createUser(string $role, string $email, int $balance = 0, int $points = 0): User
    {
        return User::create([
            'nama' => ucfirst($role),
            'email' => $email,
            'password' => Hash::make('password123'),
            'peran' => $role,
            'status_akun' => 'aktif',
            'saldo' => $balance,
            'points' => $points,
        ]);
    }
}
