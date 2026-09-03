<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk Pesanan {{ $receiptData->orderNumber }}</title>
<style>
/* CSS for thermal printers 58mm/80mm */
@page { margin: 0; size: 58mm auto; }
body { 
    font-family: 'Courier New', Courier, monospace; 
    width: 56mm; /* slightly smaller than 58mm to ensure it fits */
    padding: 2mm; 
    font-size: 11px; 
    margin: 0; 
    color: #000; 
    line-height: 1.2; 
    background-color: #fff;
}
h1, h2, h3, h4, p { margin: 0; padding: 0; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.font-bold { font-weight: bold; }
.divider { border-top: 1px dashed #000; margin: 4px 0; }
.flex-between { display: flex; justify-content: space-between; }
.mb-1 { margin-bottom: 4px; }
.mb-2 { margin-bottom: 8px; }
.mt-2 { margin-top: 8px; }
.items { width: 100%; border-collapse: collapse; }
.items td { padding: 2px 0; vertical-align: top; }
.items .qty { width: 15%; }
.items .name { width: 50%; }
.items .price { width: 35%; text-align: right; }
@media print { 
    body { width: 56mm; } 
}
</style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">

<div class="text-center mb-2">
@if($receiptData->shop['logo_url'])
    <img src="{{ $receiptData->shop['logo_url'] }}" style="max-width: 40px; margin-bottom: 4px; border-radius: 4px; filter: grayscale(100%);">
@endif
<h3 class="font-bold" style="text-transform: uppercase;">{{ $receiptData->shop['name'] }}</h3>
@if($receiptData->shop['slogan'])
    <p style="font-size: 9px; margin-bottom: 2px;">{{ $receiptData->shop['slogan'] }}</p>
@endif
@if($receiptData->shop['address'])
    <p style="font-size: 9px; margin-bottom: 2px;">{{ $receiptData->shop['address'] }}</p>
@endif
</div>

<div class="divider"></div>
<div class="text-center mb-2 mt-2">
    <p>Order: {{ $receiptData->orderNumber }}</p>
    <p>{{ $receiptData->date }}</p>
</div>
<div class="divider"></div>

<div class="mb-1 mt-2">Customer: {{ $receiptData->customer['name'] }}</div>
<div class="mb-2">Pembayaran: {{ $receiptData->paymentMethod ?? '-' }} ({{ $receiptData->paymentStatus }})</div>

<div class="divider"></div>

<table class="items mb-2 mt-2">
@foreach($receiptData->items as $item)
<tr>
<td class="qty">{{ $item['quantity'] }}x</td>
<td class="name">{{ $item['name'] }}<br><small>{{ $item['notes'] }}</small></td>
<td class="price">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
</tr>
@endforeach
</table>

<div class="divider"></div>

<div class="flex-between mb-1 mt-2">
<span>Subtotal</span>
<span>{{ number_format($receiptData->subtotal, 0, ',', '.') }}</span>
</div>

@if($receiptData->discount > 0)
<div class="flex-between mb-1">
<span>Diskon</span>
<span>-{{ number_format($receiptData->discount, 0, ',', '.') }}</span>
</div>
@endif

@if($receiptData->tax > 0)
<div class="flex-between mb-1">
<span>Pajak</span>
<span>{{ number_format($receiptData->tax, 0, ',', '.') }}</span>
</div>
@endif

<div class="divider"></div>

<div class="flex-between font-bold mb-2 mt-2">
<span>TOTAL</span>
<span>Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}</span>
</div>

<div class="divider"></div>

<div class="text-center mt-2" style="font-size: 9px;">
    <p>Terima Kasih!</p>
    @if($receiptData->shop['phone'])
        <p>{{ $receiptData->shop['phone'] }}</p>
    @endif
</div>

</body>
</html>
