<?php

namespace App\Mail;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;

class OrderPurchased extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public string $name,
        public string $purchaseId,
        public array $items,
        public float $total
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS'), env('MAIL_FROM_NAME')),
            replyTo: [new Address(env('MAIL_FROM_ADDRESS'), env('APP_NAME'))],
            subject: '¡Gracias por tu compra!',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.orders.purchased',
            with: [
                'name' => $this->name,
                'receiptLink' => "https://www.mercadopago.com.uy/tools/receipt-view/{$this->purchaseId}",
                'items' => $this->items,
                'total' => $this->total,
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
