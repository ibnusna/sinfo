<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ScfGroupMember extends Model
{
    protected $connection = 'scf';
    protected $table = 'group_members';

    public $timestamps = false;

    protected $fillable = [
        'group_id', 'student_user_id',
    ];

    /**
     * student_user_id: user_id dari auth_gara (harus role siswa)
     * Tidak ada FK lintas database — validasi di application layer.
     */

    protected $casts = [
        'created_at' => 'datetime',
    ];

    public function group(): BelongsTo
    {
        return $this->belongsTo(ScfGroup::class, 'group_id');
    }

    /**
     * Ambil data siswa dari auth_gara.
     */
    public function getStudent(): ?User
    {
        return User::find($this->student_user_id);
    }

    public function getSiswaDetail(): ?Siswa
    {
        return Siswa::where('user_id', $this->student_user_id)->first();
    }
}
