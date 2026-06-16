<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewPaidOrderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New paid order · ' . $this->order->number,
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.orders.new-paid-order',
            with: [
                'order' => $this->order,
                'adminUrl' => url('/admin/orders/' . $this->order->getKey()),
            ],
        );
    }
}
