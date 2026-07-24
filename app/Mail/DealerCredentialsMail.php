<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DealerCredentialsMail extends Mailable implements \Illuminate\Contracts\Queue\ShouldQueue
{
    use Queueable, SerializesModels;

    public string $dealerName;

    public string $dealerEmail;

    public string $dealerPassword;

    public string $loginUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(string $dealerName, string $dealerEmail, string $dealerPassword)
    {
        $this->dealerName = $dealerName;
        $this->dealerEmail = $dealerEmail;
        $this->dealerPassword = $dealerPassword;
        $this->loginUrl = url('/dealer/login');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your Dealer Account Has Been Created - '.config('app.name'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.dealer-credentials',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
