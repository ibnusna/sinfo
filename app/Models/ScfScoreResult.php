<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfScoreResult extends Model
{
    protected $connection = 'scf';
    protected $table = 'score_results';

    protected $fillable = [
        'program_id',
        'group_id',
        'student_user_id',
        'poster_score',
        'science_score',
        'food_score',
        'group_score',
        'poster_weight_used',
        'science_weight_used',
        'food_weight_used',
        'contribution_factor',
        'individual_score',
        'group_status',
        'calculated_at',
        'finalized_at',
        'finalized_by',
    ];

    protected $casts = [
        'poster_score'        => 'float',
        'science_score'       => 'float',
        'food_score'          => 'float',
        'group_score'         => 'float',
        'poster_weight_used'  => 'float',
        'science_weight_used' => 'float',
        'food_weight_used'    => 'float',
        'contribution_factor' => 'float',
        'individual_score'    => 'float',
        'calculated_at'       => 'datetime',
        'finalized_at'        => 'datetime',
    ];

    /**
     * group_status: 'incomplete' | 'calculated' | 'finalized'
     * Nilai tidak boleh berubah setelah status = finalized.
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

    public function getFinalizedBy(): ?User
    {
        return $this->finalized_by ? User::find($this->finalized_by) : null;
    }

    public function isFinalized(): bool
    {
        return $this->group_status === 'finalized';
    }

    public function isCalculated(): bool
    {
        return in_array($this->group_status, ['calculated', 'finalized']);
    }

    /**
     * Scope: hanya yang sudah finalized.
     */
    public function scopeFinalized(Builder $query): Builder
    {
        return $query->where('group_status', 'finalized');
    }

    /**
     * Helper: ambil semua score results untuk satu kelompok.
     */
    public static function forGroup(int $groupId): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('group_id', $groupId)->get();
    }

    /**
     * Helper: cek apakah kelompok sudah finalized.
     */
    public static function isGroupFinalized(int $groupId): bool
    {
        return static::where('group_id', $groupId)
            ->where('group_status', 'finalized')
            ->exists();
    }
}
