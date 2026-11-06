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
        public ?string $pdfUrl = null,
        public ?string $logoPath = null,
        public ?string $fulfillmentType = 'DINE_IN',
        public ?string $tableName = null,
        public float $amountPaid = 0,
        public float $change = 0,
        public float $serviceCharge = 0,
        public ?string $cashierName = null,
        public array $receiptSettings = []
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
            'service_charge' => $this->serviceCharge,
            'grand_total' => $this->grandTotal,
            'amount_paid' => $this->amountPaid,
            'change' => $this->change,
            'payment_method' => $this->paymentMethod,
            'payment_status' => $this->paymentStatus,
            'order_status' => $this->orderStatus,
            'order_number' => $this->orderNumber,
            'date' => $this->date,
            'web_url' => $this->webUrl,
            'pdf_url' => $this->pdfUrl,
            'fulfillment_type' => $this->fulfillmentType,
            'table_name' => $this->tableName,
            'cashier_name' => $this->cashierName,
            'receipt_settings' => $this->receiptSettings,
        ];
    }
}
