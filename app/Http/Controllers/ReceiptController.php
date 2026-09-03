<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\Receipt\ReceiptService;
use Illuminate\Http\Request;

class ReceiptController extends Controller
{
    public function show(Request $request, Order $order)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired receipt link.');
        }

        $receiptData = app(ReceiptService::class)->build($order);

        return view('receipts.web', compact('receiptData'));
    }

    public function downloadPdf(Request $request, Order $order)
    {
        if (!$request->hasValidSignature()) {
            abort(403, 'Invalid or expired receipt link.');
        }

        $receiptData = app(ReceiptService::class)->build($order);
        
        return app(\App\Services\Receipt\ReceiptPdfService::class)->download($receiptData);
    }
}
