<?php

namespace App\Models;

use App\Helpers\StorageHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $fillable = [
        'title',
        'description',
        'content',
        'image_path',
        'document_path',
        'start_date',
        'end_date',
        'location',
        'max_participants',
        'price',
        'is_active',
        'slug',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'max_participants' => 'integer',
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'image_url',
        'document_url',
        'formatted_price',
        'registration_count',
        'available_spots',
    ];

    /**
     * Get the registrations for the training session.
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    /**
     * Get the image URL attribute.
     */
    public function getImageUrlAttribute(): ?string
    {
        return $this->image_path ? StorageHelper::url($this->image_path) : null;
    }

    /**
     * Get the document URL attribute.
     */
    public function getDocumentUrlAttribute(): ?string
    {
        return $this->document_path ? StorageHelper::url($this->document_path) : null;
    }

    /**
     * Get the formatted price attribute.
     */
    public function getFormattedPriceAttribute(): string
    {
        return number_format($this->price, 2) . ' €';
    }

    /**
     * Get the registration count attribute.
     */
    public function getRegistrationCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    /**
     * Get the available spots attribute.
     */
    public function getAvailableSpotsAttribute(): int
    {
        if ($this->max_participants <= 0) {
            return PHP_INT_MAX; // Unlimited spots
        }

        return max(0, $this->max_participants - $this->registration_count);
    }

    /**
     * Scope a query to only include active training sessions.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include upcoming training sessions.
     */
    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    /**
     * Scope a query to only include past training sessions.
     */
    public function scopePast($query)
    {
        return $query->where('start_date', '<', now());
    }

    /**
     * Check if the training session is full.
     */
    public function isFull(): bool
    {
        if ($this->max_participants <= 0) {
            return false; // Unlimited spots
        }

        return $this->registration_count >= $this->max_participants;
    }
}
