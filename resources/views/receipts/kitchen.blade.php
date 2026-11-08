<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Tiket Pesanan Dapur — {{ $receiptData->orderNumber }}</title>
@php
    $width = $paperWidth ?? ($shop->receipt_settings['paper_width'] ?? '58mm');
    $is80mm = ($width === '80mm');
    $kitchenSettings = $shop->receipt_settings ?? [];
    $ticketTitle = $kitchenSettings['kitchen_ticket_title'] ?? 'TIKET PESANAN DAPUR';
    $showPrices = !empty($kitchenSettings['kitchen_show_prices']);
    $largeFont = ($kitchenSettings['kitchen_font_size'] ?? 'normal') === 'large';
    $highlightNotes = $kitchenSettings['kitchen_highlight_notes'] ?? true;
    $showCashier = $kitchenSettings['kitchen_show_cashier'] ?? true;
@endphp
<style>
@page { 
    margin: 0; 
    size: {{ $is80mm ? '80mm' : '58mm' }} auto; 
}
* {
    box-sizing: border-box;
}
body { 
    background-color: #f3f4f6; 
    margin: 0;
    padding: 20px;
    display: flex;
    flex-direction: column;
    align-items: center;
    font-family: 'Courier New', Courier, monospace;
}
.action-bar {
    width: {{ $is80mm ? '80mm' : '58mm' }};
    max-width: 100%;
    margin-bottom: 12px;
    display: flex;
    gap: 8px;
}
.action-btn {
    flex: 1;
    padding: 8px 12px;
    border-radius: 6px;
    font-size: 12px;
    font-weight: bold;
    cursor: pointer;
    border: none;
    text-align: center;
    text-decoration: none;
}
.btn-print {
    background-color: #df5826;
    color: #fff;
}
.btn-close {
    background-color: #e5e7eb;
    color: #374151;
}
.ticket {
    background-color: #fff;
    width: {{ $is80mm ? '80mm' : '58mm' }};
    max-width: 100%;
    padding: 4mm;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    font-size: {{ $largeFont ? '13px' : '11.5px' }}; 
    color: #000; 
    line-height: 1.25; 
}
h1, h2, h3, h4, p { margin: 0; padding: 0; }
.text-center { text-align: center; }
.text-right { text-align: right; }
.font-bold { font-weight: bold; }
.divider { border-top: 1px dashed #000; margin: 6px 0; }
.double-divider { border-top: 2px dashed #000; margin: 8px 0; }
.flex-between { display: flex; justify-content: space-between; }
.mb-1 { margin-bottom: 3px; }
.mb-2 { margin-bottom: 6px; }
.mt-1 { margin-top: 3px; }
.mt-2 { margin-top: 6px; }

.target-badge {
    border: 2px solid #000;
    padding: 4px;
    text-align: center;
    font-size: {{ $is80mm ? '18px' : '15px' }};
    font-weight: 900;
    letter-spacing: 1px;
    margin: 4px 0;
    text-transform: uppercase;
}

.items-table {
    width: 100%;
    border-collapse: collapse;
}
.items-table td {
    padding: 3px 0;
    vertical-align: top;
}
.item-qty {
    width: 18%;
    font-weight: 900;
    font-size: {{ $largeFont ? '15px' : '13px' }};
}
.item-details {
    width: {{ $showPrices ? '57%' : '82%' }};
}
.item-name {
    font-weight: bold;
    font-size: {{ $largeFont ? '13.5px' : '12px' }};
}
.item-addon {
    font-size: 10.5px;
    color: #222;
}
.item-notes {
    font-size: 11px;
    font-weight: bold;
    border: 1px dashed #000;
    padding: 2px 4px;
    margin-top: 2px;
    display: inline-block;
    background-color: #fafafa;
}
.item-price {
    width: 25%;
    text-align: right;
    font-size: 11px;
}

@media print { 
    @page { 
        margin: 0; 
        size: {{ $is80mm ? '80mm' : '58mm' }} auto; 
    }
    body { 
        background-color: #fff; 
        padding: 0; 
        display: block; 
    }
    .action-bar { display: none !important; }
    .ticket {
        width: 100%;
        max-width: {{ $is80mm ? '80mm' : '58mm' }};
        padding: 2mm 3mm;
        box-shadow: none;
        margin: 0;
    }
}
</style>
<x-shop-favicon :shop="$shop ?? null" />
</head>
<body onload="window.print(); window.onafterprint = function(){ window.close(); }">

<div class="action-bar">
    <button type="button" onclick="window.print()" class="action-btn btn-print">Cetak Ulang</button>
    <button type="button" onclick="window.close()" class="action-btn btn-close">Tutup</button>
</div>

<div class="ticket">
    <div class="text-center mb-1">
        <h3 class="font-bold" style="font-size: 13px; letter-spacing: 0.5px;">{{ strtoupper($ticketTitle) }}</h3>
        <p style="font-size: 9.5px;">{{ $receiptData->shop['name'] }}</p>
    </div>

    <div class="divider"></div>

    {{-- Big Table or Order Mode Banner --}}
    <div class="target-badge">
        @if(strtoupper($receiptData->fulfillmentType ?? '') === 'TAKEAWAY')
            TAKEAWAY / BUNGKUS
        @elseif(strtoupper($receiptData->fulfillmentType ?? '') === 'DELIVERY')
            DELIVERY
        @else
            {{ $receiptData->tableName ? 'MEJA '.strtoupper($receiptData->tableName) : 'DINE IN' }}
        @endif
    </div>

    <div class="flex-between mb-1 text-[10.5px]">
        <span>No: {{ $receiptData->orderNumber }}</span>
        <span>{{ $receiptData->date }}</span>
    </div>

    <div class="flex-between mb-1 text-[10.5px]">
        <span>Plg: {{ $receiptData->customer['name'] ?? 'Walk-in' }}</span>
        @if($showCashier && !empty($receiptData->cashierName))
            <span>Kasir: {{ $receiptData->cashierName }}</span>
        @endif
    </div>

    <div class="double-divider"></div>

    {{-- Kitchen Prep Items Table with Dine In / Takeaway Separation --}}
    @php
        $hasDineIn = collect($receiptData->items)->contains(fn($i) => strtoupper($i['serving_type'] ?? '') !== 'TAKEAWAY');
        $hasTakeaway = collect($receiptData->items)->contains(fn($i) => strtoupper($i['serving_type'] ?? '') === 'TAKEAWAY');
        $isMixed = $hasDineIn && $hasTakeaway;
    @endphp

    @if($isMixed)
        @if($hasDineIn)
            <div style="font-weight: 900; font-size: 11px; padding: 3px 0; border-bottom: 2px solid #000; margin-top: 4px;">
                === 🍽️ DINE IN (MAKAN DI TEMPAT) ===
            </div>
            <table class="items-table mb-1">
                @foreach(collect($receiptData->items)->filter(fn($i) => strtoupper($i['serving_type'] ?? '') !== 'TAKEAWAY') as $item)
                    <tr>
                        <td class="item-qty">[{{ $item['quantity'] }}x]</td>
                        <td class="item-details">
                            <div class="item-name">{{ $item['name'] }}</div>
                            @if(!empty($item['variant']))
                                <div class="item-addon">Varian: {{ $item['variant'] }}</div>
                            @endif
                            @if(!empty($item['modifiers']) && is_array($item['modifiers']))
                                <div class="item-addon">Opsi: {{ collect($item['modifiers'])->pluck('name')->join(', ') }}</div>
                            @endif
                            @if(!empty($item['notes']))
                                <div class="{{ $highlightNotes ? 'item-notes' : 'item-addon' }}">
                                    * Catatan: {{ $item['notes'] }}
                                </div>
                            @endif
                        </td>
                        @if($showPrices)
                            <td class="item-price">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif

        @if($hasTakeaway)
            <div style="font-weight: 900; font-size: 11px; padding: 3px 0; border-top: 2px dashed #000; border-bottom: 2px solid #000; margin-top: 6px;">
                === 🛍️ TAKEAWAY (BUNGKUS) ===
            </div>
            <table class="items-table mb-1">
                @foreach(collect($receiptData->items)->filter(fn($i) => strtoupper($i['serving_type'] ?? '') === 'TAKEAWAY') as $item)
                    <tr>
                        <td class="item-qty">[{{ $item['quantity'] }}x]</td>
                        <td class="item-details">
                            <div class="item-name">{{ $item['name'] }}</div>
                            @if(!empty($item['variant']))
                                <div class="item-addon">Varian: {{ $item['variant'] }}</div>
                            @endif
                            @if(!empty($item['modifiers']) && is_array($item['modifiers']))
                                <div class="item-addon">Opsi: {{ collect($item['modifiers'])->pluck('name')->join(', ') }}</div>
                            @endif
                            @if(!empty($item['notes']))
                                <div class="{{ $highlightNotes ? 'item-notes' : 'item-addon' }}">
                                    * Catatan: {{ $item['notes'] }}
                                </div>
                            @endif
                        </td>
                        @if($showPrices)
                            <td class="item-price">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                        @endif
                    </tr>
                @endforeach
            </table>
        @endif
    @else
        @if($hasTakeaway)
            <div style="font-weight: 900; font-size: 11px; padding: 2px 0; border-bottom: 2px solid #000; margin-bottom: 4px;">
                === 🛍️ SEMUA TAKEAWAY (BUNGKUS) ===
            </div>
        @endif
        <table class="items-table">
            @forelse($receiptData->items as $item)
                <tr>
                    <td class="item-qty">[{{ $item['quantity'] }}x]</td>
                    <td class="item-details">
                        <div class="item-name">{{ $item['name'] }}</div>
                        @if(!empty($item['variant']))
                            <div class="item-addon">Varian: {{ $item['variant'] }}</div>
                        @endif
                        @if(!empty($item['modifiers']) && is_array($item['modifiers']))
                            <div class="item-addon">Opsi: {{ collect($item['modifiers'])->pluck('name')->join(', ') }}</div>
                        @endif
                        @if(!empty($item['notes']))
                            <div class="{{ $highlightNotes ? 'item-notes' : 'item-addon' }}">
                                * Catatan: {{ $item['notes'] }}
                            </div>
                        @endif
                    </td>
                    @if($showPrices)
                        <td class="item-price">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center" style="padding: 10px 0;">(Tidak ada item untuk stasiun ini)</td>
                </tr>
            @endforelse
        </table>
    @endif

    @if($showPrices)
        <div class="divider"></div>
        <div class="flex-between font-bold mt-1">
            <span>Total Tagihan:</span>
            <span>Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="double-divider"></div>

    <div class="text-center" style="font-size: 9px; margin-top: 4px;">
        <p>*** CETAK PESANAN SELESAI ***</p>
        <p>{{ now()->format('d/m/Y H:i:s') }}</p>
    </div>
</div>

</body>
</html>
