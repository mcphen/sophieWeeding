<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class TeamMember extends Model
{
    protected $fillable = [
        'firstname',
        'lastname',
        'position',
        'bio',
        'image_path',
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

    // Accesseur pour l'URL de l'image
    public function getImageUrlAttribute()
    {
        if (app()->environment('production')) {
            return $this->image_path;
        }

        return $this->image_path ? Storage::url($this->getRawOriginal('image_path')) : null;
    }
}
