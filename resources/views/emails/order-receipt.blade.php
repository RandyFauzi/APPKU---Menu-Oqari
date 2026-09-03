<!DOCTYPE html>
<html>
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <style>
        @media only screen and (max-width: 620px) {
            table.body h1 {
                font-size: 24px !important;
                margin-bottom: 10px !important;
            }
            table.body .wrapper,
            table.body .article {
                padding: 10px !important;
            }
            table.body .content {
                padding: 0 !important;
            }
            table.body .container {
                padding: 0 !important;
                width: 100% !important;
            }
            table.body .main {
                border-left-width: 0 !important;
                border-radius: 0 !important;
                border-right-width: 0 !important;
            }
            table.body .btn table {
                width: 100% !important;
            }
            table.body .btn a {
                width: 100% !important;
            }
        }
    </style>
</head>
<body style="background-color: #f6f6f6; font-family: sans-serif; -webkit-font-smoothing: antialiased; font-size: 14px; line-height: 1.4; margin: 0; padding: 0; -ms-text-size-adjust: 100%; -webkit-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" class="body" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background-color: #f6f6f6;">
        <tr>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
            <td class="container" style="font-family: sans-serif; font-size: 14px; vertical-align: top; display: block; Margin: 0 auto; max-width: 580px; padding: 10px; width: 580px;">
                <div class="content" style="box-sizing: border-box; display: block; Margin: 0 auto; max-width: 580px; padding: 10px;">
                    <!-- START CENTERED WHITE CONTAINER -->
                    <table class="main" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; background: #ffffff; border-radius: 3px; overflow: hidden;">
                        
                        <!-- Header -->
                        <tr>
                            <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; background-color: {{ $receiptData->shop['primary_color'] }}; color: #ffffff; text-align: center; padding: 20px;">
                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                    <tr>
                                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; text-align: center;">
                                            @if($receiptData->shop['logo_url'])
                                                <img src="{{ $receiptData->shop['logo_url'] }}" alt="{{ $receiptData->shop['name'] }}" style="border: none; -ms-interpolation-mode: bicubic; max-width: 100%; max-height: 60px; margin-bottom: 10px;">
                                            @endif
                                            <h1 style="color: #ffffff; font-family: sans-serif; line-height: 1.4; margin: 0; font-size: 20px; text-transform: uppercase;">{{ $receiptData->shop['name'] }}</h1>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>

                        <!-- Details -->
                        <tr>
                            <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px;">
                                <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%;">
                                    <tr>
                                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Halo <strong>{{ $receiptData->customer['name'] }}</strong>,</p>
                                            <p style="font-family: sans-serif; font-size: 14px; font-weight: normal; margin: 0; Margin-bottom: 15px;">Berikut adalah struk tanda terima untuk pesanan Anda.</p>
                                            
                                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 13px;">
                                                <tr>
                                                    <td style="padding: 5px 0; color: #666;">No. Pesanan:</td>
                                                    <td style="padding: 5px 0; text-align: right; font-weight: bold;">{{ $receiptData->orderNumber }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 5px 0; color: #666;">Tanggal:</td>
                                                    <td style="padding: 5px 0; text-align: right;">{{ $receiptData->date }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 5px 0; color: #666;">Status Pembayaran:</td>
                                                    <td style="padding: 5px 0; text-align: right; color: {{ $receiptData->paymentStatus === 'PAID' ? 'green' : '#ff9900' }}; font-weight: bold;">{{ $receiptData->paymentStatus }}</td>
                                                </tr>
                                                <tr>
                                                    <td style="padding: 5px 0; color: #666; border-bottom: 1px solid #eee;">Metode:</td>
                                                    <td style="padding: 5px 0; text-align: right; border-bottom: 1px solid #eee;">{{ $receiptData->paymentMethod ?? '-' }}</td>
                                                </tr>
                                            </table>

                                            <!-- Items -->
                                            <table style="width: 100%; border-collapse: collapse; margin-bottom: 20px;">
                                                <thead>
                                                    <tr>
                                                        <th style="text-align: left; padding: 10px 0; border-bottom: 2px solid #eee; font-size: 13px; color: #666;">Item</th>
                                                        <th style="text-align: right; padding: 10px 0; border-bottom: 2px solid #eee; font-size: 13px; color: #666;">Total</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach($receiptData->items as $item)
                                                    <tr>
                                                        <td style="padding: 10px 0; border-bottom: 1px solid #eee; font-size: 14px;">
                                                            <strong>{{ $item['name'] }}</strong><br>
                                                            <span style="color: #666; font-size: 12px;">{{ $item['quantity'] }} x Rp {{ number_format($item['price'], 0, ',', '.') }}</span>
                                                        </td>
                                                        <td style="text-align: right; padding: 10px 0; border-bottom: 1px solid #eee; font-size: 14px;">
                                                            Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                                                        </td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                            <!-- Totals -->
                                            <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                                                <tr>
                                                    <td style="padding: 5px 0; color: #666;">Subtotal</td>
                                                    <td style="padding: 5px 0; text-align: right;">Rp {{ number_format($receiptData->subtotal, 0, ',', '.') }}</td>
                                                </tr>
                                                @if($receiptData->discount > 0)
                                                <tr>
                                                    <td style="padding: 5px 0; color: #666;">Diskon</td>
                                                    <td style="padding: 5px 0; text-align: right; color: red;">-Rp {{ number_format($receiptData->discount, 0, ',', '.') }}</td>
                                                </tr>
                                                @endif
                                                @if($receiptData->tax > 0)
                                                <tr>
                                                    <td style="padding: 5px 0; color: #666;">Pajak (PB1)</td>
                                                    <td style="padding: 5px 0; text-align: right;">Rp {{ number_format($receiptData->tax, 0, ',', '.') }}</td>
                                                </tr>
                                                @endif
                                                <tr>
                                                    <td style="padding: 10px 0; border-top: 2px solid #333; font-weight: bold; font-size: 16px;">TOTAL</td>
                                                    <td style="padding: 10px 0; border-top: 2px solid #333; text-align: right; font-weight: bold; font-size: 16px;">Rp {{ number_format($receiptData->grandTotal, 0, ',', '.') }}</td>
                                                </tr>
                                            </table>

                                            <!-- Call to action (Web Receipt URL) -->
                                            @if($receiptData->webUrl)
                                            <table border="0" cellpadding="0" cellspacing="0" class="btn btn-primary" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: 100%; box-sizing: border-box; margin-top: 20px;">
                                                <tbody>
                                                    <tr>
                                                        <td align="center" style="font-family: sans-serif; font-size: 14px; vertical-align: top; padding-bottom: 15px;">
                                                            <table border="0" cellpadding="0" cellspacing="0" style="border-collapse: separate; mso-table-lspace: 0pt; mso-table-rspace: 0pt; width: auto;">
                                                                <tbody>
                                                                    <tr>
                                                                        <td style="font-family: sans-serif; font-size: 14px; vertical-align: top; background-color: {{ $receiptData->shop['primary_color'] }}; border-radius: 5px; text-align: center;">
                                                                            <a href="{{ $receiptData->webUrl }}" target="_blank" style="display: inline-block; color: #ffffff; background-color: {{ $receiptData->shop['primary_color'] }}; border: solid 1px {{ $receiptData->shop['primary_color'] }}; border-radius: 5px; box-sizing: border-box; cursor: pointer; text-decoration: none; font-size: 14px; font-weight: bold; margin: 0; padding: 12px 25px; text-transform: capitalize;">View Receipt in Browser</a>
                                                                        </td>
                                                                    </tr>
                                                                </tbody>
                                                            </table>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                            @endif
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        
                        <!-- Shop Info Footer -->
                        <tr>
                            <td class="wrapper" style="font-family: sans-serif; font-size: 14px; vertical-align: top; box-sizing: border-box; padding: 20px; background: #f9f9f9; text-align: center; border-top: 1px solid #eee;">
                                <p style="margin: 0; font-size: 12px; color: #999;">Terima kasih atas pesanan Anda.</p>
                                @if($receiptData->shop['address'])
                                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #999;">{{ $receiptData->shop['address'] }}</p>
                                @endif
                                @if($receiptData->shop['phone'] || $receiptData->shop['email'])
                                    <p style="margin: 5px 0 0 0; font-size: 12px; color: #999;">{{ $receiptData->shop['phone'] }} | {{ $receiptData->shop['email'] }}</p>
                                @endif
                            </td>
                        </tr>
                        
                    </table>
                </div>
            </td>
            <td style="font-family: sans-serif; font-size: 14px; vertical-align: top;">&nbsp;</td>
        </tr>
    </table>
</body>
</html>
