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
        $vista = $this->cotizacion->origen === 'latimer'
            ? 'emails.cotizacion-latimer'
            : 'emails.cotizacion';

        return new Content(
            view: $vista,
            with: ['cotizacion' => $this->cotizacion],
        );
    }

    public function attachments(): array
    {
        $vistaPdf = $this->cotizacion->origen === 'latimer'
            ? 'pdf.cotizacion-latimer'
            : 'pdf.cotizacion';

        $pdf = Pdf::loadView($vistaPdf, [
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