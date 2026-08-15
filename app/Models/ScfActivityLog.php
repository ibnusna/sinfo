<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfActivityLog extends Model
{
    protected $connection = 'scf';
    protected $table = 'activity_logs';

    public $timestamps = false;

    protected $fillable = [
        'program_id', 'user_id', 'action', 'description',
    ];

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    public function getUser(): ?User
    {
        return User::find($this->user_id);
    }

    /**
     * Helper: log an activity.
     */
    public static function log(int $userId, string $action, string $description, int $programId = null): void
    {
        static::create([
            'program_id' => $programId,
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'created_at' => now(),
        ]);
    }
}
