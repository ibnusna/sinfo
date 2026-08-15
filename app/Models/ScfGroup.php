<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScfGroup extends Model
{
    protected $connection = 'scf';
    protected $table = 'groups';

    protected $fillable = [
        'program_id', 'name', 'leader_user_id', 'created_by',
    ];

    /**
     * leader_user_id: user_id dari auth_gara (harus siswa)
     * created_by: user_id dari auth_gara (guru yang membuat)
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    public function members(): HasMany
    {
        return $this->hasMany(ScfGroupMember::class, 'group_id');
    }

    public function poster(): HasOne
    {
        return $this->hasOne(ScfPoster::class, 'group_id')->latest();
    }

    public function assessments(): HasMany
    {
        return $this->hasMany(ScfAssessment::class, 'group_id');
    }

    public function productMetadata(): HasOne
    {
        return $this->hasOne(ScfProductMetadata::class, 'group_id');
    }

    public function contributions(): HasMany
    {
        return $this->hasMany(ScfMemberContribution::class, 'group_id');
    }

    public function scoreResults(): HasMany
    {
        return $this->hasMany(ScfScoreResult::class, 'group_id');
    }

    /**
     * Ambil data leader dari auth_gara.
     */
    public function getLeader(): ?User
    {
        return User::find($this->leader_user_id);
    }

    /**
     * Ambil data pembuat (guru) dari auth_gara.
     */
    public function getCreator(): ?User
    {
        return User::find($this->created_by);
    }

    /**
     * Cek apakah user_id adalah ketua kelompok ini.
     */
    public function isLeader(int $userId): bool
    {
        return (int) $this->leader_user_id === $userId;
    }

    /**
     * Cek apakah kelompok sudah difinalisasi.
     */
    public function isFinalized(): bool
    {
        return ScfScoreResult::isGroupFinalized($this->id);
    }
}
