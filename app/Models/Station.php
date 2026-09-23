<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Station extends Model
{
    use HasFactory;

    protected $fillable = [
        'operator_id',
        'name',
        'address',
        'latitude',
        'longitude',
        'operational_hours',
        'facilities',
        'photo_url',
        'status',
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'facilities' => 'array',
    ];

    /**
     * Station dimiliki oleh satu operator.
     */
    public function operator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'operator_id');
    }

    /**
     * Satu station memiliki banyak charger.
     */
    public function chargers(): HasMany
    {
        return $this->hasMany(Charger::class);
    }
}