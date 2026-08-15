<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfMemberContribution extends Model
{
    protected $connection = 'scf';
    protected $table = 'member_contributions';

    protected $fillable = [
        'program_id',
        'group_id',
        'student_user_id',
        'is_contributed',
        'reason',
        'submitted_by',
        'submitted_at',
    ];

    protected $casts = [
        'is_contributed' => 'boolean',
        'submitted_at'   => 'datetime',
    ];

    /**
     * is_contributed: true = berkontribusi penuh, false = tidak/berkurang
     * reason: WAJIB diisi jika is_contributed = false
     * submitted_by: user_id ketua yang mengisi (dari auth_gara)
     * student_user_id: user_id anggota (dari auth_gara)
     */

    public function group(): BelongsTo
    {
        return $this->belongsTo(ScfGroup::class, 'group_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    public function getStudent(): ?User
    {
        return User::find($this->student_user_id);
    }

    public function getSubmitter(): ?User
    {
        return User::find($this->submitted_by);
    }

    /**
     * Cek apakah contribution untuk kelompok ini sudah disubmit.
     */
    public static function isSubmittedForGroup(int $groupId): bool
    {
        return static::where('group_id', $groupId)
            ->whereNotNull('submitted_at')
            ->exists();
    }

    /**
     * Ambil semua contribution untuk satu kelompok.
     */
    public static function forGroup(int $groupId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('group_id', $groupId)->get();
    }
}
