<?php

namespace App\Http\Controllers\Admin;

use App\Actions\BuildFinancialReport;
use App\Actions\RenderAdminPage;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request, BuildFinancialReport $report, RenderAdminPage $renderPage): View|StreamedResponse
    {
        $this->authorize('view-reports');
        abort_unless($request->user()->shop_id, 403);
        $filters = $request->validate([
            'date' => ['nullable', 'date_format:Y-m-d'], 'period' => ['nullable', 'in:daily,weekly,monthly,all'],
            'format' => ['nullable', 'in:csv'], 'page' => ['sometimes', 'integer', 'min:1'],
        ]);
        if (($filters['format'] ?? null) === 'csv') {
            return response()->streamDownload(function () use ($request, $report, $filters): void {
                $stream = fopen('php://output', 'w');
                fwrite($stream, "\xEF\xBB\xBF");
                fputcsv($stream, ['ID', 'Waktu', 'Pelanggan', 'Pembayaran', 'Penjualan Bersih', 'HPP Tersimpan', 'Laba Kotor', 'Total Tagihan'], ',', '"', '');
                foreach ($report->rows($request->user(), $filters) as $row) {
                    fputcsv($stream, $row, ',', '"', '');
                }
                fclose($stream);
            }, 'laporan-keuangan.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
        }

        return $renderPage->execute($request->user(), 'report', $report->execute($request->user(), $filters));
    }
}
