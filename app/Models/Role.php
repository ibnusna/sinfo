<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $connection = 'auth';
    protected $table = 'roles';
    public $timestamps = false;

    protected $fillable = ['role_name'];
}
