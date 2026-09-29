<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'end_time'   => 'datetime',
        'energy_consumed_kwh' => 'decimal:3',
        'total_cost'          => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function charger()
    {
        return $this->belongsTo(Charger::class, 'charger_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }
}