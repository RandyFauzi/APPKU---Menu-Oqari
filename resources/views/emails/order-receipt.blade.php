<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @media only screen and (max-width: 620px) {
            table.body .container { width: 100% !important; padding: 0 !important; }
            table.body .main { border-radius: 0 !important; }
        }
    </style>
</head>
<body style="background-color: #f4f4f5; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 40px 0; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="600" cellpadding="0" cellspacing="0" class="main"
                       style="background: #ffffff; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.05);">

                    {{-- HERO SECTION --}}
                    <tr>
                        <td style="background-color: #FFF8E7; padding: 40px 30px; text-align: center; border-bottom: 1px solid #f0e6d3;">
                            {{-- Logo & Shop Name --}}
                            <div style="margin-bottom: 24px;">
                                @if($receiptData->logoPath)
                                    <img src="{{ $message->embed($receiptData->logoPath) }}"
                                         alt="{{ $receiptData->shop['name'] }}"
                                         style="height: 40px; vertical-align: middle; margin-right: 10px; display: inline-block;">
                                @elseif($receiptData->shop['logo_url'])
                                    <img src="{{ $receiptData->shop['logo_url'] }}"
                                         alt="{{ $receiptData->shop['name'] }}"
                                         style="height: 40px; vertical-align: middle; margin-right: 10px; display: inline-block;">
                                @endif
                                <span style="font-size: 24px; font-weight: bold; color: #333; vertical-align: middle; display: inline-block;">
                                    {{ $receiptData->shop['name'] }}
                                </span>
                            </div>

                            {{-- Greeting --}}
                            <h1 style="color: #222; margin: 0 0 12px; font-size: 26px; font-weight: 800;">
                                Pesanan Anda Berhasil!
                            </h1>
                            
                            @if($receiptData->shop['receipt_header'])
                                <p style="color: #555; margin: 0; font-size: 14px; line-height: 1.5; max-width: 80%; margin: 0 auto;">
                                    {{ $receiptData->shop['receipt_header'] }}
                                </p>
                            @else
                                <p style="color: #555; margin: 0; font-size: 14px; line-height: 1.5; max-width: 80%; margin: 0 auto;">
                                    Halo <strong>{{ $receiptData->customer['name'] }}</strong>, pesanan Anda telah kami terima dan sedang diproses.
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- ORDER NUMBER & BUTTON --}}
                    <tr>
                        <td style="padding: 35px 30px 25px; text-align: center;">
                            <p style="margin: 0 0 5px; font-size: 16px; color: #333;">
                                Nomor Pesanan: <strong style="color: {{ $receiptData->shop['primary_color'] }}; font-size: 20px;">{{ $receiptData->orderNumber }}</strong>
                            </p>
                            <p style="margin: 0 0 25px; font-size: 13px; color: #888;">
                                Tanggal: {{ $receiptData->date }} &nbsp;|&nbsp; Status: <strong style="color: {{ $receiptData->paymentStatus === 'PAID' ? '#16a34a' : '#d97706' }};">{{ $receiptData->paymentStatus === 'PAID' ? 'LUNAS' : 'BELUM BAYAR' }}</strong>
                            </p>

                            @if($receiptData->webUrl)
                            <a href="{{ $receiptData->webUrl }}" target="_blank"
                               style="display: inline-block; background-color: {{ $receiptData->shop['primary_color'] }}; color: #ffffff; text-decoration: none; font-weight: bold; font-size: 14px; padding: 14px 32px; border-radius: 30px;">
                                Lihat Struk Browser
                            </a>
                            @endif
                        </td>
                    </tr>

                    {{-- ORDER SUMMARY --}}
                    <tr>
                        <td style="padding: 0 40px 30px;">
                            <h3 style="margin: 0 0 15px; font-size: 12px; color: #888; text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid #eee; padding-bottom: 10px;">
                                Ringkasan Pesanan
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #333;">
                                @foreach($receiptData->items as $item)
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f5f5f5;">
                                        <div style="font-weight: 600;">{{ $item['name'] }}</div>
                                        @if($item['notes'])
                                            <div style="color: #999; font-size: 12px; margin-top: 2px;">Catatan: {{ $item['notes'] }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f5f5f5; text-align: center; color: #666; width: 60px;">
                                        QTY {{ $item['quantity'] }}
                                    </td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #f5f5f5; text-align: right; font-weight: 500;">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            {{-- TOTALS --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; margin-top: 15px;">
                                <tr>
                                    <td style="padding: 6px 0; color: #666;">Subtotal</td>
                                    <td style="padding: 6px 0; text-align: right; color: #333;">Rp {{ number_format($receiptData->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @if($receiptData->discount > 0)
                                <tr>
                                    <td style="padding: 6px 0; color: #666;">Diskon</td>
                                    <td style="padding: 6px 0; text-align: right; color: #dc2626;">-Rp {{ number_format($receiptData->discount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($receiptData->tax > 0)
                                <tr>
                                    <td style="padding: 6px 0; color: #666;">Pajak (PB1)</td>
                                    <td style="padding: 6px 0; text-align: right; color: #333;">Rp {{ number_format($receiptData->tax, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                <tr>
                                    <td style="padding: 15px 0 0; font-weight: bold; font-size: 16px; color: #111;">Total</td>
                                    <td style="padding: 15px 0 0; text-align: right; font-weight: bold; font-size: 18px; color: #111;">
                                        Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    {{-- FOOTER / CONTACT --}}
                    <tr>
                        <td style="background-color: #F9F9F9; padding: 35px 40px; text-align: center; border-top: 1px solid #eee;">
                            <h3 style="margin: 0 0 10px; font-size: 18px; color: #333;">Ada Pertanyaan?</h3>
                            
                            @if($receiptData->shop['receipt_footer'])
                                <p style="margin: 0 0 15px; font-size: 13px; color: #666; line-height: 1.5; white-space: pre-wrap;">{{ $receiptData->shop['receipt_footer'] }}</p>
                            @else
                                <p style="margin: 0 0 15px; font-size: 13px; color: #666; line-height: 1.5;">
                                    Jika Anda butuh bantuan mengenai pesanan ini, jangan ragu untuk menghubungi kami.
                                </p>
                            @endif

                            @if($receiptData->shop['phone'] || $receiptData->shop['email'])
                                <p style="margin: 0; font-size: 13px; color: {{ $receiptData->shop['primary_color'] }}; font-weight: bold;">
                                    {{ $receiptData->shop['email'] }} @if($receiptData->shop['email'] && $receiptData->shop['phone']) &nbsp;|&nbsp; @endif {{ $receiptData->shop['phone'] }}
                                </p>
                            @endif
                        </td>
                    </tr>
                </table>
                
                {{-- COPYRIGHT --}}
                <table role="presentation" width="600" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding: 20px 0; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #999;">
                                Copyright &copy; {{ date('Y') }} {{ $receiptData->shop['name'] }}. All rights reserved.<br>
                                Powered by Menu Oqari
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
