<?php

namespace Tests\Feature;

use App\Models\Charger;
use App\Models\Location;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_dashboard_uses_station_and_charger_data_created_by_operator(): void
    {
        $admin = User::create([
            'nama' => 'Admin',
            'email' => 'admin-live-dashboard@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);
        $operator = User::create([
            'nama' => 'Operator',
            'email' => 'operator-live-dashboard@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'operator',
            'status_akun' => 'aktif',
        ]);

        $this->actingAs($operator)
            ->post(route('operator.stations.store'), [
                'nama_lokasi' => 'SPKLU Baru dari Operator',
                'alamat' => 'Jl. Merdeka',
                'latitude' => -6.2,
                'longitude' => 106.8,
                'jam_operasional' => '24 Jam',
                'fasilitas' => 'Toilet',
                'status' => 'aktif',
            ])
            ->assertRedirect();

        $secondLocation = Location::create([
            'nama_lokasi' => 'SPKLU Kedua',
            'alamat' => 'Jl. Sudirman',
            'latitude' => -6.21,
            'longitude' => 106.81,
            'jam_operasional' => '24 Jam',
            'status' => 'perawatan',
        ]);

        $this->createCharger($secondLocation, 'CH-ONLINE', true, 'tersedia');
        $this->createCharger($secondLocation, 'CH-OFFLINE', false, 'tersedia');
        $this->createCharger($secondLocation, 'CH-BROKEN', true, 'rusak');

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('id="admin-total-stations">2<', false)
            ->assertSee('id="admin-total-chargers">3<', false)
            ->assertSee('id="admin-online-chargers">1<', false)
            ->assertSee('id="admin-attention-chargers" style="display:block;color:#ea580c;">2<', false)
            ->assertSee('href="' . route('locations.index') . '" aria-label="Lihat daftar stasiun SPKLU"', false)
            ->assertSee('aria-label="Lihat stasiun dan unit charger"', false)
            ->assertSee('aria-label="Lihat ketersediaan charger di stasiun"', false)
            ->assertSee('Klik untuk melihat unit charger')
            ->assertSee('id="admin-attention-toggle"', false)
            ->assertSee('aria-controls="admin-inspection-reports"', false)
            ->assertSee('id="admin-inspection-reports" aria-labelledby="inspection-reports-heading" hidden', false)
            ->assertSee('1 unit · 33%')
            ->assertSee('Belum ada aktivitas charging.');
    }

    public function test_admin_can_update_and_resolve_operator_charger_report_and_operator_sees_changes(): void
    {
        $admin = User::create([
            'nama' => 'Admin Pemeriksa',
            'email' => 'admin-inspection@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);
        $operator = User::create([
            'nama' => 'Operator Pelapor',
            'email' => 'operator-inspection@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'operator',
            'status_akun' => 'aktif',
        ]);
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Laporan Pemeriksaan',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
        $charger = $this->createCharger($location, 'CH-REPORTED', true, 'tersedia');

        $this->actingAs($operator)
            ->from(route('operator.dashboard'))
            ->put(route('operator.chargers.inspection.update', $charger->id_charger), [
                'needs_inspection' => 1,
                'inspection_note' => 'Konektor terasa longgar.',
            ])
            ->assertRedirect(route('operator.dashboard'));

        $this->actingAs($admin)
            ->get(route('admin.dashboard'))
            ->assertOk()
            ->assertSee('Laporan Pemeriksaan Charger')
            ->assertSee('id="admin-inspection-reports" aria-labelledby="inspection-reports-heading" hidden', false)
            ->assertSee('CH-REPORTED')
            ->assertSee('Konektor terasa longgar.')
            ->assertSee('Perbarui catatan');

        $this->from(route('admin.dashboard'))
            ->put(route('admin.chargers.inspection.update', $charger->id_charger), [
                'needs_inspection' => 1,
                'inspection_note' => 'Konektor sudah diperiksa, pin perlu diganti.',
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('chargers', [
            'id_charger' => $charger->id_charger,
            'needs_inspection' => true,
            'inspection_note' => 'Konektor sudah diperiksa, pin perlu diganti.',
            'inspection_reported_by' => $operator->id_user,
        ]);

        $this->actingAs($operator)
            ->get(route('operator.dashboard.live'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $charger->id_charger,
                'needs_inspection' => true,
                'note' => 'Konektor sudah diperiksa, pin perlu diganti.',
                'reporter' => 'Operator Pelapor',
            ]);

        $this->actingAs($admin)
            ->from(route('admin.dashboard'))
            ->put(route('admin.chargers.inspection.update', $charger->id_charger), [
                'needs_inspection' => 0,
            ])
            ->assertRedirect(route('admin.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('chargers', [
            'id_charger' => $charger->id_charger,
            'needs_inspection' => false,
            'inspection_note' => null,
            'inspection_reported_by' => null,
        ]);

        $this->actingAs($operator)
            ->get(route('operator.dashboard.live'))
            ->assertOk()
            ->assertJsonFragment([
                'id' => $charger->id_charger,
                'needs_inspection' => false,
                'note' => null,
                'reporter' => null,
            ])
            ->assertJsonPath('stats.attention_chargers', 0);
    }

    private function createCharger(
        Location $location,
        string $deviceNumber,
        bool $online,
        string $status,
    ): Charger {
        return Charger::create([
            'id_location' => $location->id_location,
            'device_number' => $deviceNumber,
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'is_online' => $online,
            'status' => $status,
        ]);
    }
}
