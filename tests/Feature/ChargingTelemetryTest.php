<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Models\Charger;
use App\Models\Vehicle;
use App\Models\ChargingSession;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class ChargingTelemetryTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_view_live_telemetry_and_stop_session(): void
    {
        $user = User::create([
            'nama' => 'Driver Telemetry',
            'email' => 'driver-telemetry@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
            'saldo' => 100000,
        ]);

        $location = Location::create([
            'nama_lokasi' => 'SPKLU Tugu Telemetry',
            'alamat' => 'Jl. Pasar Kembang No. 1',
            'latitude' => -7.7892,
            'longitude' => 110.3634,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);

        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'CHG-TELE-01',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'status' => 'sedang_mengisi',
            'is_online' => true,
        ]);

        $vehicle = Vehicle::create([
            'id_user' => $user->id_user,
            'merek' => 'Hyundai',
            'model' => 'Ioniq 5',
            'nomor_polisi' => 'AB 1234 EV',
            'tipe_konektor' => 'CCS2',
        ]);

        $session = ChargingSession::create([
            'user_id' => $user->id_user,
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $vehicle->id_vehicle,
            'start_time' => now()->subMinutes(15),
            'end_time' => null,
            'energy_consumed_kwh' => 0,
            'total_cost' => 0,
            'status' => 'ongoing',
        ]);

        // 1. Akses halaman telemetry
        $response = $this->actingAs($user)->get(route('charging.session', $session->id));

        $response->assertStatus(200);
        $response->assertSee('Live Telemetry Pengisian');
        $response->assertSee('CHG-TELE-01');
        $response->assertSee('Hyundai Ioniq 5');
        $response->assertSee('Batas Pengisian (Battery Health)');

        // 2. Stop session dengan telemetri
        $stopResponse = $this->actingAs($user)->post(route('charging.stop', $session->id), [
            'energy_consumed_kwh' => 12.500,
            'total_cost' => 31250,
        ]);

        $stopResponse->assertRedirect(route('charging.payment.view', $session->id));

        $session->refresh();
        $this->assertEquals('completed', $session->status);
        $this->assertEquals(12.500, (float) $session->energy_consumed_kwh);
        $this->assertEquals(31250, (float) $session->total_cost);
    }

    public function test_user_can_view_scan_page_and_lookup_charger_manually(): void
    {
        $user = User::create([
            'nama' => 'Pengemudi Scan Test',
            'email' => 'driver-scan@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);

        $location = Location::create([
            'nama_lokasi' => 'SPKLU Stasiun Scan',
            'alamat' => 'Jl. Malioboro No. 99',
            'latitude' => -7.79,
            'longitude' => 110.36,
            'status' => 'aktif',
        ]);

        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'CHG-SCAN-99',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'status' => 'tersedia',
            'is_online' => true,
        ]);

        $scanPage = $this->actingAs($user)->get(route('scan.charge'));
        $scanPage->assertOk()
            ->assertSee('Mulai Pengisian Daya SPKLU')
            ->assertSee('Pindai Kamera QR')
            ->assertSee('Masukkan ID Unit')
            ->assertSee('CHG-SCAN-99');

        $lookupResponse = $this->actingAs($user)->post(route('scan.charge.lookup'), [
            'charger_code' => 'CHG-SCAN-99',
        ]);

        $lookupResponse->assertRedirect(route('scan.charge.show', $charger->id_charger));
    }
}

