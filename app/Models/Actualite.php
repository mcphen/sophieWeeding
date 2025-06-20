<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Actualite extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'published_at',
    ];

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        return $this->image_path ? Storage::url($this->image_path) : null;
    }
}
