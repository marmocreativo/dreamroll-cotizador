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

    public Cotizacion $cotizacion;
    public ?\App\Models\User $usuario;

    public function __construct(Cotizacion $cotizacion, ?\App\Models\User $usuario = null)
    {
        $this->cotizacion = $cotizacion->loadMissing('productos.producto');
        $this->usuario = $usuario;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Cotización ' . $this->cotizacion->folio,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.cotizacion',
            with: ['cotizacion' => $this->cotizacion],
        );
    }

    public function attachments(): array
    {
        $pdf = Pdf::loadView('pdf.cotizacion', [
            'cotizacion' => $this->cotizacion,
            'usuario'    => $this->usuario,
        ]);

        return [
            Attachment::fromData(
                fn () => $pdf->output(),
                $this->cotizacion->folio . '.pdf'
            )->withMime('application/pdf'),
        ];
    }
}