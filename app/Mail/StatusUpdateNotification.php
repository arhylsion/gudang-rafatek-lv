<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class StatusUpdateNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Update Status Transaksi {$this->data['status']} - {$this->data['id_transaksi']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.status-update',
            with: ['d' => $this->data],
        );
    }
}
