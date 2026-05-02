<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'User';

    protected $fillable = [
        'nama_lengkap',
        'email',
        'kata_sandi',
        'peran',
        'aktif',
    ];

    protected $hidden = [
        'kata_sandi',
        'remember_token',
    ];

    protected $casts = [
        'email_terverifikasi_pada' => 'datetime',
        'aktif'                    => 'boolean',
    ];

    /**
     * Override default password field name.
     */
    public function getAuthPassword(): string
    {
        return $this->kata_sandi;
    }

    // ── Relasi ──────────────────────────────────────────────────────────────

    public function dataAlumni()
    {
        return $this->hasOne(DataAlumni::class, 'User_id');
    }

    // ── Helper ───────────────────────────────────────────────────────────────

    public function isAdmin(): bool
    {
        return $this->peran === 'admin';
    }

    public function isAlumni(): bool
    {
        return $this->peran === 'alumni';
    }

    public function sudahMengisiForm(): bool
    {
        return $this->dataAlumni()->whereNotNull('diisi_pada')->exists();
    }
}
