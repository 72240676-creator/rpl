<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('locations', 'category')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->string('category', 50)->default('spklu')->after('nama_lokasi');
            });
        }

        if (!Schema::hasTable('battery_swap_cabinets')) {
            Schema::create('battery_swap_cabinets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('location_id');
                $table->string('cabinet_name');
                $table->string('cabinet_code')->unique();
                $table->integer('total_slots')->default(8);
                $table->integer('available_batteries')->default(5);
                $table->integer('charging_batteries')->default(2);
                $table->integer('empty_slots')->default(1);
                $table->string('battery_type')->default('Lithium LiFePO4 60V / 24Ah');
                $table->decimal('price_per_swap', 10, 2)->default(8000);
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('battery_swap_logs')) {
            Schema::create('battery_swap_logs', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('cabinet_id');
                $table->unsignedBigInteger('vehicle_id')->nullable();
                $table->integer('old_soc')->default(20);
                $table->integer('new_soc')->default(100);
                $table->decimal('amount', 10, 2)->default(8000);
                $table->string('swap_code', 50)->nullable();
                $table->string('status', 20)->default('completed');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('splu_sessions')) {
            Schema::create('splu_sessions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('location_id');
                $table->string('socket_number', 20)->default('Soket 01');
                $table->integer('duration_minutes')->default(60);
                $table->decimal('amount', 10, 2)->default(5000);
                $table->decimal('energy_kwh', 8, 3)->default(1.200);
                $table->timestamp('started_at')->nullable();
                $table->timestamp('expires_at')->nullable();
                $table->string('status', 20)->default('active');
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('splu_sessions');
        Schema::dropIfExists('battery_swap_logs');
        Schema::dropIfExists('battery_swap_cabinets');

        if (Schema::hasColumn('locations', 'category')) {
            Schema::table('locations', function (Blueprint $table) {
                $table->dropColumn('category');
            });
        }
    }
};

