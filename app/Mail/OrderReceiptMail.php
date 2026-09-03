<?php

namespace App\Mail;

use App\Services\Receipt\ReceiptData;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ReceiptData $receiptData)
    {
    }

    public function envelope(): Envelope
    {
        $shopName = $this->receiptData->shop['name'];
        $orderNo = $this->receiptData->orderNumber;
        
        return new Envelope(
            subject: "Receipt {$shopName} - Order #{$orderNo}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.order-receipt',
        );
    }
}
