<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TrainingSession extends Model
{
    protected $fillable = [
        'masterclass_id',
        'start_date',
        'end_date',
        'adresse',
        'location_type',
        'google_maps_url',
        'online_link',
        'replay_url',
        'max_participants',
        'price',
        'is_active',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_active' => 'boolean',
        'max_participants' => 'integer',
        'price' => 'decimal:2',
    ];

    protected $appends = [
        'formatted_price',
        'registration_count',
        'available_spots',
        'location_label',
    ];

    public function masterclass(): BelongsTo
    {
        return $this->belongsTo(Masterclass::class);
    }

    public function registrations(): HasMany
    {
        return $this->hasMany(TrainingRegistration::class);
    }

    public function getFormattedPriceAttribute(): string
    {
        if ($this->price === null || $this->price == 0) {
            return 'Gratuit';
        }
        return number_format((int) $this->price, 0, ',', ' ') . ' F CFA';
    }

    public function getRegistrationCountAttribute(): int
    {
        return $this->registrations()->count();
    }

    public function getAvailableSpotsAttribute(): ?int
    {
        if ($this->max_participants === null || $this->max_participants <= 0) {
            return null; // Illimité
        }
        return max(0, $this->max_participants - $this->registration_count);
    }

    public function getLocationLabelAttribute(): string
    {
        return match ($this->location_type) {
            'online' => 'En ligne',
            'both' => 'Présentiel & En ligne',
            default => 'Présentiel',
        };
    }

    public function isFull(): bool
    {
        if ($this->max_participants === null || $this->max_participants <= 0) {
            return false;
        }
        return $this->registration_count >= $this->max_participants;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>=', now());
    }

    public function scopePast($query)
    {
        return $query->where('start_date', '<', now());
    }
}
