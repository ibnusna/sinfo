<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfAssessmentDetail extends Model
{
    protected $connection = 'scf';
    protected $table = 'assessment_details';

    protected $fillable = [
        'assessment_id', 'criterion', 'score', 'note',
    ];

    protected $casts = [
        'score' => 'float',
    ];

    public function assessment(): BelongsTo
    {
        return $this->belongsTo(ScfAssessment::class, 'assessment_id');
    }
}
