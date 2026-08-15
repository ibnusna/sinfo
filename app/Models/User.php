<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasOne;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Koneksi ke auth_gara — READ ONLY dari SCF.
     * Sumber kebenaran tunggal untuk identitas, password, dan role GARA.
     */
    protected $connection = 'auth';

    protected $table = 'users';

    protected $primaryKey = 'id';

    /**
     * auth_gara menggunakan 'password_hash', bukan 'password'.
     * Override agar Laravel Auth dapat memvalidasi password.
     */
    protected $authPasswordName = 'password_hash';

    protected $fillable = [
        'username',
        'nama_lengkap',
        'password_hash',
        'role_id',
        'status_aktif',
    ];

    protected $hidden = [
        'password_hash',
        'remember_token',
    ];

    /**
     * Timestamps: auth_gara hanya punya created_at.
     */
    public $timestamps = false;

    /**
     * Override getAuthPassword() agar Laravel Auth menggunakan kolom password_hash.
     */
    public function getAuthPassword(): string
    {
        return $this->password_hash;
    }

    /**
     * Override getRememberToken() untuk kompatibilitas kolom auth_gara.
     */
    public function getRememberToken(): ?string
    {
        return $this->remember_token;
    }

    public function setRememberToken($value): void
    {
        $this->remember_token = $value;
    }

    public function getRememberTokenName(): string
    {
        return 'remember_token';
    }

    /**
     * Relasi ke tabel siswa (auth_gara).
     */
    public function siswa(): HasOne
    {
        return $this->hasOne(Siswa::class, 'user_id', 'id');
    }

    /**
     * Relasi ke tabel guru (auth_gara).
     */
    public function guru(): HasOne
    {
        return $this->hasOne(Guru::class, 'user_id', 'id');
    }

    /**
     * Relasi ke role (auth_gara).
     */
    public function role()
    {
        return $this->belongsTo(Role::class, 'role_id', 'id');
    }

    /**
     * Ambil nama role GARA dari relasi.
     * Contoh: 'guru', 'operator', 'siswa', 'super_admin', 'kepsek'
     */
    public function getRoleName(): string
    {
        return $this->role?->role_name ?? 'unknown';
    }

    /**
     * Hitung effective SCF role user ini.
     *
     * Logika:
     * - Jika punya assignment 'juri' di SCF → return 'juri'
     * - Jika punya assignment 'guru' di SCF → return 'guru'
     * - Default: role GARA asli
     */
    public function getScfRole(): string
    {
        $roleName = $this->getRoleName();

        // super_admin dan operator bisa langsung akses tanpa assignment SCF
        if (in_array($roleName, ['super_admin', 'operator', 'kepsek'])) {
            return $roleName;
        }

        // Cek assignment SCF untuk guru yang bisa menjadi juri
        if ($roleName === 'guru') {
            $assignment = \App\Models\ScfAssignment::where('user_id', $this->id)
                ->where('status', 'active')
                ->first();

            if ($assignment) {
                return $assignment->assignment_type; // 'guru' atau 'juri'
            }

            // Guru yang belum di-assign tidak punya akses SCF khusus
            return 'guru_unassigned';
        }

        // Siswa tetap siswa
        return $roleName;
    }

    /**
     * Nama display untuk UI.
     * Prioritas: nama_lengkap → nama dari tabel siswa/guru → username
     */
    public function getDisplayName(): string
    {
        if ($this->nama_lengkap) {
            return $this->nama_lengkap;
        }

        if ($this->siswa) {
            return $this->siswa->nama;
        }

        if ($this->guru) {
            return $this->guru->nama_lengkap;
        }

        return $this->username;
    }

    /**
     * Cek apakah user aktif.
     */
    public function isActive(): bool
    {
        return (bool) $this->status_aktif;
    }
}
