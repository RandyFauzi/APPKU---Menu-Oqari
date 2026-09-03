<?php

namespace App\Services\Receipt;

use App\Models\Order;
use Illuminate\Support\Facades\URL;

class ReceiptService
{
    public function build(Order $order): ReceiptData
    {
        $order->loadMissing(['shop', 'items.product']);
        
        $shop = $order->shop;
        
        $shopData = [
            'name' => $shop->name ?? 'OQARI',
            'logo_url' => $shop->logo_url,
            'address' => $shop->address,
            'phone' => $shop->whatsapp_number,
            'email' => $shop->email,
            'slogan' => $shop->slogan,
            'primary_color' => $shop->primary_color ?? '#000000',
        ];

        $customerData = [
            'name' => $order->customer_name ?? 'Guest',
            'email' => $order->customer_email,
            'phone' => $order->customer_phone,
        ];

        $itemsData = $order->items->map(function ($item) {
            return [
                'name' => $item->product ? $item->product->name : 'Item',
                'quantity' => $item->quantity,
                'price' => (float) $item->unit_price,
                'subtotal' => (float) $item->subtotal,
                'notes' => $item->notes,
            ];
        })->toArray();

        $webUrl = URL::signedRoute('receipt.web', ['order' => $order->id]);
        $pdfUrl = URL::signedRoute('receipt.pdf', ['order' => $order->id]);

        return new ReceiptData(
            shop: $shopData,
            customer: $customerData,
            items: $itemsData,
            subtotal: (float) $order->subtotal,
            discount: (float) $order->discount_amount,
            tax: (float) $order->tax_amount,
            grandTotal: (float) $order->grand_total,
            paymentMethod: $order->payment_method,
            paymentStatus: $order->payment_status,
            orderStatus: $order->order_status,
            orderNumber: 'OQR-' . $order->created_at->format('Ymd') . '-' . str_pad($order->id, 4, '0', STR_PAD_LEFT),
            date: $order->created_at->format('d M Y, H:i'),
            webUrl: $webUrl,
            pdfUrl: $pdfUrl
        );
    }
}
