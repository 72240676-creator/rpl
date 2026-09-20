<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Charger extends Model
{
    use HasFactory;

    protected $fillable = [
        'station_id',
        'device_number',
        'connector_type',
        'max_power_kw',
        'price_per_kwh',
        'is_online',
        'status',
    ];

    protected $casts = [
        'max_power_kw' => 'decimal:2',
        'price_per_kwh' => 'decimal:2',
        'is_online' => 'boolean',
    ];

    public function station(): BelongsTo
    {
        return $this->belongsTo(Station::class);
    }
}