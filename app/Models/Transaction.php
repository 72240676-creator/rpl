<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',
        'user_id',
        'session_id',
        'invoice_number',
        'payment_method',
        'amount',
        'type',
        'description',
        'status',
        'paid_at',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user');
    }

    public function chargingSession()
    {
        return $this->belongsTo(ChargingSession::class, 'session_id', 'id');
    }
}