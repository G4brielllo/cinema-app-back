<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Queue\SerializesModels;

class ReservationConfirmation extends Mailable
{
    use Queueable, SerializesModels;

    public $reservation;

    /**
     * Create a new message instance.
     */
    public function __construct($reservation)
    {
        $this->reservation = $reservation;
    }

    /**
     * Get the envelope for the message.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address('no-reply@cinemanager.com', 'Cinema Manager'),
            subject: 'Potwierdzenie rezerwacji'
        );
    }

    /**
     * Get the content definition for the message.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.reservation_confirmation',
            with: [
                'reservation' => $this->reservation,
            ]
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
