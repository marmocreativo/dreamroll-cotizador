<?php

namespace App\Mail;

use App\Models\Cotizacion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CotizacionMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Cotizacion $cotizacion) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotización ' . $this->cotizacion->folio . ' — GeneticLab',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cotizacion',
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.cotizacion', ['cotizacion' => $this->cotizacion]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                $this->cotizacion->folio . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}