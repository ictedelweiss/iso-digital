<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class RequestStatusMail extends Mailable
{
    use Queueable, SerializesModels;

    public $record;
    public $status;
    public $url;
    public $reason;

    /**
     * Create a new message instance.
     */
    public function __construct(Model $record, string $status, string $url, ?string $reason = null)
    {
        $this->record = $record;
        $this->status = $status;
        $this->url = $url;
        $this->reason = $reason;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $type = class_basename($this->record);
        $number = $this->record->pr_number ?? $this->record->id;

        return new Envelope(
            subject: "[{$this->status}] {$type} - {$number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.request_status',
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
