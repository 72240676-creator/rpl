<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Location;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class OperatorLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_login_redirects_to_operator_dashboard(): void
    {
        User::create([
            'nama' => 'Operator',
            'email' => 'operator@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'operator',
            'status_akun' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'email' => 'operator@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('operator.dashboard'));
        $this->assertAuthenticated();
        $this->get(route('operator.dashboard'))->assertOk();
        $this->get(route('operator.stations.index'))->assertOk();
    }

    public function test_operator_dashboard_shows_live_station_charger_and_tariff_data(): void
    {
        $operator = $this->createOperator('dashboard-operator@example.com');
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Operator',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
        Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'OP-CH-01',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'is_online' => true,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($operator)->get(route('operator.dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard Operator')
            ->assertSee('id="operator-clock"', false)
            ->assertSee('data-timezone="Asia/Jakarta"', false)
            ->assertDontSee('· WIB')
            ->assertSee('SPKLU Operator')
            ->assertSee('OP-CH-01')
            ->assertSee('value="2500"', false)
            ->assertSee('Status charger & tarif', false)
            ->assertSee('aria-label="Kelola stasiun aktif"', false)
            ->assertSee('aria-label="Kelola ketersediaan charger"', false)
            ->assertSee('href="#recent-activity"', false)
            ->assertSee('data-open-inspection', false)
            ->assertSee('id="charger-status"', false)
            ->assertSee('id="recent-activity"', false);

        $this->get(route('operator.stations.index'))
            ->assertOk()
            ->assertSee('Kembali ke Dashboard Operator')
            ->assertSee(route('operator.dashboard'));
    }

    public function test_station_management_shows_live_charger_availability_for_each_station(): void
    {
        $operator = $this->createOperator('station-availability-operator@example.com');
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Ketersediaan',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);

        foreach ([
            ['AV-01', true, 'tersedia'],
            ['USE-02', true, 'digunakan'],
            ['OFF-03', false, 'tersedia'],
            ['BAD-04', true, 'rusak'],
        ] as [$deviceNumber, $isOnline, $status]) {
            Charger::create([
                'id_location' => $location->id_location,
                'device_number' => $deviceNumber,
                'connector_type' => 'CCS2',
                'max_power_kw' => 50,
                'price_per_kwh' => 2500,
                'is_online' => $isOnline,
                'status' => $status,
            ]);
        }

        $response = $this->actingAs($operator)->get(route('operator.stations.index'));

        $response->assertOk()
            ->assertSee('Ketersediaan charger di SPKLU Ketersediaan')
            ->assertSee('1 siap digunakan')
            ->assertSee('dari 4 unit')
            ->assertSee('1 tersedia')
            ->assertSee('1 digunakan')
            ->assertSee('1 offline')
            ->assertSee('1 rusak');
    }

    public function test_operator_can_report_and_resolve_a_charger_inspection(): void
    {
        $operator = $this->createOperator('inspection-operator@example.com');
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Pemeriksaan',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'INSPECT-01',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'is_online' => true,
            'status' => 'tersedia',
        ]);

        $this->actingAs($operator)
            ->from(route('operator.dashboard'))
            ->put(route('operator.chargers.inspection.update', $charger->id_charger), [
                'needs_inspection' => 1,
                'inspection_note' => 'Kabel konektor perlu diperiksa.',
            ])
            ->assertRedirect(route('operator.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('chargers', [
            'id_charger' => $charger->id_charger,
            'needs_inspection' => true,
            'inspection_note' => 'Kabel konektor perlu diperiksa.',
            'inspection_reported_by' => $operator->id_user,
        ]);

        $this->get(route('operator.dashboard.live'))
            ->assertOk()
            ->assertJsonPath('stats.attention_chargers', 1);

        $this->actingAs($operator)
            ->from(route('operator.dashboard'))
            ->put(route('operator.chargers.inspection.update', $charger->id_charger), [
                'needs_inspection' => 0,
            ])
            ->assertRedirect(route('operator.dashboard'))
            ->assertSessionHas('success');

        $this->assertDatabaseHas('chargers', [
            'id_charger' => $charger->id_charger,
            'needs_inspection' => false,
            'inspection_note' => null,
            'inspection_reported_by' => null,
        ]);

        $this->get(route('operator.dashboard.live'))
            ->assertOk()
            ->assertJsonPath('stats.attention_chargers', 0);
    }

    public function test_live_dashboard_counts_only_sessions_that_are_ongoing(): void
    {
        $operator = $this->createOperator('live-session-operator@example.com');
        $driver = User::create([
            'nama' => 'Pengemudi',
            'email' => 'live-session-driver@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Sesi Realtime',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'LIVE-SESSION-01',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'is_online' => true,
            'status' => 'digunakan',
        ]);
        $vehicle = Vehicle::create([
            'id_user' => $driver->id_user,
            'merek' => 'Hyundai',
            'model' => 'Ioniq',
            'nomor_polisi' => 'B 5678 EV',
            'tipe_konektor' => 'CCS2',
        ]);
        ChargingSession::create([
            'user_id' => $driver->id_user,
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $vehicle->id_vehicle,
            'start_time' => now(),
            'energy_consumed_kwh' => 1,
            'total_cost' => 2500,
            'status' => 'ongoing',
        ]);

        $this->actingAs($operator)
            ->get(route('operator.dashboard.live'))
            ->assertOk()
            ->assertJsonPath('stats.ongoing_sessions', 1);
    }

    public function test_operator_can_update_charger_tariff(): void
    {
        $operator = $this->createOperator('tariff-operator@example.com');
        $driver = User::create([
            'nama' => 'Pengemudi',
            'email' => 'tariff-driver@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Tarif',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'OP-CH-02',
            'connector_type' => 'Type 2',
            'max_power_kw' => 22,
            'price_per_kwh' => 2500,
            'is_online' => true,
            'status' => 'tersedia',
        ]);
        $vehicle = Vehicle::create([
            'id_user' => $driver->id_user,
            'merek' => 'Hyundai',
            'model' => 'Ioniq',
            'nomor_polisi' => 'B 1234 EV',
            'tipe_konektor' => 'CCS2',
        ]);
        $session = ChargingSession::create([
            'user_id' => $driver->id_user,
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $vehicle->id_vehicle,
            'start_time' => now(),
            'energy_consumed_kwh' => 0,
            'total_cost' => 0,
            'status' => 'ongoing',
        ]);

        $response = $this->actingAs($operator)
            ->from(route('operator.dashboard'))
            ->put(route('operator.chargers.tariff.update', $charger->id_charger), [
                'price_per_kwh' => 2600,
            ]);

        $response->assertRedirect(route('operator.dashboard'))
            ->assertSessionHas('success');
        $this->assertDatabaseHas('chargers', [
            'id_charger' => $charger->id_charger,
            'price_per_kwh' => 2600,
        ]);

        $this->actingAs($driver)
            ->get(route('scan.charge.show', $charger->id_charger))
            ->assertOk()
            ->assertSee('Rp 2.600 / kWh');

        $this->get(route('charging.session', $session->id))
            ->assertOk()
            ->assertSee('Rp 2.600', false);
    }

    public function test_non_operator_cannot_access_station_management(): void
    {
        $driver = User::create([
            'nama' => 'Pengemudi',
            'email' => 'driver@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($driver)->get(route('operator.stations.index'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_access_operator_station_management(): void
    {
        $admin = User::create([
            'nama' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('operator.stations.index'));

        $response->assertRedirect(route('dashboard'));
        $response->assertSessionHas('error');
    }

    public function test_admin_cannot_update_charger_tariff(): void
    {
        $admin = User::create([
            'nama' => 'Admin',
            'email' => 'admin-tariff@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Admin',
            'alamat' => 'Jl. Merdeka',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'ADMIN-CH-01',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'is_online' => true,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($admin)
            ->put(route('operator.chargers.tariff.update', $charger->id_charger), [
                'price_per_kwh' => 3000,
            ]);

        $response->assertRedirect(route('dashboard'))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('chargers', [
            'id_charger' => $charger->id_charger,
            'price_per_kwh' => 2500,
        ]);
    }

    private function createOperator(string $email): User
    {
        return User::create([
            'nama' => 'Operator',
            'email' => $email,
            'password' => Hash::make('password123'),
            'peran' => 'operator',
            'status_akun' => 'aktif',
        ]);
    }

    public function test_admin_can_open_dashboard_and_dashboard_has_no_station_management_menu(): void
    {
        $admin = User::create([
            'nama' => 'Admin',
            'email' => 'admin-dashboard@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));

        $response->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertDontSee('Kelola SPKLU');
    }

    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        User::create([
            'nama' => 'Admin',
            'email' => 'admin-login@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);

        $response = $this->post('/login', [
            'email' => 'admin-login@example.com',
            'password' => 'password123',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
    }
}
