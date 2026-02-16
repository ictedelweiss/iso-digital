<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Database\Eloquent\Model;

class ApprovalRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $record;
    public $role;
    public $url;
    public $approverName;

    /**
     * Create a new message instance.
     */
    public function __construct(Model $record, string $role, string $url, ?string $approverName = 'Approver')
    {
        $this->record = $record;
        $this->role = $role;
        $this->url = $url;
        $this->approverName = $approverName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $type = class_basename($this->record);
        $title = $this->record->title ?? $this->record->item_name ?? 'Request';
        $number = $this->record->pr_number ?? $this->record->id;

        return new Envelope(
            subject: "[Approval Required] {$type} - {$number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.approval_request',
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
