<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @media only screen and (max-width: 620px) {
            table.body .container { width: 100% !important; padding: 0 !important; }
            table.body .main { border-radius: 0 !important; border-left: none !important; border-right: none !important; }
            .mobile-padding { padding-left: 20px !important; padding-right: 20px !important; }
        }
        /* Dark mode overrides (works on some clients) */
        @media (prefers-color-scheme: dark) {
            .main { background: #ffffff !important; }
        }
    </style>
</head>
<body style="background-color: #f7f9fc; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; margin: 0; padding: 40px 0; -webkit-font-smoothing: antialiased;">
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table role="presentation" width="560" cellpadding="0" cellspacing="0" class="main"
                       style="background: #ffffff; border-radius: 16px; overflow: hidden; border: 1px solid #eaeaea; box-shadow: 0 10px 25px rgba(0,0,0,0.03);">

                    {{-- HEADER / LOGO --}}
                    <tr>
                        <td class="mobile-padding" style="padding: 40px 40px 20px; text-align: center;">
                            @if($receiptData->logoPath)
                                <img src="{{ $message->embed($receiptData->logoPath) }}"
                                     alt="{{ $receiptData->shop['name'] }}"
                                     style="height: 50px; max-width: 150px; margin-bottom: 16px; object-fit: contain;">
                            @elseif($receiptData->shop['logo_url'])
                                <img src="{{ $receiptData->shop['logo_url'] }}"
                                     alt="{{ $receiptData->shop['name'] }}"
                                     style="height: 50px; max-width: 150px; margin-bottom: 16px; object-fit: contain;">
                            @else
                                <div style="height: 20px;"></div>
                            @endif

                            <h1 style="color: #111827; margin: 0; font-size: 22px; font-weight: 700; letter-spacing: -0.5px;">
                                {{ $receiptData->shop['name'] }}
                            </h1>
                            
                            @if($receiptData->shop['receipt_header'])
                                <p style="color: #6b7280; margin: 8px 0 0; font-size: 14px; line-height: 1.5;">
                                    {{ $receiptData->shop['receipt_header'] }}
                                </p>
                            @else
                                <p style="color: #6b7280; margin: 8px 0 0; font-size: 14px; line-height: 1.5;">
                                    Halo <strong>{{ $receiptData->customer['name'] }}</strong>, pesanan Anda berhasil.
                                </p>
                            @endif
                        </td>
                    </tr>

                    {{-- BIG TOTAL (Square POS Style) --}}
                    <tr>
                        <td style="padding: 10px 40px 30px; text-align: center;" class="mobile-padding">
                            <div style="font-size: 36px; font-weight: 800; color: #111827; margin-bottom: 15px; letter-spacing: -1px;">
                                Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}
                            </div>

                            <span style="display: inline-block; padding: 6px 14px; border-radius: 20px; font-size: 12px; font-weight: 700; background-color: {{ $receiptData->paymentStatus === 'PAID' ? '#dcfce7' : '#fef3c7' }}; color: {{ $receiptData->paymentStatus === 'PAID' ? '#166534' : '#92400e' }}; text-transform: uppercase; letter-spacing: 0.5px;">
                                {{ $receiptData->paymentStatus === 'PAID' ? '✓ LUNAS' : '⌚ BELUM BAYAR' }}
                            </span>
                        </td>
                    </tr>

                    {{-- ORDER DETAILS INFO --}}
                    <tr>
                        <td style="padding: 0 40px 20px;" class="mobile-padding">
                            <div style="background-color: #f8fafc; border-radius: 12px; padding: 20px; border: 1px solid #f1f5f9;">
                                <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 13px;">
                                    <tr>
                                        <td style="color: #64748b; padding-bottom: 8px; width: 40%;">Nomor Pesanan</td>
                                        <td style="color: #0f172a; padding-bottom: 8px; font-weight: 600; text-align: right;">{{ $receiptData->orderNumber }}</td>
                                    </tr>
                                    <tr>
                                        <td style="color: #64748b; padding-bottom: 8px;">Tanggal</td>
                                        <td style="color: #0f172a; padding-bottom: 8px; font-weight: 500; text-align: right;">{{ $receiptData->date }}</td>
                                    </tr>
                                    @if($receiptData->paymentMethod)
                                    <tr>
                                        <td style="color: #64748b;">Metode Pembayaran</td>
                                        <td style="color: #0f172a; font-weight: 500; text-align: right;">{{ $receiptData->paymentMethod }}</td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </td>
                    </tr>

                    {{-- DIVIDER --}}
                    <tr>
                        <td style="padding: 0 40px;" class="mobile-padding">
                            <div style="border-top: 2px dashed #e2e8f0;"></div>
                        </td>
                    </tr>

                    {{-- ORDER ITEMS --}}
                    <tr>
                        <td style="padding: 25px 40px 10px;" class="mobile-padding">
                            <h3 style="margin: 0 0 15px; font-size: 12px; font-weight: 700; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px;">
                                Rincian Pesanan
                            </h3>

                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; color: #1e293b;">
                                @foreach($receiptData->items as $item)
                                <tr>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9;">
                                        <div style="font-weight: 600; color: #0f172a;">{{ $item['name'] }}</div>
                                        <div style="color: #64748b; font-size: 13px; margin-top: 4px;">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</div>
                                        @if($item['notes'])
                                            <div style="color: #94a3b8; font-size: 12px; margin-top: 2px; font-style: italic;">Catatan: {{ $item['notes'] }}</div>
                                        @endif
                                    </td>
                                    <td style="padding: 10px 0; border-bottom: 1px solid #f1f5f9; text-align: right; font-weight: 600; color: #0f172a; vertical-align: top;">
                                        Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            </table>

                            {{-- SUBTOTALS --}}
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size: 14px; margin-top: 15px;">
                                <tr>
                                    <td style="padding: 6px 0; color: #64748b;">Subtotal</td>
                                    <td style="padding: 6px 0; text-align: right; color: #0f172a; font-weight: 500;">Rp {{ number_format($receiptData->subtotal, 0, ',', '.') }}</td>
                                </tr>
                                @if($receiptData->discount > 0)
                                <tr>
                                    <td style="padding: 6px 0; color: #64748b;">Diskon</td>
                                    <td style="padding: 6px 0; text-align: right; color: #ef4444; font-weight: 500;">-Rp {{ number_format($receiptData->discount, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                                @if($receiptData->tax > 0)
                                <tr>
                                    <td style="padding: 6px 0; color: #64748b;">Pajak (PB1)</td>
                                    <td style="padding: 6px 0; text-align: right; color: #0f172a; font-weight: 500;">Rp {{ number_format($receiptData->tax, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            </table>
                        </td>
                    </tr>

                    {{-- CTA BUTTON --}}
                    @if($receiptData->webUrl)
                    <tr>
                        <td style="padding: 30px 40px 40px; text-align: center;" class="mobile-padding">
                            <a href="{{ $receiptData->webUrl }}" target="_blank"
                               style="display: block; width: 100%; box-sizing: border-box; background-color: {{ $receiptData->shop['primary_color'] }}; color: #ffffff; text-decoration: none; font-weight: 600; font-size: 15px; padding: 16px 24px; border-radius: 8px;">
                                Download / Lihat Struk Asli
                            </a>
                        </td>
                    </tr>
                    @endif

                    {{-- FOOTER --}}
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px 40px; text-align: center; border-top: 1px solid #f1f5f9;" class="mobile-padding">
                            @if($receiptData->shop['receipt_footer'])
                                <p style="margin: 0 0 15px; font-size: 13px; color: #64748b; line-height: 1.6; white-space: pre-wrap;">{{ $receiptData->shop['receipt_footer'] }}</p>
                            @else
                                <p style="margin: 0 0 15px; font-size: 13px; color: #64748b; line-height: 1.6;">
                                    Terima kasih telah berbelanja di {{ $receiptData->shop['name'] }}.
                                </p>
                            @endif

                            @if($receiptData->shop['phone'] || $receiptData->shop['email'])
                                <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8;">
                                    Butuh bantuan? Hubungi kami:<br>
                                    <strong style="color: #475569;">{{ $receiptData->shop['email'] }} @if($receiptData->shop['email'] && $receiptData->shop['phone']) &bull; @endif {{ $receiptData->shop['phone'] }}</strong>
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>
                
                {{-- BOTTOM POWERED BY --}}
                <table role="presentation" width="560" cellpadding="0" cellspacing="0">
                    <tr>
                        <td style="padding: 24px 0; text-align: center;">
                            <p style="margin: 0; font-size: 12px; color: #94a3b8;">
                                Powered by <strong>Menu Oqari</strong>
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
