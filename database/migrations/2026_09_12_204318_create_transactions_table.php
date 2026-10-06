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

            // Tambahkan id_user jika diperlukan langsung di tabel transaksi
            $table->unsignedBigInteger('id_user')->nullable();

            $table->foreignId('session_id')
                ->nullable()
                ->constrained('charging_sessions')
                ->onDelete('cascade');

            $table->string('invoice_number')->unique()->nullable();

            // Tambahkan kolom type dan description yang dipanggil di controller
            $table->string('type')->default('payment');
            $table->text('description')->nullable();

            $table->enum('payment_method', [
                'e-wallet',
            ])->default('e-wallet');

            $table->decimal('amount', 12, 2);

            $table->enum('status', [
                'pending',
                'success',
                'failed',
                'refunded'
            ])->default('success');

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