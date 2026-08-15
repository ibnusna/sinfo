<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfAssignment extends Model
{
    protected $connection = 'scf';
    protected $table = 'assignments';

    protected $fillable = [
        'program_id', 'user_id', 'assignment_type', 'status',
    ];

    /**
     * assignment_type: 'guru' | 'juri'
     * status: 'active' | 'inactive'
     *
     * user_id adalah ID user dari auth_gara.users
     * TIDAK ada FK lintas database — validasi dilakukan di application layer.
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    /**
     * Ambil data user dari auth_gara berdasarkan user_id.
     * Karena cross-database, ini dilakukan secara manual.
     */
    public function getUser(): ?User
    {
        return User::find($this->user_id);
    }
}
