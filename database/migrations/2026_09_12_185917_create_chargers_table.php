<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chargers', function (Blueprint $table) {
        $table->id();

        $table->foreignId('station_id')
            ->constrained('stations')
            ->onDelete('cascade');

        $table->string('device_number')->unique();

        $table->enum('connector_type', [
            'CCS2',
            'Type 2',
            'CHAdeMO'
        ]);

        $table->decimal('max_power_kw', 8, 2);

        $table->decimal('price_per_kwh', 12, 2);

        $table->boolean('is_online')->default(false);

        $table->enum('status', [
            'available',
            'in_use',
            'broken'
        ])->default('available');

        $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chargers');
    }
};
