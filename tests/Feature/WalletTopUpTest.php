<?php

namespace Tests\Feature;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class WalletTopUpTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_can_view_simple_top_up_page(): void
    {
        $driver = $this->createDriver('wallet-page@example.com', 25000);

        $this->actingAs($driver)->get(route('topup.index'))
            ->assertOk()
            ->assertSee('Isi saldo sekarang')
            ->assertSee('Saldo Anda')
            ->assertSee('Rp 25.000')
            ->assertSee('Pilih jumlah top up')
            ->assertDontSee('Midtrans')
            ->assertDontSee('sandbox');
    }

    public function test_driver_can_top_up_and_transaction_is_recorded(): void
    {
        $driver = $this->createDriver('wallet-success@example.com', 25000);

        $response = $this->actingAs($driver)->post(route('topup.store'), [
            'nominal' => 100000,
            'topup_reference' => 'c5c9272f-0631-41c5-850c-53e259c7bd1b',
        ]);

        $response->assertRedirect(route('topup.index'))
            ->assertSessionHas('success', 'Saldo berhasil ditambahkan sebesar Rp 100.000.');

        $this->assertSame(125000.0, (float) $driver->fresh()->saldo);
        $this->assertDatabaseHas('transactions', [
            'id_user' => $driver->id_user,
            'session_id' => null,
            'invoice_number' => 'TOPUP-c5c9272f-0631-41c5-850c-53e259c7bd1b',
            'payment_method' => 'e-wallet',
            'amount' => 100000,
            'type' => 'topup',
            'status' => 'success',
            'description' => 'Penambahan saldo dompet.',
        ]);
    }

    public function test_repeating_same_reference_does_not_add_balance_twice(): void
    {
        $driver = $this->createDriver('wallet-repeat@example.com', 10000);
        $payload = [
            'nominal' => 50000,
            'topup_reference' => 'ac93d1a5-04b4-43c0-9140-48d0e112cb13',
        ];

        $this->actingAs($driver)->post(route('topup.store'), $payload)
            ->assertRedirect(route('topup.index'));
        $this->post(route('topup.store'), $payload)
            ->assertRedirect(route('topup.index'))
            ->assertSessionHas('success', 'Transaksi ini sudah diproses sebelumnya.');

        $this->assertSame(60000.0, (float) $driver->fresh()->saldo);
        $this->assertSame(1, Transaction::where('invoice_number', 'TOPUP-' . $payload['topup_reference'])->count());
    }

    public function test_reference_cannot_be_reused_by_another_user(): void
    {
        $owner = $this->createDriver('wallet-reference-owner@example.com');
        $otherDriver = $this->createDriver('wallet-reference-other@example.com');
        $reference = '7ae65546-a925-470f-909d-32f7c6658614';

        Transaction::create([
            'id_user' => $owner->id_user,
            'invoice_number' => 'TOPUP-' . $reference,
            'payment_method' => 'e-wallet',
            'amount' => 10000,
            'type' => 'topup',
            'description' => 'Penambahan saldo dompet.',
            'status' => 'success',
        ]);

        $this->actingAs($otherDriver)->post(route('topup.store'), [
            'nominal' => 50000,
            'topup_reference' => $reference,
        ])->assertSessionHasErrors('topup_reference');

        $this->assertSame(0.0, (float) $otherDriver->fresh()->saldo);
        $this->assertSame(1, Transaction::where('invoice_number', 'TOPUP-' . $reference)->count());
    }

    public function test_top_up_rejects_out_of_range_amount(): void
    {
        $driver = $this->createDriver('wallet-invalid-amount@example.com');

        $this->actingAs($driver)->post(route('topup.store'), [
            'nominal' => 5000,
            'topup_reference' => '7208447b-06e1-4f06-acd5-8af700179d71',
        ])->assertSessionHasErrors('nominal');

        $this->assertSame(0.0, (float) $driver->fresh()->saldo);
        $this->assertDatabaseCount('transactions', 0);
    }

    public function test_top_up_page_only_shows_the_authenticated_users_history(): void
    {
        $driver = $this->createDriver('wallet-owner@example.com');
        $otherDriver = $this->createDriver('wallet-other@example.com');

        $this->createTopUp($driver, 'TOPUP-OWNER-001', 10000);
        $this->createTopUp($otherDriver, 'TOPUP-OTHER-001', 20000);

        $this->actingAs($driver)->get(route('topup.index'))
            ->assertOk()
            ->assertSee('TOPUP-' . str_pad((string) Transaction::where('invoice_number', 'TOPUP-OWNER-001')->value('id'), 6, '0', STR_PAD_LEFT))
            ->assertDontSee('TOPUP-OTHER-001')
            ->assertDontSee('TOPUP-' . str_pad((string) Transaction::where('invoice_number', 'TOPUP-OTHER-001')->value('id'), 6, '0', STR_PAD_LEFT));
    }

    private function createTopUp(User $user, string $invoice, int $amount): Transaction
    {
        return Transaction::create([
            'id_user' => $user->id_user,
            'invoice_number' => $invoice,
            'payment_method' => 'e-wallet',
            'amount' => $amount,
            'type' => 'topup',
            'description' => 'Penambahan saldo dompet.',
            'status' => 'success',
            'paid_at' => now(),
        ]);
    }

    private function createDriver(string $email, int $balance = 0): User
    {
        return User::create([
            'nama' => 'Pengemudi EV',
            'email' => $email,
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
            'saldo' => $balance,
        ]);
    }
}
