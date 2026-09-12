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
        Schema::create('vehicles', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel users
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            $table->string('license_plate')->unique();
            $table->string('brand');
            $table->string('model');
            // Angka 8 adalah total digit, 2 adalah angka di belakang koma (contoh: 999999.99)
            $table->decimal('battery_capacity_kwh', 8, 2); 
            
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicles');
    }
};
