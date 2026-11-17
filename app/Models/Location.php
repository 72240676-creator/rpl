<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'id_location';

    protected $fillable = [
        'nama_lokasi',
        'category',
        'alamat',
        'latitude',
        'longitude',
        'jam_operasional',
        'fasilitas',
        'foto',
        'status',
    ];

    public function chargers(): HasMany
    {
        return $this->hasMany(Charger::class, 'id_location', 'id_location');
    }

    public function batteryCabinets(): HasMany
    {
        return $this->hasMany(BatterySwapCabinet::class, 'location_id', 'id_location');
    }

    public function spluSessions(): HasMany
    {
        return $this->hasMany(SpluSession::class, 'location_id', 'id_location');
    }
}