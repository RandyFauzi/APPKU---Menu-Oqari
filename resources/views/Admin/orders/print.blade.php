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
<h3 class="font-bold">{{ $order->shop->name ?? 'Menu Oqari' }}</h3>
<p>{{ $order->created_at->format('d/m/Y H:i') }}</p>
</div>

<div class="divider"></div>
<div class="mb-1">Order: #{{ $order->id }}</div>
<div class="mb-1">Customer: {{ $order->customer_name }}</div>
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
