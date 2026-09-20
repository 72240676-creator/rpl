<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    protected $primaryKey = 'id_location';

    protected $fillable = [
        'nama_lokasi',
        'alamat',
        'latitude',
        'longitude',
        'jam_operasional',
        'fasilitas',
        'status'
    ];

    // Relasi ke Charger (jika nanti dibutuhkan)
    public function chargers()
    {
        return $this->hasMany(Charger::class, 'id_location', 'id_location');
    }
}