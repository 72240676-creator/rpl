<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Location;
use App\Models\Charger;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;

class LocationMapTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_access_interactive_map_and_locations_list(): void
    {
        $user = User::create([
            'nama' => 'Pengemudi Test',
            'email' => 'driver-map@example.com',
            'password' => Hash::make('password123'),
            'peran' => 'pengemudi',
            'status_akun' => 'aktif',
        ]);

        $location = Location::create([
            'nama_lokasi' => 'SPKLU Test Station',
            'alamat' => 'Jl. Pengujian No. 123, Sleman',
            'latitude' => -7.771234,
            'longitude' => 110.378910,
            'jam_operasional' => '24 Jam',
            'fasilitas' => 'Kafe, Toilet, Rest Area',
            'status' => 'aktif',
        ]);

        $charger = Charger::create([
            'id_location' => $location->id_location,
            'device_number' => 'TEST-CHG-01',
            'connector_type' => 'CCS2',
            'max_power_kw' => 50,
            'price_per_kwh' => 2500,
            'is_online' => true,
            'status' => 'tersedia',
        ]);

        $response = $this->actingAs($user)->get(route('locations.index'));

        $response->assertStatus(200);
        $response->assertSee('SPKLU Test Station');
        $response->assertSee('CCS2');
        $response->assertSee('50 kW');
        $response->assertSee('Peta Jaringan SPKLU Interaktif');
        $response->assertSee('Urutkan dari yang terdekat');
        $response->assertSee('id="gpsStatus" role="status" aria-live="polite"', false);
        $response->assertSee('data-lat="-7.771234"', false);
        $response->assertSee('data-lng="110.37891"', false);
    }
}
