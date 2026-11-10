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

class AdminReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_view_transaction_and_revenue_reports(): void
    {
        $admin = User::create([
            'nama' => 'Admin EV',
            'email' => 'admin@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);

        $user = User::create([
            'nama' => 'Pengemudi Setia',
            'email' => 'driver-report@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);

        $vehicle = Vehicle::create([
            'id_user' => $user->id_user,
            'merek' => 'Wuling',
            'model' => 'Air EV',
            'nomor_polisi' => 'AB 1234 CD',
            'tipe_konektor' => 'CCS2',
        ]);

        $location = Location::create([
            'nama_lokasi' => 'SPKLU Tugu Utama',
            'alamat' => 'Jl. Mangkubumi No. 1',
            'latitude' => -7.78,
            'longitude' => 110.36,
            'status' => 'aktif',
        ]);

        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'CHG-TUGU-01',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'status' => 'tersedia',
        ]);

        $session = ChargingSession::create([
            'user_id' => $user->id_user,
            'charger_id' => $charger->id_charger,
            'vehicle_id' => $vehicle->id_vehicle,
            'start_time' => Carbon::now()->subMinutes(30),
            'end_time' => Carbon::now(),
            'energy_consumed_kwh' => 15.5,
            'total_cost' => 38750,
            'status' => 'paid',
        ]);

        Transaction::create([
            'session_id' => $session->id,
            'invoice_number' => 'INV-REPORT-001',
            'payment_method' => 'e-wallet',
            'amount' => 38750,
            'type' => 'payment',
            'status' => 'success',
            'paid_at' => Carbon::now(),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertSee('Laporan Transaksi & Pendapatan SPKLU', false);
        $response->assertSee('INV-REPORT-001');
        $response->assertSee('Pengemudi Setia');
        $response->assertSee('SPKLU Tugu Utama');
        $response->assertSee('15.50 kWh');
        $response->assertSee('38.750');
    }
}
