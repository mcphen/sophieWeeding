<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;

class ProductImage extends Model
{
    protected $fillable = [
        'product_id',
        'image_path',
        'order',
    ];

    protected $appends = ['image_url'];

    /**
     * Get the product that owns the image.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getImagePathAttribute($value)
    {
        // In production, prepend sophieWeeding/public/storage to the path
        if (app()->environment('production') && $value && !str_starts_with($value, '/sophieWeeding/public/storage/')) {
            return '/sophieWeeding/public/storage/' . ltrim($value, '/');
        }

        return $value;
    }

    // Accessor for the image URL
    public function getImageUrlAttribute()
    {
        if (app()->environment('production')) {
            return $this->image_path;
        }

        return $this->image_path ? Storage::url($this->getRawOriginal('image_path')) : null;
    }
}
