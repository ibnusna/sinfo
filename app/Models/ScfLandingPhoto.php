<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScfLandingPhoto extends Model
{
    protected $connection = 'scf';
    protected $table = 'landing_photos';

    protected $fillable = [
        'title',
        'section',
        'category',
        'image_path',
        'sort_order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function getUrlAttribute(): string
    {
        if (str_starts_with($this->image_path, 'http://') || str_starts_with($this->image_path, 'https://')) {
            return $this->image_path;
        }

        return asset($this->image_path);
    }
}
