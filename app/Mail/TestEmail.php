<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Config;

class TestEmail extends Mailable
{
    use Queueable;

    /**
     * Create a new message instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Test Email from ' . config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $config = [
            'mail.mailer' => config('mail.default'),
            'mail.host' => config('mail.mailers.smtp.host'),
            'mail.port' => config('mail.mailers.smtp.port'),
            'mail.encryption' => config('mail.mailers.smtp.encryption'),
            'mail.from.address' => config('mail.from.address'),
            'mail.from.name' => config('mail.from.name'),
            'app.url' => config('app.url'),
            'app.env' => config('app.env'),
        ];

        return new Content(
            view: 'emails.test',
            with: [
                'config' => $config,
                'time' => now()->toDateTimeString(),
            ]
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
