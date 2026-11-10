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
        Schema::table('vehicles', function (Blueprint $table) {
            $table->decimal('battery_capacity_kwh', 6, 2)->default(60.00)->after('tipe_konektor');
            $table->integer('current_battery_soc')->default(25)->after('battery_capacity_kwh');
        });

        Schema::table('charging_sessions', function (Blueprint $table) {
            $table->integer('start_battery_soc')->default(25)->after('vehicle_id');
            $table->integer('end_battery_soc')->nullable()->after('start_battery_soc');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vehicles', function (Blueprint $table) {
            $table->dropColumn(['battery_capacity_kwh', 'current_battery_soc']);
        });

        Schema::table('charging_sessions', function (Blueprint $table) {
            $table->dropColumn(['start_battery_soc', 'end_battery_soc']);
        });
    }
};
