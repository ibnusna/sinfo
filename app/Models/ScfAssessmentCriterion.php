<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfAssessmentCriterion extends Model
{
    protected $connection = 'scf';
    protected $table = 'assessment_criteria';

    protected $fillable = [
        'program_id',
        'assessment_type',
        'name',
        'description',
        'weight',
        'max_score',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'weight'     => 'float',
        'max_score'  => 'float',
        'sort_order' => 'integer',
        'is_active'  => 'boolean',
    ];

    /**
     * assessment_type: 'poster' | 'science' | 'food'
     * weight: persentase (0-100), total semua kriteria dalam satu type harus = 100
     * max_score: nilai maksimum per kriteria (default 100)
     * program_id: null = global (berlaku untuk semua program), non-null = khusus program
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    /**
     * Scope: hanya kriteria aktif.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: filter berdasarkan assessment_type.
     */
    public function scopeOfType(Builder $query, string $type): Builder
    {
        return $query->where('assessment_type', $type);
    }

    /**
     * Helper: ambil semua kriteria aktif untuk satu tipe, diurutkan.
     * Prioritas: kriteria program-spesifik > global.
     */
    public static function getForType(string $type, ?int $programId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = static::active()
            ->ofType($type)
            ->orderBy('sort_order')
            ->orderBy('id');

        if ($programId) {
            // Program-specific criteria override global
            $programCriteria = (clone $query)->where('program_id', $programId)->get();
            if ($programCriteria->isNotEmpty()) {
                return $programCriteria;
            }
        }

        // Fallback ke global criteria (program_id = null)
        return $query->whereNull('program_id')->get();
    }
}
