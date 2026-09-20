<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;

class LocationSeeder extends Seeder
{
    public function run(): void
    {
        $locations = [
            [
                'nama_lokasi' => 'SPKLU PLN UP3 Yogyakarta',
                'alamat' => 'Jl. Gedongkuning No.3, Banguntapan, Bantul, DIY',
                'latitude' => -7.809315,
                'longitude' => 110.402008,
                'jam_operasional' => '24 Jam',
                'fasilitas' => 'Ruang Tunggu, Toilet, Mushola',
                'status' => 'aktif',
            ],
            [
                'nama_lokasi' => 'SPKLU PLN UP2D JTY',
                'alamat' => 'Jl. Pangeran Mangkubumi No.16, Gowongan, Jetis, Yogyakarta',
                'latitude' => -7.788544,
                'longitude' => 110.365313,
                'jam_operasional' => '24 Jam',
                'fasilitas' => 'Minimarket, Toilet',
                'status' => 'aktif',
            ],
            [
                'nama_lokasi' => 'SPKLU Ambarrukmo Plaza',
                'alamat' => 'Jl. Laksda Adisucipto No.80, Sleman, DIY',
                'latitude' => -7.782728,
                'longitude' => 110.401297,
                'jam_operasional' => '10:00 - 22:00',
                'fasilitas' => 'Mall, Restoran, Toilet',
                'status' => 'aktif',
            ]
        ];

        foreach ($locations as $loc) {
            Location::create($loc);
        }
    }
}