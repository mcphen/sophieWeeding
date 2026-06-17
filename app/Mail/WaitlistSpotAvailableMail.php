<?php

namespace App\Mail;

use App\Models\Masterclass;
use App\Models\TrainingSession;
use App\Models\WaitlistEntry;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WaitlistSpotAvailableMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public WaitlistEntry $entry,
        public Masterclass $masterclass,
        public TrainingSession $session,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎉 Une place vient de se libérer — ' . $this->masterclass->title,
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.waitlist-spot-available');
    }
}
