<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'id_user',      // <-- Tambahkan ini
        'amount',       // <-- Pastikan kolom lain yang di-create juga ada
        'type',
        'description',
        // Tambahkan kolom lain jika ada di tabel transactions Anda
    ];
}
