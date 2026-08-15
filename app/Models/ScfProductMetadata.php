<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfProductMetadata extends Model
{
    protected $connection = 'scf';
    protected $table = 'product_metadata';

    protected $fillable = [
        'program_id',
        'group_id',
        'product_name',
        'description',
        'ingredients',
        'carbohydrate',
        'protein',
        'fat',
        'other_nutrients',
        'created_by',
    ];

    /**
     * created_by: user_id ketua dari auth_gara.
     * Semua kandungan gizi adalah RAW DATA dari ketua — sistem TIDAK boleh mengarang nilai.
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ScfGroup::class, 'group_id');
    }

    public function getCreator(): ?User
    {
        return User::find($this->created_by);
    }

    /**
     * Helper: ambil metadata untuk satu kelompok.
     */
    public static function forGroup(int $groupId): ?self
    {
        return static::where('group_id', $groupId)->first();
    }
}
