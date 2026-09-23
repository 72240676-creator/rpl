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
            $table->id('id_charger');
            $table->unsignedBigInteger('id_location');
            $table->string('device_number')->unique();
            $table->string('connector_type');
            $table->decimal('max_power_kw', 8, 2);
            $table->decimal('price_per_kwh', 10, 2);
            $table->boolean('is_online')->default(true);
            $table->enum('status', ['tersedia', 'digunakan', 'rusak'])->default('tersedia');
            $table->timestamps();

            $table->foreign('id_location')->references('id_location')->on('locations')->onDelete('cascade');
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