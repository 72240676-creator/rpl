<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'users';
    protected $primaryKey = 'id_user'; // Definisikan primary key kustom

    protected $fillable = [
        'nama',
        'email',
        'password',
        'nomor_telepon',
        'peran',
        'status_akun',
        'saldo',
        'points',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relasi ke model Vehicle (Kendaraan milik user)
     */
    public function vehicles()
    {
        return $this->hasMany(Vehicle::class, 'id_user', 'id_user');
    }

    /**
     * Helper pemeriksaan peran (Role check).
     */
    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isOperator(): bool
    {
        return $this->peran === 'operator';
    }

    public function isDriver(): bool
    {
        return $this->peran === 'pengemudi' || empty($this->peran);
    }

    /**
     * Accessor alias untuk kemudahan & konsistensi penamaan.
     */
    public function getRoleAttribute(): string
    {
        return (string) ($this->peran ?? 'pengemudi');
    }

    public function getBalanceAttribute(): float
    {
        return (float) ($this->saldo ?? 0);
    }
}