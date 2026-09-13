<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('charging_sessions', function (Blueprint $table) {
            $table->id();

            // 1. Relasi manual ke id_user pada tabel users
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            // 2. Relasi otomatis ke tabel chargers (menggunakan id default)
            $table->foreignId('charger_id')
                ->constrained('chargers')
                ->onDelete('cascade');

            // 3. Relasi manual ke id_vehicle pada tabel vehicles
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')
                ->references('id_vehicle')
                ->on('vehicles')
                ->onDelete('cascade');

            $table->dateTime('start_time');

            $table->dateTime('end_time')
                ->nullable();

            $table->decimal('energy_consumed_kwh', 10, 3)
                ->nullable();

            $table->decimal('total_cost', 12, 2)
                ->nullable();

            $table->enum('status', [
                'ongoing',
                'completed',
                'cancelled',
                'failed'
            ])->default('ongoing');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('charging_sessions');
    }
};