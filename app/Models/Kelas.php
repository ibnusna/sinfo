<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kelas extends Model
{
    protected $connection = 'auth';
    protected $table = 'kelas';
    public $timestamps = false;

    protected $fillable = ['nama_kelas', 'tingkat'];
}
