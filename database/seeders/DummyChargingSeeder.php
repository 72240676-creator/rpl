<?php

namespace Database\Seeders;

use App\Models\Station;
use App\Models\Charger;
use Illuminate\Database\Seeder;

class DummyChargingSeeder extends Seeder
{
    public function run(): void
    {
        // Station dummy
        $station = Station::updateOrCreate(
            [
                'name' => 'SPKLU Dummy Jakarta',
            ],
            [
                'operator_id' => 1,
                'address' => 'Jl. Dummy No. 1, Jakarta',
                'latitude' => -6.2000000,
                'longitude' => 106.8166667,
                'operational_hours' => '24 Jam',
                'facilities' => json_encode([
                    'Toilet',
                    'Minimarket',
                    'Parkir',
                ]),
                'photo_url' => null,
                'status' => 'active',
            ]
        );

        // Charger dummy
        Charger::updateOrCreate(
            [
                'device_number' => 'CHG-001',
            ],
            [
                'station_id' => $station->id,
                'connector_type' => 'CCS2',
                'max_power_kw' => 50,
                'price_per_kwh' => 2500,
                'is_online' => true,
                'status' => 'available',
            ]
        );
    }
}