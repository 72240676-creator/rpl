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
            $table->id('id_vehicle'); // Primary Key kendaraan
            
            // Membuat kolom Foreign Key
            $table->unsignedBigInteger('id_user');
            
            // Menghubungkan id_user di tabel vehicles dengan id_user di tabel users
            $table->foreign('id_user')
                  ->references('id_user')->on('users')
                  ->onDelete('cascade'); // Jika user dihapus, data mobilnya ikut terhapus
                  
            $table->string('merek');
            $table->string('model');
            $table->string('nomor_polisi');
            $table->string('tipe_konektor');
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
