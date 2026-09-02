<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk Pesanan #{{ $order->id }}</title>
<style>
@page { margin: 0; size: 58mm 100%; }
body { font-family: 'Courier New', Courier, monospace; width: 58mm; padding: 2mm; font-size: 11px; margin: 0; color: #000; line-height: 1.2; }
h1, h2, h3, h4, p { margin: 0; padding: 0; }
.text-center { text-align: center; }
.font-bold { font-weight: bold; }
.divider { border-top: 1px dashed #000; margin: 4px 0; }
.flex-between { display: flex; justify-content: space-between; }
.mb-1 { margin-bottom: 4px; }
.mb-2 { margin-bottom: 8px; }
.items { width: 100%; }
.items td { padding: 2px 0; vertical-align: top; }
.items .qty { width: 15%; }
.items .name { width: 50%; }
.items .price { width: 35%; text-align: right; }
@media print { body { width: 58mm; } }
</style>
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">

<div class="text-center mb-2">
@if($order->shop->logo_url)
    <img src="{{ $order->shop->logo_url }}" style="max-width: 40px; margin-bottom: 4px; border-radius: 4px;">
@endif
<h3 class="font-bold" style="text-transform: uppercase;">{{ $order->shop->name ?? 'Menu Oqari' }}</h3>
@if($order->shop->slogan)
    <p style="font-size: 9px; margin-bottom: 2px;">{{ $order->shop->slogan }}</p>
@endif
</div>

<div class="divider"></div>
<div class="text-center mb-2">
    <p>Order #{{ str_pad($order->id, 4, '0', STR_PAD_LEFT) }}</p>
    <p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
</div>

<div class="divider"></div>
<div class="mb-1">Customer: {{ $order->customer_name ?: 'Guest' }}</div>
<div class="mb-1">Meja: {{ $order->table ? $order->table->name : 'Takeaway' }}</div>
<div class="divider"></div>

<table class="items mb-2">
@foreach($order->items as $item)
<tr>
<td class="qty">{{ $item->quantity }}x</td>
<td class="name">{{ $item->product ? $item->product->name : 'Produk' }}<br><small>{{ $item->notes }}</small></td>
<td class="price">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}</td>
</tr>
@endforeach
</table>

<div class="divider"></div>
<div class="flex-between font-bold mb-2">
<span>TOTAL</span>
<span>Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
</div>

<div class="text-center">
<p>Terima Kasih!</p>
</div>

</body>
</html>
