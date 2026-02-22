<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;

class TicketCompletadoMail extends Mailable
{
    use Queueable, SerializesModels;

    public $ticket;
    public $pdfContent;

    public function __construct($ticket, $pdfContent)
    {
        $this->ticket = $ticket;
        $this->pdfContent = $pdfContent;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '✅ ¡Tu equipo está listo! - SISTEMA MK',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.ticket_completado', 
        );
    }

    public function attachments(): array
    {
        return [

            Attachment::fromData(fn () => $this->pdfContent, "Orden_ST_{$this->ticket->nro_orden_st}.pdf")
                    ->withMime('application/pdf'),
        ];
    }
}