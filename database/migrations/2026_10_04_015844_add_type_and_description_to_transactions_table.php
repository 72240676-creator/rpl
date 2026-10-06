<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            // Cek jika 'type' belum ada baru ditambahkan
            if (!Schema::hasColumn('transactions', 'type')) {
                $table->string('type')->default('payment');
            }

            // Tambahkan kolom 'description' jika belum ada
            if (!Schema::hasColumn('transactions', 'description')) {
                $table->text('description')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            if (Schema::hasColumn('transactions', 'description')) {
                $table->dropColumn('description');
            }

            // Opsional: hapus 'type' jika dibuat oleh migrasi ini
            // if (Schema::hasColumn('transactions', 'type')) {
            //     $table->dropColumn('type');
            // }
        });
    }
};