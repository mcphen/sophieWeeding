<?php

namespace App\Models;

use App\Mail\WaitlistNewSessionMail;
use App\Mail\WaitlistSpotAvailableMail;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Mail;

class WaitlistEntry extends Model
{
    protected $fillable = [
        'training_session_id',
        'masterclass_id',
        'name',
        'email',
        'phone',
        'notified_at',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
    ];

    public function trainingSession(): BelongsTo
    {
        return $this->belongsTo(TrainingSession::class);
    }

    public function masterclass(): BelongsTo
    {
        return $this->belongsTo(Masterclass::class);
    }

    public function isNotified(): bool
    {
        return $this->notified_at !== null;
    }

    /**
     * Notifie la première personne en attente quand une place se libère.
     */
    public static function notifyNextForSession(TrainingSession $session): void
    {
        $entry = self::where('training_session_id', $session->id)
            ->whereNull('notified_at')
            ->orderBy('created_at')
            ->first();

        if (!$entry) {
            return;
        }

        $session->load('masterclass');

        try {
            Mail::to($entry->email)->send(new WaitlistSpotAvailableMail($entry, $session->masterclass, $session));
            $entry->update(['notified_at' => now()]);
        } catch (\Exception) {
            // Silent fail
        }
    }

    /**
     * Notifie toutes les personnes en attente (toutes sessions) d'une masterclass
     * quand une nouvelle session est ajoutée.
     */
    public static function notifyAllForMasterclass(Masterclass $masterclass, TrainingSession $newSession): void
    {
        $entries = self::where('masterclass_id', $masterclass->id)
            ->whereNull('notified_at')
            ->orderBy('created_at')
            ->get();

        foreach ($entries as $entry) {
            try {
                Mail::to($entry->email)->send(new WaitlistNewSessionMail($entry, $masterclass, $newSession));
                $entry->update(['notified_at' => now()]);
            } catch (\Exception) {
                // Silent fail
            }
        }
    }
}
