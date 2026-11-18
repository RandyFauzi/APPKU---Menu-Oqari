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
            'format' => ['nullable', 'in:csv,xls,xlsx'], 'page' => ['sometimes', 'integer', 'min:1'],
            'cash_out_page' => ['sometimes', 'integer', 'min:1'], 'type' => ['nullable', 'in:orders,cash_out'],
        ]);

        $format = $filters['format'] ?? null;
        if (in_array($format, ['csv', 'xls', 'xlsx'], true)) {
            $exportType = $filters['type'] ?? 'orders';
            $isCashOut = $exportType === 'cash_out';

            $headers = $isCashOut
                ? ['ID', 'Waktu', 'Keperluan / Keterangan', 'Dicatat Oleh', 'Sesi Shift', 'Nominal Kas Keluar']
                : ['ID', 'Waktu', 'Pelanggan', 'Pembayaran', 'Penjualan Bersih', 'HPP Tersimpan', 'Laba Kotor', 'Total Tagihan'];

            $dataRows = $isCashOut
                ? $report->cashOutRows($request->user(), $filters)
                : $report->rows($request->user(), $filters);

            if ($format === 'csv') {
                $filename = $isCashOut ? 'laporan-kas-keluar.csv' : 'laporan-keuangan.csv';

                return response()->streamDownload(function () use ($headers, $dataRows): void {
                    $stream = fopen('php://output', 'w');
                    fwrite($stream, "\xEF\xBB\xBF");
                    fputcsv($stream, $headers, ',', '"', '');
                    foreach ($dataRows as $row) {
                        fputcsv($stream, $row, ',', '"', '');
                    }
                    fclose($stream);
                }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
            }

            $filename = $isCashOut ? 'laporan-kas-keluar.xls' : 'laporan-keuangan.xls';
            $sheetTitle = $isCashOut ? 'Laporan Kas Keluar' : 'Laporan Keuangan Penjualan';

            return response()->streamDownload(function () use ($sheetTitle, $headers, $dataRows): void {
                echo '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40">';
                echo '<head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">';
                echo '<!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>'.htmlspecialchars($sheetTitle).'</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]-->';
                echo '<style>table { border-collapse: collapse; font-family: Calibri, Arial, sans-serif; font-size: 11pt; } th { background-color: #f1f3f5; font-weight: bold; border: 0.5pt solid #d1d5db; padding: 6px 10px; text-align: left; } td { border: 0.5pt solid #e5e7eb; padding: 5px 10px; } .num { mso-number-format: "\#\,\#\#0"; text-align: right; }</style>';
                echo '</head><body>';
                echo '<h2>'.htmlspecialchars($sheetTitle).'</h2>';
                echo '<table border="1">';
                echo '<thead><tr>';
                foreach ($headers as $h) {
                    echo '<th>'.htmlspecialchars($h).'</th>';
                }
                echo '</tr></thead><tbody>';
                foreach ($dataRows as $row) {
                    echo '<tr>';
                    foreach ($row as $idx => $cell) {
                        $isNumeric = is_numeric($cell) && ! in_array($headers[$idx] ?? '', ['ID', 'Waktu'], true);
                        $class = $isNumeric ? ' class="num"' : '';
                        echo '<td'.$class.'>'.htmlspecialchars((string) $cell).'</td>';
                    }
                    echo '</tr>';
                }
                echo '</tbody></table></body></html>';
            }, $filename, [
                'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
                'Content-Disposition' => 'attachment; filename="'.$filename.'"',
            ]);
        }

        return $renderPage->execute($request->user(), 'report', $report->execute($request->user(), $filters));
    }
}
