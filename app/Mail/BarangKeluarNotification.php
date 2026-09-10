<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BarangKeluarNotification extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $data) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Notifikasi Barang {$this->data['jenis']} - {$this->data['id_transaksi']}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.barang-keluar',
            with: ['d' => $this->data],
        );
    }
}
