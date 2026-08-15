<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class ScfPoster extends Model
{
    protected $connection = 'scf';
    protected $table = 'posters';

    protected $fillable = [
        'program_id', 'group_id', 'uploaded_by',
        'original_name', 'file_path', 'mime_type', 'file_size',
        'status', 'uploaded_at', 'verified_at', 'verified_by',
    ];

    protected $casts = [
        'uploaded_at' => 'datetime',
        'verified_at' => 'datetime',
        'file_size' => 'integer',
    ];

    /**
     * status: 'pending' | 'submitted' | 'revision' | 'approved'
     * uploaded_by: user_id dari auth_gara (harus leader_user_id kelompok)
     * verified_by: user_id dari auth_gara (guru/operator)
     */

    public function program(): BelongsTo
    {
        return $this->belongsTo(ScfProgram::class, 'program_id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(ScfGroup::class, 'group_id');
    }

    public function getUploader(): ?User
    {
        return User::find($this->uploaded_by);
    }

    /**
     * Dapatkan URL publik file poster.
     */
    public function getPublicUrl(): string
    {
        return Storage::disk('public')->url($this->file_path);
    }

    /**
     * Dapatkan ukuran file dalam format yang readable.
     */
    public function getFileSizeFormatted(): string
    {
        $bytes = $this->file_size;
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024) return round($bytes / 1024, 2) . ' KB';
        return $bytes . ' B';
    }

    public function isImage(): bool
    {
        return in_array($this->mime_type, ['image/jpeg', 'image/png', 'image/webp']);
    }

    public function isPdf(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}
