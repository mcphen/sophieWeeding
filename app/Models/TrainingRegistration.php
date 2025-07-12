<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrainingRegistration extends Model
{
    protected $fillable = [
        'training_session_id',
        'client_id',
        'name',
        'email',
        'phone',
        'message',
        'is_confirmed',
        'confirmed_at',
    ];

    protected $casts = [
        'is_confirmed' => 'boolean',
        'confirmed_at' => 'datetime',
    ];

    /**
     * Get the training session that owns the registration.
     */
    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    /**
     * Get the client that owns the registration.
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Confirm the registration.
     */
    public function confirm(): self
    {
        $this->is_confirmed = true;
        $this->confirmed_at = now();
        $this->save();

        return $this;
    }

    /**
     * Scope a query to only include confirmed registrations.
     */
    public function scopeConfirmed($query)
    {
        return $query->where('is_confirmed', true);
    }

    /**
     * Scope a query to only include unconfirmed registrations.
     */
    public function scopeUnconfirmed($query)
    {
        return $query->where('is_confirmed', false);
    }
}
