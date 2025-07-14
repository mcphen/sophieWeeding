<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Helpers\StorageHelper;
use Illuminate\Support\Str;

class Actualite extends Model
{
    protected $fillable = [
        'title',
        'image_path',
        'published_at',
        'slug',
    ];

    protected $appends = ['image_url'];

    /**
     * Get the blocks for the article.
     */
    public function blocks()
    {
        return $this->hasMany(ArticleBlock::class, 'article_id')->orderBy('position');
    }

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

    /**
     * Generate a slug from the title
     *
     * @param string $title
     * @return string
     */
    public static function generateSlug($title)
    {
        $slug = Str::slug($title);
        $count = static::whereRaw("slug RLIKE '^{$slug}(-[0-9]+)?$'")->count();

        return $count ? "{$slug}-{$count}" : $slug;
    }

    /**
     * Set the slug attribute when the title is set
     *
     * @param string $value
     * @return void
     */
    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = $value;

        if (!$this->exists || !$this->slug) {
            $this->attributes['slug'] = self::generateSlug($value);
        }
    }
}
