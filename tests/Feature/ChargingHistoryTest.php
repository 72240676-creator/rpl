<?php

namespace Tests\Feature;

use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChargingHistoryTest extends TestCase
{
    use RefreshDatabase;

    public function test_history_shows_charging_usage_expense_and_payment_details(): void
    {
        $user = User::create([
            'nama' => 'Pengemudi',
            'email' => 'driver@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);
        $location = Location::create([
            'nama_lokasi' => 'SPKLU Sudirman',
            'alamat' => 'Jl. Sudirman',
            'latitude' => -6.2,
            'longitude' => 106.8,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);
        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'CH-001',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'status' => 'tersedia',
        ]);
        $vehicle = Vehicle::create([
            'id_user' => $user->id_user,
            'merek' => 'Hyundai',
            'model' => 'Ioniq 5',
            'nomor_polisi' => 'B 1234 EV',
            'tipe_konektor' => 'CCS2',
        ]);
        $session = ChargingSession::create([
            'user_id' => $user->id_user,
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $vehicle->id_vehicle,
            'start_time' => Carbon::parse('2026-10-28 07:00:00'),
            'end_time' => Carbon::parse('2026-10-28 07:30:00'),
            'energy_consumed_kwh' => 1.25,
            'total_cost' => 3125,
            'status' => 'paid',
        ]);
        Transaction::create([
            'session_id' => $session->id,
            'invoice_number' => 'INV-TEST-001',
            'payment_method' => 'e-wallet',
            'amount' => 3125,
            'type' => 'payment',
            'status' => 'success',
            'paid_at' => Carbon::parse('2026-10-28 07:31:00'),
        ]);

        $response = $this->actingAs($user)->get(route('charging.history'));

        $response->assertOk()
            ->assertDontSee('history-clock')
            ->assertDontSee('WIB')
            ->assertSee('SPKLU Sudirman')
            ->assertSee('CH-001')
            ->assertSee('1,250 kWh')
            ->assertSee('Rp 2.500 / kWh')
            ->assertSee('00:30:00')
            ->assertSee('28 October 2026, 14:00')
            ->assertSee('14:00:00')
            ->assertSee('14:30:00')
            ->assertSee('28/10/2026 14:31')
            ->assertSee('Rp 3.125')
            ->assertSee('INV-TEST-001')
            ->assertSee('Lihat invoice');
    }
}
