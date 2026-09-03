<?php

namespace App\Services\Receipt;

class ReceiptData
{
    public function __construct(
        public array $shop,
        public array $customer,
        public array $items,
        public float $subtotal,
        public float $discount,
        public float $tax,
        public float $grandTotal,
        public ?string $paymentMethod,
        public string $paymentStatus,
        public string $orderStatus,
        public string $orderNumber,
        public string $date,
        public ?string $webUrl = null,
        public ?string $pdfUrl = null
    ) {}

    public function toArray(): array
    {
        return [
            'shop' => $this->shop,
            'customer' => $this->customer,
            'items' => $this->items,
            'subtotal' => $this->subtotal,
            'discount' => $this->discount,
            'tax' => $this->tax,
            'grand_total' => $this->grandTotal,
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentStatus,
            'order_status' => $this->orderStatus,
            'order_number' => $this->orderNumber,
            'date' => $this->date,
            'web_url' => $this->webUrl,
            'pdf_url' => $this->pdfUrl,
        ];
    }
}
