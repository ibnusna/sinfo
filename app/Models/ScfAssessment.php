<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ScfAssessment extends Model
{
    protected $connection = 'scf';
    protected $table = 'assessments';

    protected $fillable = [
        'program_id', 'group_id', 'assessor_user_id',
        'assessor_type', 'assessment_type', 'status', 'submitted_at',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    /**
     * assessor_type: 'guru' | 'juri'
     * assessment_type: 'poster' | 'science' | 'food'
     * status: 'draft' | 'submitted'
     * assessor_user_id: user_id dari auth_gara
     *
     * CATATAN: assessment_type adalah tipe penilaian, assessor_type adalah tipe penilai.
     * Guru dapat menilai 'poster' (assessment_type=poster) dan 'science' (assessment_type=science).
     * Juri hanya menilai 'food' (assessment_type=food).
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ScfGroup::class, 'group_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(ScfAssessmentDetail::class, 'assessment_id');
    }

    public function getAssessor(): ?User
    {
        return User::find($this->assessor_user_id);
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    /**
     * Scope: hanya yang submitted.
     */
    public function scopeSubmitted(Builder $query): Builder
    {
        return $query->where('status', 'submitted');
    }

    /**
     * Scope: filter by assessment_type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('assessment_type', $type);
    }

    /**
     * Hitung weighted score berdasarkan criteria yang tersimpan di assessment_criteria.
     * Formula: sum( (detail.score / criterion.max_score) * criterion.weight )
     *
     * Return nilai akhir dalam skala 0-100 (sudah terbobot).
     */
    public function calculateWeightedScore(?int $programId = null): float
    {
        if (!$this->assessment_type) {
            return 0;
        }

        $criteria = ScfAssessmentCriterion::getForType($this->assessment_type, $programId);

        if ($criteria->isEmpty()) {
            // Fallback: rata-rata sederhana jika tidak ada criteria terdefinisi
            $count = $this->details->count();
            return $count > 0 ? round($this->details->avg('score'), 4) : 0;
        }

        $weightedScore = 0;
        $criteriaMap = $criteria->keyBy('name');

        foreach ($this->details as $detail) {
            $criterion = $criteriaMap->get($detail->criterion);
            if (!$criterion) {
                continue;
            }
            // (score / max_score) * weight = kontribusi kriteria ini terhadap total
            $contribution = ($detail->score / $criterion->max_score) * $criterion->weight;
            $weightedScore += $contribution;
        }

        return round($weightedScore, 4);
    }
}
