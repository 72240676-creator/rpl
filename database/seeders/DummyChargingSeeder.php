<?php

namespace Database\Seeders;

use App\Models\Charger;
use Illuminate\Database\Seeder;

class DummyChargingSeeder extends Seeder
{
    public function run(): void
    {
        Charger::updateOrCreate(
            [
                'device_number' => 'CHG-001',
            ],
            [
                'id_charger' => 1,
                'id_location' => 1, // Sesuai dengan kolom foreign key di migrasi
                'connector_type' => 'CCS2',
                'max_power_kw' => 50.00,
                'price_per_kwh' => 2500.00,
                'is_online' => true,
                'status' => 'tersedia',
            ]
        );
    }
}