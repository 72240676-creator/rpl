<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('locations', function (Blueprint $table) {
            $table->id('id_location');
            $table->string('nama_lokasi');
            $table->text('alamat');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('jam_operasional')->default('24 Jam');
            $table->text('fasilitas')->nullable();
            $table->string('foto')->nullable();
            $table->enum('status', ['aktif', 'tutup_sementara', 'penuh', 'perawatan'])->default('aktif');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};