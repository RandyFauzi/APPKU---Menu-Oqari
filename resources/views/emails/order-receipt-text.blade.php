{{ $receiptData->shop['name'] }}
{{ $receiptData->shop['slogan'] ?? '' }}
----------------------------------------
{{ $receiptData->shop['receipt_header'] ?? '' }}

Halo {{ $receiptData->customer['name'] }}, berikut struk pesanan Anda.

No. Pesanan: {{ $receiptData->orderNumber }}
Tanggal: {{ $receiptData->date }}
Status: {{ $receiptData->paymentStatus === 'PAID' ? 'LUNAS' : 'MENUNGGU PEMBAYARAN' }}

ITEMS:
@foreach($receiptData->items as $item)
- {{ $item['name'] }}
  {{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }} = Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
@if($item['notes'])
  Catatan: {{ $item['notes'] }}
@endif
@endforeach

----------------------------------------
Subtotal: Rp {{ number_format($receiptData->subtotal, 0, ',', '.') }}
@if($receiptData->discount > 0)
Diskon: -Rp {{ number_format($receiptData->discount, 0, ',', '.') }}
@endif
@if($receiptData->tax > 0)
Pajak: Rp {{ number_format($receiptData->tax, 0, ',', '.') }}
@endif
TOTAL: Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}
----------------------------------------

@if($receiptData->webUrl)
Lihat Struk Online: {{ $receiptData->webUrl }}
@endif

{{ $receiptData->shop['receipt_footer'] ?? 'Terima kasih atas pesanan Anda.' }}
{{ $receiptData->shop['address'] ?? '' }}
{{ $receiptData->shop['phone'] }} | {{ $receiptData->shop['email'] }}
