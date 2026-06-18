<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Masterclass extends Model
{
    protected $fillable = [
        'title',
        'niveau',
        'description',
        'programme',
        'image_path',
        'document_path',
        'slug',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'image_url',
        'document_url',
        'upcoming_sessions_count',
    ];

    public function sessions(): HasMany
    {
        return $this->hasMany(TrainingSession::class);
    }

    public function getImagePathAttribute($value)
    {
        if (app()->environment('production') && $value && !str_starts_with($value, '/sophieWeeding/public/storage/')) {
            return '/sophieWeeding/public/storage/' . ltrim($value, '/');
        }

        return $value;
    }

    public function getDocumentPathAttribute($value)
    {
        if (app()->environment('production') && $value && !str_starts_with($value, '/sophieWeeding/public/storage/')) {
            return '/sophieWeeding/public/storage/' . ltrim($value, '/');
        }

        return $value;
    }

    public function getImageUrlAttribute(): ?string
    {
        if (!$this->getRawOriginal('image_path')) {
            return null;
        }

        if (app()->environment('production')) {
            return $this->image_path;
        }

        return Storage::url($this->getRawOriginal('image_path'));
    }

    public function getDocumentUrlAttribute(): ?string
    {
        if (!$this->getRawOriginal('document_path')) {
            return null;
        }

        if (app()->environment('production')) {
            return $this->document_path;
        }

        return Storage::url($this->getRawOriginal('document_path'));
    }

    public function getUpcomingSessionsCountAttribute(): int
    {
        return $this->sessions()->active()->upcoming()->count();
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
