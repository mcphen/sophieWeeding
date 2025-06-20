<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Partner extends Model
{
    protected $fillable = [
        'name',
        'website_url',
        'logo_path',
    ];

    // Accesseur pour l'URL du logo
    public function getLogoUrlAttribute()
    {
        return Storage::url($this->logo_path);
    }
}
