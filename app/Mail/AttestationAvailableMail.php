<?php

namespace App\Mail;

use App\Models\Masterclass;
use App\Models\Setting;
use App\Models\TrainingRegistration;
use App\Models\TrainingSession;
use Barryvdh\DomPDF\Facade\Pdf;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AttestationAvailableMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public TrainingRegistration $registration,
        public Masterclass $masterclass,
        public TrainingSession $session
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Votre attestation de participation est disponible — Sophie Weddings Dreams',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.attestation-available',
        );
    }

    public function attachments(): array
    {
        $signaturePath = Setting::get('attestation_signature');
        $signatureFile = $signaturePath ? storage_path('app/public/' . $signaturePath) : null;

        if ($signatureFile && file_exists($signatureFile)) {
            $registration = $this->registration;
            $session      = $this->session;
            $masterclass  = $this->masterclass;

            $token     = substr(hash_hmac('sha256', $registration->id . '|' . $registration->email, config('app.key')), 0, 32);
            $url       = route('certificate.verify', ['id' => $registration->id, 'token' => $token]);
            $qrCodeUri = (new PngWriter())->write(new QrCode(data: $url, size: 200, margin: 4))->getDataUri();

            $pdf = Pdf::loadView('pdf.attestation', compact('registration', 'session', 'masterclass', 'qrCodeUri'));
            $pdf->setPaper('A4', 'landscape');

            $filename = 'certificat-' . str_pad($this->registration->id, 5, '0', STR_PAD_LEFT) . '-' . \Str::slug($this->registration->name) . '.pdf';

            return [
                Attachment::fromData(fn () => $pdf->output(), $filename)
                    ->withMime('application/pdf'),
            ];
        }

        return [];
    }
}
