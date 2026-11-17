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

    public function test_admin_report_uses_datatables_for_search_sorting_and_pagination(): void
    {
        $admin = User::create([
            'nama' => 'Admin EV',
            'email' => 'admin-pagination@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'admin',
            'status_akun' => 'aktif',
        ]);

        foreach (range(1, 11) as $number) {
            Transaction::create([
                'id_user' => $admin->id_user,
                'invoice_number' => 'TOPUP-PAGE-' . $number,
                'payment_method' => 'e-wallet',
                'amount' => 10000,
                'type' => 'topup',
                'description' => 'Penambahan saldo dompet.',
                'status' => 'pending',
            ]);
        }

        $this->actingAs($admin)->get(route('admin.reports.index'))
            ->assertOk()
            ->assertSee('id="transactionsTable"', false)
            ->assertSee('TOPUP-000011')
            ->assertDontSee('<svg', false)
            ->assertViewHas('transactions', fn ($transactions) => $transactions->count() === 11);
    }

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

        $topUp = Transaction::create([
            'id_user' => $user->id_user,
            'invoice_number' => 'TOPUP-054d8578-df26-4b21-9b6b-69be54ea83f1',
            'payment_method' => 'e-wallet',
            'amount' => 100000,
            'type' => 'topup',
            'description' => 'Penambahan saldo dompet.',
            'status' => 'success',
            'paid_at' => Carbon::now(),
        ]);
        Transaction::whereKey($topUp->id)->update([
            'created_at' => Carbon::parse('2026-11-12 18:33:00', 'UTC'),
        ]);

        $response = $this->actingAs($admin)->get(route('admin.reports.index'));

        $response->assertOk();
        $response->assertViewHas('totalPendapatan', 38750);
        $response->assertSee('Laporan Transaksi & Pendapatan SPKLU', false);
        $response->assertSee('INV-REPORT-001');
        $response->assertSee('TOPUP-' . str_pad((string) $topUp->id, 6, '0', STR_PAD_LEFT));
        $response->assertDontSee('TOPUP-054d8578-df26-4b21-9b6b-69be54ea83f1');
        $response->assertSee('Pengemudi Setia');
        $response->assertSee('SPKLU Tugu Utama');
        $response->assertSee('Isi saldo');
        $response->assertSee('Penambahan saldo dompet.');
        $response->assertSee('13 Nov 2026 01:33 WIB');
        $response->assertSee('15.50 kWh');
        $response->assertSee('38.750');
    }
}
