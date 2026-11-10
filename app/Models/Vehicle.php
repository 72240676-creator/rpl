<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    protected $primaryKey = 'id_vehicle';

    protected $fillable = [
        'id_user', 
        'merek', 
        'model', 
        'nomor_polisi', 
        'tipe_konektor',
        'battery_capacity_kwh',
        'current_battery_soc',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }
}