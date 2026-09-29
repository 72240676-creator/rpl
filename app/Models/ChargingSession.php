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
        'end_time' => 'datetime',
        'energy_consumed_kwh' => 'decimal:3',
        'total_cost' => 'decimal:2',
    ];

    /**
     * User yang melakukan charging.
     */
    public function user()
    {
        return $this->belongsTo(
            User::class,
            'user_id',
            'id_user'
        );
    }

    /**
     * Charger yang digunakan.
     */
    public function charger()
    {
        return $this->belongsTo(
            Charger::class,
            'charger_id', // Foreign key di tabel charging_sessions
            'id_charger'  // Primary key di tabel chargers
        );
    }

    /**
     * Kendaraan yang digunakan.
     */
    public function vehicle()
    {
        return $this->belongsTo(
            Vehicle::class,
            'vehicle_id',
            'id_vehicle'
        );
    }

    
}