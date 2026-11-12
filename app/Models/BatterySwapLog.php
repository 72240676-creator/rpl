<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatterySwapLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'cabinet_id',
        'vehicle_id',
        'old_soc',
        'new_soc',
        'amount',
        'swap_code',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id_user');
    }

    public function cabinet()
    {
        return $this->belongsTo(BatterySwapCabinet::class, 'cabinet_id');
    }

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class, 'vehicle_id', 'id_vehicle');
    }
}

