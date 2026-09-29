<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ChargingSession extends Model
{
   protected $table = 'charging_sessions';
    protected $primaryKey = 'id'; // Sesuaikan jika primary key kamu berbeda

    protected $fillable = [
        'user_id',
        'vehicle_id',
        'charger_id', // <--- PASTIKAN BARIS INI ADA DI DALAM $fillable
        'status',
        'start_time',
        'end_time',
        'energy_consumed_kwh',
        'total_cost',
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
    return $this->belongsTo(
        Charger::class,
        'charger_id',
        'id_charger'
    );

    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id');
    }

    
}