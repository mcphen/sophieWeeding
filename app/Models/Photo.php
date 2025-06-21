<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class Photo extends Model
{
    protected $fillable = ['album_id','image_path','caption','is_banner','banner_order'];

    protected $appends = ['image_url'];

    public function album()
    {
        return $this->belongsTo(Album::class);
    }

    public function getImagePathAttribute($value)
    {
        // In production, prepend sophieWeeding/public/storage to the path
        if (app()->environment('production') && $value && !str_starts_with($value, '/sophieWeeding/public/storage/')) {
            return '/sophieWeeding/public/storage/' . ltrim($value, '/');
        }

        return $value;
    }

    public function getImageUrlAttribute()
    {
        if (app()->environment('production')) {
            return $this->image_path;
        }

        return Storage::url($this->getRawOriginal('image_path'));
    }
}
