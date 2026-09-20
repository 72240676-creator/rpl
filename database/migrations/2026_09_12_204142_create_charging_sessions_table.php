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

            // Relasi ke user
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id_user')
                ->on('users')
                ->onDelete('cascade');

            // Relasi ke charger
            $table->foreignId('charger_id')
                ->constrained('chargers')
                ->onDelete('cascade');

            // Relasi ke kendaraan
            $table->unsignedBigInteger('vehicle_id');
            $table->foreign('vehicle_id')
                ->references('id_vehicle')
                ->on('vehicles')
                ->onDelete('cascade');

            // Waktu session
            $table->dateTime('start_time');
            $table->dateTime('end_time')->nullable();

            // Hasil charging
            $table->decimal('energy_consumed_kwh', 10, 3)
                ->default(0);

            $table->decimal('total_cost', 12, 2)
                ->default(0);

            // Status session
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