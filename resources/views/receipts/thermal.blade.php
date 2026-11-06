<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Struk Pembayaran — {{ $receiptData->orderNumber }}</title>
@php
    $width = $paperWidth ?? ($shop->receipt_settings['paper_width'] ?? '58mm');
    $is80mm = ($width === '80mm');
    $receiptSettings = $shop->receipt_settings ?? [];
    $showLogo = $receiptSettings['show_logo'] ?? true;
    $showAddress = $receiptSettings['show_address'] ?? true;
    $showPhone = $receiptSettings['show_phone'] ?? true;
    $showSocial = $receiptSettings['show_social'] ?? true;
    $showWifi = !empty($receiptSettings['show_wifi']);
    $wifiSsid = $receiptSettings['wifi_ssid'] ?? '';
    $wifiPassword = $receiptSettings['wifi_password'] ?? '';
    $showQr = !empty($receiptSettings['show_qr']);
    $qrType = $receiptSettings['qr_type'] ?? 'menu';
    $customHeader = $receiptSettings['header_text'] ?? ($receiptData->shop['receipt_header'] ?? ($shop->receipt_header ?? null));
    $customFooter = $receiptSettings['footer_text'] ?? ($receiptData->shop['receipt_footer'] ?? ($shop->receipt_footer ?? null));
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
.receipt {
    background-color: #fff;
    width: {{ $is80mm ? '80mm' : '58mm' }};
    max-width: 100%;
    padding: 4mm;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    font-size: {{ $is80mm ? '13px' : '11.5px' }}; 
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

.items { width: 100%; border-collapse: collapse; }
.items td { padding: 3px 0; vertical-align: top; }
.items .qty { width: 14%; font-weight: bold; }
.items .name { width: 56%; }
.items .price { width: 30%; text-align: right; }

.wifi-box {
    border: 1px dashed #000;
    padding: 4px 6px;
    margin: 6px 0;
    text-align: center;
    font-size: 10.5px;
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
    .receipt {
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

<div class="receipt">
    {{-- Shop Header --}}
    <div class="text-center mb-1">
        @if($showLogo && $receiptData->shop['logo_url'])
            <img src="{{ $receiptData->shop['logo_url'] }}" style="max-width: 44px; max-height: 44px; margin-bottom: 4px; border-radius: 4px; filter: grayscale(100%);">
        @endif
        <h3 class="font-bold" style="font-size: 14px; text-transform: uppercase;">{{ $receiptData->shop['name'] }}</h3>
        @if(!empty($receiptData->shop['slogan']))
            <p style="font-size: 9.5px; margin-top: 1px;">{{ $receiptData->shop['slogan'] }}</p>
        @endif
        @if($showAddress && !empty($receiptData->shop['address']))
            <p style="font-size: 9.5px; margin-top: 2px;">{{ $receiptData->shop['address'] }}</p>
        @endif
        @if($showPhone && !empty($receiptData->shop['phone']))
            <p style="font-size: 9.5px;">Telp/WA: {{ $receiptData->shop['phone'] }}</p>
        @endif
        @if(!empty($customHeader))
            <div style="font-size: 9.5px; margin-top: 3px; white-space: pre-line;">{{ $customHeader }}</div>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Order Metadata --}}
    <div class="flex-between text-[11px] mb-1">
        <span>No: {{ $receiptData->orderNumber }}</span>
        <span>{{ $receiptData->date }}</span>
    </div>

    <div class="flex-between text-[11px] mb-1">
        <span>Plg: {{ $receiptData->customer['name'] ?? 'Walk-in Customer' }}</span>
        @if(!empty($receiptData->cashierName))
            <span>Kasir: {{ $receiptData->cashierName }}</span>
        @endif
    </div>

    <div class="flex-between text-[11px] mb-1">
        <span>Tipe: {{ strtoupper($receiptData->fulfillmentType ?? '') === 'TAKEAWAY' ? 'Takeaway (Bungkus)' : (strtoupper($receiptData->fulfillmentType ?? '') === 'DELIVERY' ? 'Delivery' : 'Dine-in') }}</span>
        @if($receiptData->tableName && strtoupper($receiptData->fulfillmentType ?? '') !== 'TAKEAWAY')
            <span class="font-bold">Meja: {{ $receiptData->tableName }}</span>
        @endif
    </div>

    <div class="divider"></div>

    {{-- Items Table --}}
    <table class="items mb-1 mt-1">
        @foreach($receiptData->items as $item)
            <tr>
                <td class="qty">{{ $item['quantity'] }}x</td>
                <td class="name">
                    <span class="font-bold">{{ $item['name'] }}</span>
                    @if(!empty($item['variant']))
                        <br><small>({{ $item['variant'] }})</small>
                    @endif
                    @if(!empty($item['modifiers']) && is_array($item['modifiers']))
                        <br><small>+ {{ collect($item['modifiers'])->pluck('name')->join(', ') }}</small>
                    @endif
                    @if(!empty($item['notes']))
                        <br><small style="font-style: italic;">* {{ $item['notes'] }}</small>
                    @endif
                </td>
                <td class="price">{{ number_format($item['subtotal'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
    </table>

    <div class="divider"></div>

    {{-- Financial Totals --}}
    <div class="flex-between mb-1">
        <span>Subtotal</span>
        <span>{{ number_format($receiptData->subtotal, 0, ',', '.') }}</span>
    </div>

    @if($receiptData->discount > 0)
        <div class="flex-between mb-1">
            <span>Diskon</span>
            <span>-{{ number_format($receiptData->discount, 0, ',', '.') }}</span>
        </div>
    @endif

    @if($receiptData->serviceCharge > 0)
        <div class="flex-between mb-1">
            <span>Biaya Layanan</span>
            <span>{{ number_format($receiptData->serviceCharge, 0, ',', '.') }}</span>
        </div>
    @endif

    @if($receiptData->tax > 0)
        <div class="flex-between mb-1">
            <span>Pajak Resto</span>
            <span>{{ number_format($receiptData->tax, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="double-divider"></div>

    <div class="flex-between font-bold mb-1" style="font-size: {{ $is80mm ? '15px' : '13px' }};">
        <span>TOTAL</span>
        <span>Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}</span>
    </div>

    <div class="divider"></div>

    {{-- Payment Info --}}
    <div class="flex-between mb-1 text-[11px]">
        <span>Metode Bayar:</span>
        <span class="font-bold uppercase">{{ $receiptData->paymentMethod ?? 'TUNAI' }}</span>
    </div>

    @if($receiptData->amountPaid > 0)
        <div class="flex-between mb-1 text-[11px]">
            <span>Bayar:</span>
            <span>Rp {{ number_format($receiptData->amountPaid, 0, ',', '.') }}</span>
        </div>
    @endif

    @if($receiptData->change > 0)
        <div class="flex-between mb-1 text-[11px] font-bold">
            <span>Kembali:</span>
            <span>Rp {{ number_format($receiptData->change, 0, ',', '.') }}</span>
        </div>
    @endif

    <div class="divider"></div>

    {{-- WiFi Box if enabled --}}
    @if($showWifi && (!empty($wifiSsid) || !empty($wifiPassword)))
        <div class="wifi-box">
            <strong>WIFI CAFE</strong><br>
            @if(!empty($wifiSsid)) SSID: {{ $wifiSsid }} @endif
            @if(!empty($wifiPassword)) | Password: {{ $wifiPassword }} @endif
        </div>
    @endif

    {{-- Custom Footer & Thank You Message --}}
    <div class="text-center mt-2" style="font-size: 9.5px;">
        @if(!empty($customFooter))
            <p style="white-space: pre-line; margin-bottom: 3px;">{{ $customFooter }}</p>
        @else
            <p>Terima Kasih Atas Kunjungan Anda!</p>
            <p>Silakan Datang Kembali</p>
        @endif

        @if($showSocial && !empty($shop->instagram_link))
            <p style="margin-top: 3px;">IG: {{ $shop->instagram_link }}</p>
        @endif
        
        <p style="margin-top: 4px; font-size: 8.5px; opacity: 0.85;">Powered by OQARI POS</p>
    </div>
</div>

</body>
</html>
