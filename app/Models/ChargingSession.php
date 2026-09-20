<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Charger;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChargingSession extends Model
{
    protected $fillable = [
        'user_id',
        'charger_id',
        'vehicle_id',
        'start_time',
        'end_time',
        'energy_consumed_kwh',
        'total_cost',
        'status',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'energy_consumed_kwh' => 'decimal:3',
        'total_cost' => 'decimal:2',
    ];

    /**
     * Session dimiliki oleh satu user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    /**
     * Session menggunakan satu charger.
     */
    public function charger(): BelongsTo
    {
        return $this->belongsTo(Charger::class);
    }

    /**
     * Session menggunakan satu kendaraan.
     */
    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id_vehicle');
    }
}