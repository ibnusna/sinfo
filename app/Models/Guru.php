<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guru extends Model
{
    protected $connection = 'auth';
    protected $table = 'guru';
    public $timestamps = false;

    protected $fillable = ['user_id', 'nip', 'nama_lengkap', 'no_hp', 'alamat'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
