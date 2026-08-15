<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfSetting extends Model
{
    protected $connection = 'scf';
    protected $table = 'settings';

    protected $fillable = [
        'program_id', 'key', 'value',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    /**
     * Helper: get a setting value by key for a program.
     */
    public static function getValue(string $key, ?int $programId = null, mixed $default = null): mixed
    {
        $query = static::where('key', $key);
        if ($programId) {
            $query->where('program_id', $programId);
        }
        $setting = $query->first();
        return $setting ? $setting->value : $default;
    }

    /**
     * Helper: get system_status (open/closed).
     */
    public static function getSystemStatus(?int $programId = null): string
    {
        return static::getValue('system_status', $programId, 'closed');
    }

    /**
     * Helper: set system_status.
     */
    public static function setSystemStatus(string $status, int $programId): void
    {
        static::updateOrCreate(
            ['program_id' => $programId, 'key' => 'system_status'],
            ['value' => $status]
        );
    }
}
