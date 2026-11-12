<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpluSession extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'location_id',
        'socket_number',
        'duration_minutes',
        'amount',
        'energy_kwh',
        'started_at',
        'expires_at',
        'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'expires_at' => 'datetime',
        'amount' => 'decimal:2',
        'energy_kwh' => 'decimal:3',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id_location');
    }
}

