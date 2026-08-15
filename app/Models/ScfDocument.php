<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfDocument extends Model
{
    protected $connection = 'scf';
    protected $table = 'documents';

    protected $fillable = [
        'program_id', 'type', 'title', 'content',
        'status', 'created_by', 'published_at',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    /**
     * type: 'juklak' | 'juknis'
     * status: 'draft' | 'published'
     * created_by: user_id dari auth_gara
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function getCreator(): ?User
    {
        return User::find($this->created_by);
    }
}
