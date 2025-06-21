<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'website_url',
        'logo_path',
    ];

    protected $appends = ['logo_url'];

    public function getLogoPathAttribute($value)
    {
        // In production, prepend sophieWeeding/public/storage to the path
        if (app()->environment('production') && $value && !str_starts_with($value, '/sophieWeeding/public/storage/')) {
            return '/sophieWeeding/public/storage/' . ltrim($value, '/');
        }

        return $value;
    }

    // Accesseur pour l'URL du logo
    public function getLogoUrlAttribute()
    {
        if (app()->environment('production')) {
            return $this->logo_path;
        }

        return $this->logo_path ? Storage::url($this->getRawOriginal('logo_path')) : null;
    }
}
