<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class PedidoConfirmacion extends Mailable
{
    use Queueable, SerializesModels;

    public $cliente;
    public $pedido;
    public $pdf;
    public $numeroPedido;

    public function __construct($cliente, $pedido, $pdf, $numeroPedido = null)
    {
        $this->cliente = $cliente;
        $this->pedido = $pedido;
        $this->pdf = $pdf;
        $this->numeroPedido = $numeroPedido;
    }

    public function build()
    {
        return $this->subject('Confirmación de tu pedido - Menú Coffee')
                    ->view('emails.order-confirmation')
                    ->attachData($this->pdf->output(), 'pedido-' . $this->numeroPedido . '.pdf', [
                        'mime' => 'application/pdf',
                    ]);
    }
}
