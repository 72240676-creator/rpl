<?php

namespace Tests\Feature;

use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Location;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CustomerDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_lists_only_stations_with_online_available_chargers(): void
    {
        $user = $this->createCustomer();
        $availableStation = $this->createStation('SPKLU dengan Charger Tersedia');
        $unavailableStation = $this->createStation('SPKLU tanpa Charger Tersedia');
        $fourthStation = $this->createStation('SPKLU Tambahan');

        $this->createCharger($availableStation, 'DASH-AVAILABLE', true, 'tersedia');
        $this->createCharger($availableStation, 'DASH-OFFLINE', false, 'tersedia');
        $this->createCharger($unavailableStation, 'DASH-BUSY', true, 'digunakan');
        $this->createCharger($fourthStation, 'DASH-FOURTH', true, 'tersedia');

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('SPKLU dengan Charger Tersedia')
            ->assertSee('SPKLU Tambahan')
            ->assertSee('1 unit tersedia')
            ->assertSee('CCS2')
            ->assertSee('50,0 kW')
            ->assertSee('Rp 2.500/kWh')
            ->assertSee('id="sortByLocation"', false)
            ->assertSee('data-lat="-7.8"', false)
            ->assertSee('data-lng="110.3"', false)
            ->assertSee('hingga 3 SPKLU terdekat')
            ->assertDontSee('SPKLU tanpa Charger Tersedia')
            ->assertSee('Daftarkan kendaraan listrik Anda')
            ->assertDontSee('Segera hadir')
            ->assertDontSee('Tukar baterai');
    }

    public function test_registered_customer_can_start_the_charging_flow_from_dashboard(): void
    {
        $user = $this->createCustomer();

        Vehicle::create([
            'id_user' => $user->id_user,
            'merek' => 'Hyundai',
            'model' => 'Ioniq 5',
            'nomor_polisi' => 'AB 1234 EV',
            'tipe_konektor' => 'CCS2',
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Hyundai Ioniq 5')
            ->assertSee('AB 1234 EV')
            ->assertSee('Pilih kendaraan')
            ->assertSee('Mulai charging')
            ->assertSee(route('scan.charge'), false);
    }

    public function test_selected_vehicle_is_used_when_charging_starts(): void
    {
        $user = $this->createCustomer();
        $firstVehicle = $this->createVehicle($user, 'Hyundai', 'Ioniq 5', 'AB 1234 EV');
        $selectedVehicle = $this->createVehicle($user, 'Wuling', 'Air EV', 'AB 4321 EV');
        $station = $this->createStation('SPKLU Kendaraan Terpilih');
        $charger = $this->createCharger($station, 'DASH-SELECTED', true, 'tersedia');

        $this->actingAs($user)
            ->post(route('vehicles.select'), ['vehicle_id' => $selectedVehicle->id_vehicle])
            ->assertRedirect(route('dashboard'))
            ->assertSessionHas('selected_vehicle_id', $selectedVehicle->id_vehicle);

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Wuling Air EV')
            ->assertSee('AB 4321 EV')
            ->assertSee('value="' . $selectedVehicle->id_vehicle . '" selected', false);

        $this->get(route('scan.charge.show', $charger->id_charger))
            ->assertOk()
            ->assertSee('value="' . $selectedVehicle->id_vehicle . '" selected', false);

        $response = $this->post(route('charging.start'), [
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $selectedVehicle->id_vehicle,
        ]);

        $session = ChargingSession::where('user_id', $user->id_user)->firstOrFail();

        $response->assertRedirect(route('charging.session', $session->id));
        $this->assertSame($selectedVehicle->id_vehicle, $session->vehicle_id);
        $this->assertNotSame($firstVehicle->id_vehicle, $session->vehicle_id);
    }

    public function test_customer_cannot_select_another_users_vehicle(): void
    {
        $user = $this->createCustomer();
        $otherUser = User::create([
            'nama' => 'Pelanggan Lain',
            'email' => 'other-customer-dashboard@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);
        $otherVehicle = $this->createVehicle($otherUser, 'Tesla', 'Model 3', 'AB 9999 EV');

        $this->actingAs($user)
            ->from(route('dashboard'))
            ->post(route('vehicles.select'), ['vehicle_id' => $otherVehicle->id_vehicle])
            ->assertNotFound();
    }

    private function createCustomer(): User
    {
        return User::create([
            'nama' => 'Pelanggan Uji',
            'email' => 'customer-dashboard@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);
    }

    private function createStation(string $name): Location
    {
        return Location::create([
            'nama_lokasi' => $name,
            'alamat' => 'Jl. Uji Coba, Yogyakarta',
            'latitude' => -7.8,
            'longitude' => 110.3,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
    }

    private function createVehicle(User $user, string $make, string $model, string $plate): Vehicle
    {
        return Vehicle::create([
            'id_user' => $user->id_user,
            'merek' => $make,
            'model' => $model,
            'nomor_polisi' => $plate,
            'tipe_konektor' => 'CCS2',
        ]);
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
