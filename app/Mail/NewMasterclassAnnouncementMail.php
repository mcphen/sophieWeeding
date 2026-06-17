<?php

namespace App\Mail;

use App\Models\Masterclass;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewMasterclassAnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Masterclass $masterclass,
        public string $recipientName = ''
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '🎓 Nouvelle masterclass disponible — ' . $this->masterclass->title,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.new-masterclass-announcement',
        );
    }
}
