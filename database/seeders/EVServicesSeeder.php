<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Charger;
use App\Models\BatterySwapCabinet;

class EVServicesSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Pastikan lokasi yang sudah ada memiliki category 'spklu'
        Location::whereNull('category')->orWhere('category', '')->update(['category' => 'spklu']);

        // 2. Seeding SPKLU R2 (Motor Listrik)
        $r2Locations = [
            [
                'nama_lokasi' => 'SPKLU R2 Motor Listrik Malioboro Hub',
                'category' => 'spklu_r2',
                'alamat' => 'Jl. Malioboro No. 42, Sosromenduran, Gedong Tengen, Yogyakarta',
                'latitude' => -7.793012,
                'longitude' => 110.365842,
                'jam_operasional' => '24 Jam',
                'fasilitas' => 'Parkir Khusus Motor, Kafe, Toilet',
                'status' => 'aktif',
                'chargers' => [
                    ['device_number' => 'R2-MLBR-01', 'connector_type' => 'Schuko (AC Motor)', 'max_power_kw' => 3.3, 'price_per_kwh' => 1800],
                    ['device_number' => 'R2-MLBR-02', 'connector_type' => 'Type 2 (AC Motor)', 'max_power_kw' => 7.4, 'price_per_kwh' => 2000],
                ]
            ],
            [
                'nama_lokasi' => 'SPKLU R2 Alva & Gesits Hub Kaliurang',
                'category' => 'spklu_r2',
                'alamat' => 'Jl. Kaliurang KM 5.2, Caturtunggal, Depok, Sleman',
                'latitude' => -7.765022,
                'longitude' => 110.380045,
                'jam_operasional' => '07:00 - 23:00',
                'fasilitas' => 'Rest Area R2, Bengkel Ringan EV, Minimarket',
                'status' => 'aktif',
                'chargers' => [
                    ['device_number' => 'R2-KLRG-01', 'connector_type' => 'Schuko (AC Motor)', 'max_power_kw' => 3.3, 'price_per_kwh' => 1800],
                ]
            ],
        ];

        foreach ($r2Locations as $data) {
            $chargers = $data['chargers'];
            unset($data['chargers']);

            $loc = Location::firstOrCreate(
                ['nama_lokasi' => $data['nama_lokasi']],
                $data
            );

            foreach ($chargers as $chg) {
                Charger::firstOrCreate(
                    ['device_number' => $chg['device_number']],
                    array_merge($chg, [
                        'id_location' => $loc->id_location,
                        'is_online' => true,
                        'status' => 'tersedia',
                    ])
                );
            }
        }

        // 3. Seeding SPLU (Stasiun Pengisian Listrik Umum)
        $spluLocations = [
            [
                'nama_lokasi' => 'SPLU Alun-Alun Kidul Yogyakarta',
                'category' => 'splu',
                'alamat' => 'Patehan, Kraton, Kota Yogyakarta, DIY',
                'latitude' => -7.812034,
                'longitude' => 110.363022,
                'jam_operasional' => '24 Jam',
                'fasilitas' => '4 Soket Colokan 220V, Lampu Penerangan, Area Terbuka',
                'status' => 'aktif',
            ],
            [
                'nama_lokasi' => 'SPLU Pasar Kranggan Kuliner',
                'category' => 'splu',
                'alamat' => 'Jl. Poncowinatan No. 12, Gowongan, Jetis, Yogyakarta',
                'latitude' => -7.782510,
                'longitude' => 110.367040,
                'jam_operasional' => '06:00 - 23:00',
                'fasilitas' => 'Pusat Kuliner, Parkir Luas, 4 Soket 220V',
                'status' => 'aktif',
            ],
        ];

        foreach ($spluLocations as $data) {
            Location::firstOrCreate(
                ['nama_lokasi' => $data['nama_lokasi']],
                $data
            );
        }

        // 4. Seeding Tukar Baterai (SPBKLU / Battery Swap)
        $swapLocations = [
            [
                'nama_lokasi' => 'SPBKLU Swap Hub Stasiun Lempuyangan',
                'category' => 'battery_swap',
                'alamat' => 'Jl. Lempuyangan, Bausasran, Danurejan, Kota Yogyakarta',
                'latitude' => -7.790515,
                'longitude' => 110.375042,
                'jam_operasional' => '24 Jam',
                'fasilitas' => 'Kabinet Otomatis 8 Slot, CCTV, Stasiun Kereta',
                'status' => 'aktif',
                'cabinet' => [
                    'cabinet_name' => 'Swap Cabinet Lempuyangan 01',
                    'cabinet_code' => 'SWAP-LMP-01',
                    'total_slots' => 8,
                    'available_batteries' => 6,
                    'charging_batteries' => 1,
                    'empty_slots' => 1,
                    'battery_type' => 'Lithium LiFePO4 60V / 24Ah (Universal)',
                    'price_per_swap' => 8000,
                    'status' => 'active',
                ]
            ],
            [
                'nama_lokasi' => 'SPBKLU Swap Point SPBU Sagan Gejayan',
                'category' => 'battery_swap',
                'alamat' => 'Jl. Prof. Herman Yohanes No. 54, Terban, Gondokusuman, Yogyakarta',
                'latitude' => -7.779032,
                'longitude' => 110.381015,
                'jam_operasional' => '24 Jam',
                'fasilitas' => 'SPBU 24 Jam, Mini Market, Tambal Ban, ATM',
                'status' => 'aktif',
                'cabinet' => [
                    'cabinet_name' => 'Swap Cabinet Sagan 02',
                    'cabinet_code' => 'SWAP-SGN-02',
                    'total_slots' => 8,
                    'available_batteries' => 5,
                    'charging_batteries' => 2,
                    'empty_slots' => 1,
                    'battery_type' => 'Lithium LiFePO4 60V / 24Ah (Universal)',
                    'price_per_swap' => 8000,
                    'status' => 'active',
                ]
            ],
        ];

        foreach ($swapLocations as $data) {
            $cabData = $data['cabinet'];
            unset($data['cabinet']);

            $loc = Location::firstOrCreate(
                ['nama_lokasi' => $data['nama_lokasi']],
                $data
            );

            BatterySwapCabinet::firstOrCreate(
                ['cabinet_code' => $cabData['cabinet_code']],
                array_merge($cabData, ['location_id' => $loc->id_location])
            );
        }
    }
}

