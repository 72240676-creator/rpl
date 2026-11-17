<?php

namespace Tests\Feature;

use App\Models\Charger;
use App\Models\ChargingSession;
use App\Models\Location;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ChargingPrepaidTest extends TestCase
{
    use RefreshDatabase;

    public function test_session_cannot_start_when_wallet_cannot_cover_reservation(): void
    {
        [$user, $charger, $vehicle] = $this->createChargingSetup(9000);

        $this->actingAs($user)
            ->from(route('scan.charge.show', $charger->id_charger))
            ->post(route('charging.start'), [
                'charger_id' => $charger->id_charger,
                'vehicle_id' => $vehicle->id_vehicle,
                'reserved_amount' => 10000,
            ])
            ->assertRedirect(route('scan.charge.show', $charger->id_charger))
            ->assertSessionHasErrors('reserved_amount');

        $this->assertSame(9000.0, (float) $user->fresh()->saldo);
        $this->assertDatabaseCount('charging_sessions', 0);
    }

    public function test_session_reserves_balance_and_settles_actual_cost_once(): void
    {
        [$user, $charger, $vehicle] = $this->createChargingSetup(100000);

        $response = $this->actingAs($user)->post(route('charging.start'), [
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $vehicle->id_vehicle,
            'reserved_amount' => 50000,
        ]);

        $session = ChargingSession::where('user_id', $user->id_user)->firstOrFail();
        $response->assertRedirect(route('charging.session', $session->id));
        $this->assertSame(50000.0, (float) $user->fresh()->saldo);
        $this->assertSame(50000.0, (float) $session->reserved_amount);
        $this->assertDatabaseHas('transactions', [
            'session_id' => $session->id,
            'invoice_number' => 'HOLD-'.$session->id,
            'amount' => 50000,
            'status' => 'pending',
        ]);

        $stopResponse = $this->post(route('charging.stop', $session->id), [
            'energy_consumed_kwh' => 12.5,
            'total_cost' => 1,
        ]);

        $stopResponse->assertRedirect(route('charging.invoice', $session->id));
        $this->assertSame('paid', $session->fresh()->status);
        $this->assertSame(31250.0, (float) $session->fresh()->total_cost);
        $this->assertSame(68750.0, (float) $user->fresh()->saldo);
        $this->assertSame(3, (int) $user->fresh()->points);
        $this->assertDatabaseHas('transactions', [
            'session_id' => $session->id,
            'amount' => 31250,
            'type' => 'payment',
            'status' => 'success',
        ]);
        $this->assertDatabaseHas('transactions', [
            'session_id' => $session->id,
            'invoice_number' => 'REFUND-'.$session->id,
            'amount' => 18750,
            'type' => 'refund',
            'status' => 'success',
        ]);

        $this->post(route('charging.stop', $session->id), [
            'energy_consumed_kwh' => 12.5,
            'total_cost' => 1,
        ])->assertRedirect(route('charging.invoice', $session->id));

        $this->assertSame(68750.0, (float) $user->fresh()->saldo);
        $this->assertSame(2, Transaction::where('session_id', $session->id)->count());
    }

    private function createChargingSetup(int $balance): array
    {
        $user = User::create([
            'nama' => 'Pelanggan Uji',
            'email' => 'prepaid-'.$balance.'@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
            'saldo' => $balance,
        ]);

        $location = Location::create([
            'nama_lokasi' => 'SPKLU Prepaid',
            'alamat' => 'Jl. Uji Coba',
            'latitude' => -7.8,
            'longitude' => 110.3,
            'jam_operasional' => '24 Jam',
            'status' => 'aktif',
        ]);

        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'PREPAID-'.$balance,
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'status' => 'tersedia',
            'is_online' => true,
        ]);

        $vehicle = Vehicle::create([
            'id_user' => $user->id_user,
            'merek' => 'Hyundai',
            'model' => 'Ioniq 5',
            'nomor_polisi' => 'AB 1234 EV',
            'tipe_konektor' => 'CCS2',
        ]);

        return [$user, $charger, $vehicle];
    }
}
