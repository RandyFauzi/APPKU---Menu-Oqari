<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt {{ $receiptData->orderNumber }}</title>
    @vite(['resources/css/app.css'])
    <style>
        :root {
            --brand-color: {{ $receiptData->shop['primary_color'] }};
        }
    </style>
</head>
<body class="bg-gray-100 font-sans antialiased pb-20">

    <div class="max-w-md mx-auto mt-6 sm:mt-10 bg-white shadow-xl overflow-hidden sm:rounded-lg">
        
        <!-- Header -->
        <div class="text-center py-8" style="background-color: var(--brand-color); color: #fff;">
            @if($receiptData->shop['logo_url'])
                <img src="{{ $receiptData->shop['logo_url'] }}" alt="{{ $receiptData->shop['name'] }}" class="mx-auto h-16 w-16 rounded-full border-2 border-white object-cover mb-3 bg-white">
            @endif
            <h1 class="text-2xl font-bold uppercase tracking-wider">{{ $receiptData->shop['name'] }}</h1>
            @if($receiptData->shop['slogan'])
                <p class="text-sm opacity-90 mt-1">{{ $receiptData->shop['slogan'] }}</p>
            @endif
        </div>

        <div class="p-6 sm:p-8">
            <!-- Status & Meta -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-gray-500 text-xs font-semibold uppercase">Status Pembayaran</h2>
                    <p class="text-lg font-bold {{ $receiptData->paymentStatus === 'PAID' ? 'text-green-600' : 'text-yellow-600' }}">{{ $receiptData->paymentStatus }}</p>
                </div>
                <div class="text-right">
                    <h2 class="text-gray-500 text-xs font-semibold uppercase">Order No</h2>
                    <p class="font-semibold text-gray-800">{{ $receiptData->orderNumber }}</p>
                </div>
            </div>

            <!-- Customer & Date -->
            <div class="mb-6 bg-gray-50 p-4 rounded-md text-sm text-gray-700">
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Tanggal</span>
                    <span class="font-medium">{{ $receiptData->date }}</span>
                </div>
                <div class="flex justify-between mb-2">
                    <span class="text-gray-500">Pelanggan</span>
                    <span class="font-medium">{{ $receiptData->customer['name'] }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500">Metode Bayar</span>
                    <span class="font-medium uppercase">{{ $receiptData->paymentMethod ?? '-' }}</span>
                </div>
            </div>

            <!-- Items -->
            <div class="border-t border-gray-200 py-4 mb-4">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-4">Rincian Pesanan</h3>
                
                @foreach($receiptData->items as $item)
                    <div class="flex justify-between items-start mb-4">
                        <div class="flex-1 pr-4">
                            <h4 class="font-medium text-gray-900">{{ $item['name'] }}</h4>
                            <p class="text-sm text-gray-500">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                            @if($item['notes'])
                                <p class="text-xs text-gray-400 mt-1">Catatan: {{ $item['notes'] }}</p>
                            @endif
                        </div>
                        <div class="font-medium text-gray-900">
                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Totals -->
            <div class="border-t border-gray-200 py-4 space-y-2 text-sm">
                <div class="flex justify-between text-gray-600">
                    <span>Subtotal</span>
                    <span>Rp {{ number_format($receiptData->subtotal, 0, ',', '.') }}</span>
                </div>
                
                @if($receiptData->discount > 0)
                <div class="flex justify-between text-red-500">
                    <span>Diskon</span>
                    <span>-Rp {{ number_format($receiptData->discount, 0, ',', '.') }}</span>
                </div>
                @endif
                
                @if($receiptData->tax > 0)
                <div class="flex justify-between text-gray-600">
                    <span>Pajak (PB1)</span>
                    <span>Rp {{ number_format($receiptData->tax, 0, ',', '.') }}</span>
                </div>
                @endif
                
                <div class="flex justify-between items-center text-lg font-bold text-gray-900 mt-4 pt-4 border-t border-gray-200">
                    <span>TOTAL</span>
                    <span>Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>

        </div>
        
        <!-- Footer Info -->
        <div class="bg-gray-50 p-6 text-center border-t border-gray-200">
            <p class="text-sm text-gray-600 mb-2 font-medium">Terima kasih telah berkunjung!</p>
            @if($receiptData->shop['address'])
                <p class="text-xs text-gray-500 mb-1">{{ $receiptData->shop['address'] }}</p>
            @endif
            @if($receiptData->shop['phone'] || $receiptData->shop['email'])
                <p class="text-xs text-gray-500">
                    {{ $receiptData->shop['phone'] }} @if($receiptData->shop['phone'] && $receiptData->shop['email']) • @endif {{ $receiptData->shop['email'] }}
                </p>
            @endif
        </div>
    </div>

    <!-- Actions -->
    <div class="max-w-md mx-auto mt-6 flex gap-4 px-4 sm:px-0">
        <a href="{{ $receiptData->pdfUrl }}" class="flex-1 text-center bg-white border border-gray-300 text-gray-700 py-3 rounded-lg font-medium shadow-sm hover:bg-gray-50 transition-colors">
            Download PDF
        </a>
        <button onclick="window.print()" class="flex-1 text-center py-3 rounded-lg font-medium shadow-sm hover:opacity-90 transition-opacity" style="background-color: var(--brand-color); color: #fff;">
            Cetak
        </button>
    </div>

</body>
</html>
