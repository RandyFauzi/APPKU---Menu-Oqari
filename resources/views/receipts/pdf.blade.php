<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Receipt {{ $receiptData->orderNumber }}</title>
<style>
    body {
        font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
        color: #333;
        font-size: 14px;
        line-height: 1.5;
        margin: 0;
        padding: 20px;
    }
    .header {
        text-align: center;
        border-bottom: 2px solid {{ $receiptData->shop['primary_color'] }};
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .header h1 {
        margin: 0;
        font-size: 24px;
        color: {{ $receiptData->shop['primary_color'] }};
        text-transform: uppercase;
    }
    .header p {
        margin: 5px 0 0;
        color: #666;
    }
    .details-table {
        width: 100%;
        margin-bottom: 30px;
    }
    .details-table td {
        vertical-align: top;
    }
    .items-table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 30px;
    }
    .items-table th {
        background-color: #f8f9fa;
        text-align: left;
        padding: 10px;
        border-bottom: 1px solid #ddd;
    }
    .items-table td {
        padding: 10px;
        border-bottom: 1px solid #eee;
    }
    .totals-table {
        width: 50%;
        float: right;
        border-collapse: collapse;
    }
    .totals-table td {
        padding: 8px 0;
    }
    .totals-table .amount {
        text-align: right;
    }
    .totals-table .grand-total {
        font-size: 18px;
        font-weight: bold;
        border-top: 2px solid #333;
        padding-top: 10px;
    }
    .footer {
        clear: both;
        margin-top: 50px;
        text-align: center;
        font-size: 12px;
        color: #888;
        border-top: 1px solid #eee;
        padding-top: 20px;
    }
</style>
</head>
<body>

<div class="header">
    <h1>{{ $receiptData->shop['name'] }}</h1>
    @if($receiptData->shop['slogan'])
        <p>{{ $receiptData->shop['slogan'] }}</p>
    @endif
    @if($receiptData->shop['address'])
        <p>{{ $receiptData->shop['address'] }}</p>
    @endif
</div>

<table class="details-table">
    <tr>
        <td style="width: 50%;">
            <strong>TANGGAL:</strong><br>
            {{ $receiptData->date }}<br><br>
            <strong>ORDER NO:</strong><br>
            {{ $receiptData->orderNumber }}
        </td>
        <td style="width: 50%; text-align: right;">
            <strong>PELANGGAN:</strong><br>
            {{ $receiptData->customer['name'] }}<br>
            {{ $receiptData->customer['email'] ?? '-' }}<br><br>
            <strong>PEMBAYARAN:</strong><br>
            {{ $receiptData->paymentMethod ?? '-' }} ({{ $receiptData->paymentStatus }})
        </td>
    </tr>
</table>

<table class="items-table">
    <thead>
        <tr>
            <th>Item</th>
            <th style="text-align: center;">Qty</th>
            <th style="text-align: right;">Harga</th>
            <th style="text-align: right;">Subtotal</th>
        </tr>
    </thead>
    <tbody>
        @foreach($receiptData->items as $item)
        <tr>
            <td>
                {{ $item['name'] }}
                @if($item['notes'])
                <br><small style="color: #666;">Note: {{ $item['notes'] }}</small>
                @endif
            </td>
            <td style="text-align: center;">{{ $item['quantity'] }}</td>
            <td style="text-align: right;">Rp {{ number_format($item['price'], 0, ',', '.') }}</td>
            <td style="text-align: right;">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

<table class="totals-table">
    <tr>
        <td>Subtotal</td>
        <td class="amount">Rp {{ number_format($receiptData->subtotal, 0, ',', '.') }}</td>
    </tr>
    @if($receiptData->discount > 0)
    <tr>
        <td>Diskon</td>
        <td class="amount" style="color: red;">-Rp {{ number_format($receiptData->discount, 0, ',', '.') }}</td>
    </tr>
    @endif
    @if($receiptData->tax > 0)
    <tr>
        <td>Pajak (PB1)</td>
        <td class="amount">Rp {{ number_format($receiptData->tax, 0, ',', '.') }}</td>
    </tr>
    @endif
    <tr>
        <td class="grand-total">TOTAL</td>
        <td class="grand-total amount">Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}</td>
    </tr>
</table>

<div class="footer">
    <p>Terima kasih atas pesanan Anda.</p>
    @if($receiptData->shop['phone'] || $receiptData->shop['email'])
        <p>{{ $receiptData->shop['phone'] }} | {{ $receiptData->shop['email'] }}</p>
    @endif
</div>

</body>
</html>
