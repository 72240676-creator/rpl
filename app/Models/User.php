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
}