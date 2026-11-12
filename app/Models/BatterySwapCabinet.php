<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BatterySwapCabinet extends Model
{
    use HasFactory;

    protected $fillable = [
        'location_id',
        'cabinet_name',
        'cabinet_code',
        'total_slots',
        'available_batteries',
        'charging_batteries',
        'empty_slots',
        'battery_type',
        'price_per_swap',
        'status',
    ];

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id', 'id_location');
    }

    public function logs()
    {
        return $this->hasMany(BatterySwapLog::class, 'cabinet_id');
    }
}

