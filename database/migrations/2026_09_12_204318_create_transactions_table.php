<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();

            $table->foreignId('session_id')
                ->constrained('charging_sessions')
                ->onDelete('cascade');

            $table->string('invoice_number')->unique();

            $table->enum('payment_method', [
                'e-wallet',
                'credit_card',
                'qr_payment'
            ]);

            $table->decimal('amount', 12, 2);

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'refunded'
            ])->default('pending');

            $table->timestamp('paid_at')
                ->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};