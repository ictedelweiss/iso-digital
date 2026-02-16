<?php

namespace App\Mail;

use App\Models\IctTicket;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewTicketNotification extends Mailable
{
    use Queueable, SerializesModels;

    public IctTicket $ticket;
    public string $dashboardUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(IctTicket $ticket)
    {
        $this->ticket = $ticket;
        $this->dashboardUrl = url('/admin/ict-tickets');
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[ICT Helpdesk] Tiket Baru #{$this->ticket->ticket_number} - {$this->ticket->subject}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.new_ticket_notification',
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