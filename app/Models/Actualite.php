<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class Actualite extends Model
{
    protected $fillable = [
        'title',
        'description',
        'image_path',
        'published_at',
    ];

    protected $appends = ['image_url'];

    public function getImagePathAttribute($value)
    {
        // In production, prepend sophieWeeding/public/storage to the path
        if (app()->environment('production') && $value && !str_starts_with($value, '/sophieWeeding/public/storage/')) {
            return '/sophieWeeding/public/storage/' . ltrim($value, '/');
        }

        return $value;
    }

    // Accessor for image URL
    public function getImageUrlAttribute()
    {
        if (app()->environment('production')) {
            return $this->image_path;
        }

        return $this->image_path ? Storage::url($this->getRawOriginal('image_path')) : null;
    }
}
