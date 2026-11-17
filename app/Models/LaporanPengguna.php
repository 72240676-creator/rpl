<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaporanPengguna extends Model
{
    use HasFactory;

    protected $table = 'laporan_pengguna';

    protected $primaryKey = 'id_laporan';

    protected $fillable = [
        'id_user',
        'id_location',
        'id_charger',
        'jenis_masalah',
        'deskripsi',
        'foto',
        'status',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_user', 'id_user');
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'id_location', 'id_location');
    }

    public function charger(): BelongsTo
    {
        return $this->belongsTo(Charger::class, 'id_charger', 'id_charger');
    }
}