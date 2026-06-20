<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProspectMagicLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $loginUrl;

    public function __construct(string $loginUrl)
    {
        $this->loginUrl = $loginUrl;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Accès à votre espace inscrit — Sophie Weddings Dreams',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.prospect-magic-link',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
