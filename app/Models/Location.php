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
        'alamat',
        'latitude',
        'longitude',
        'jam_operasional',
        'fasilitas',
        'status',
    ];

    public function chargers(): HasMany
    {
        return $this->hasMany(Charger::class, 'id_location', 'id_location');
    }
}