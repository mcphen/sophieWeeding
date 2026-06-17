<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? StorageHelper::url($this->image_path) : null;
    }

    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document_path ? StorageHelper::url($this->document_path) : null;
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
