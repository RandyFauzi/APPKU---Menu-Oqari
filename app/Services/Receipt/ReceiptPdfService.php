<?php

namespace App\Services\Receipt;

use Barryvdh\DomPDF\Facade\Pdf;

class ReceiptPdfService
{
    public function download(ReceiptData $receiptData)
    {
        $pdf = Pdf::loadView('receipts.pdf', compact('receiptData'))
                  ->setPaper('A4', 'portrait');
                  
        $fileName = 'Receipt_' . $receiptData->orderNumber . '.pdf';
        
        return $pdf->download($fileName);
    }
}
