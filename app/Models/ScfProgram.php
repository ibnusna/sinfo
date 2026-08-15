<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ScfProgram extends Model
{
    protected $connection = 'scf';
    protected $table = 'programs';

    protected $fillable = [
        'name', 'academic_year', 'status', 'start_at', 'end_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
    ];

    public function assignments(): HasMany
    {
        return $this->hasMany(ScfAssignment::class, 'program_id');
    }

    public function groups(): HasMany
    {
        return $this->hasMany(ScfGroup::class, 'program_id');
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ScfDocument::class, 'program_id');
    }

    public function posters(): HasMany
    {
        return $this->hasMany(ScfPoster::class, 'program_id');
    }

    public function settings(): HasOne
    {
        return $this->hasOne(ScfSetting::class, 'program_id');
    }

    public function assessmentCriteria(): HasMany
    {
        return $this->hasMany(ScfAssessmentCriterion::class, 'program_id');
    }

    public function scoreResults(): HasMany
    {
        return $this->hasMany(ScfScoreResult::class, 'program_id');
    }

    public static function getActive(): ?self
    {
        return static::where('status', 'active')->latest()->first();
    }
}
