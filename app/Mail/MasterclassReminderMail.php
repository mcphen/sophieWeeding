<?php

namespace App\Mail;

use App\Models\Masterclass;
use App\Models\TrainingRegistration;
use App\Models\TrainingSession;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MasterclassReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TrainingRegistration $registration,
        public Masterclass $masterclass,
        public TrainingSession $session,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Rappel – ' . $this->masterclass->title . ' le ' . $this->session->start_date->format('d/m/Y'),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.masterclass-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
