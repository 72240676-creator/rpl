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

            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('cascade');

            $table->foreignId('charger_id')
                ->constrained('chargers')
                ->onDelete('cascade');

            $table->foreignId('vehicle_id')
                ->constrained('vehicles')
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