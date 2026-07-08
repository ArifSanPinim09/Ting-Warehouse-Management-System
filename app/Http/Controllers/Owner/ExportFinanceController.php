<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Http\Request;

class ExportFinanceController extends Controller
{
    /**
     * Export finance report as CSV or Excel.
     *
     * PRD §8.16: Export Excel/CSV
     */
    public function __invoke(Request $request, AuditLogService $auditService)
    {
        $type = $request->input('type', 'csv');

        $query = Invoice::with(['customer:id,name,email', 'box:id,tracking_number,batch_name,type,method']);

        // Apply same filters as FinanceIndex
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                  ->orWhereHas('customer', function ($cq) use ($search) {
                      $cq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($from = $request->input('date_from')) {
            $query->whereDate('created_at', '>=', $from);
        }

        if ($to = $request->input('date_to')) {
            $query->whereDate('created_at', '<=', $to);
        }

        if ($month = $request->input('month')) {
            $query->whereMonth('created_at', $month);
        }

        if ($year = $request->input('year')) {
            $query->whereYear('created_at', $year);
        }

        if ($customer = $request->input('customer')) {
            $query->where('customer_id', $customer);
        }

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $invoices = $query->latest()->get();

        // Log the export
        $firstInvoice = Invoice::first();
        if ($firstInvoice) {
            $auditService->logCustom(
                $firstInvoice,
                'export_finance',
                "Export laporan keuangan {$type} — {$invoices->count()} baris"
            );
        }

        $filename = 'laporan-keuangan-' . now()->format('Y-m-d');

        if ($type === 'csv') {
            return $this->exportCsv($invoices, $filename);
        }

        return $this->exportExcel($invoices, $filename);
    }

    private function exportCsv($invoices, string $filename)
    {
        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.csv\"",
        ];

        $callback = function () use ($invoices) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF)); // UTF-8 BOM for Excel
            fputcsv($handle, [
                'No Invoice', 'Customer', 'Box', 'Berat (kg)', 'Volume (m³)',
                'Fee TAX', 'Fee WH', 'Fee Packing', 'Add On', 'Grand Total',
                'Status', 'Tanggal',
            ]);

            foreach ($invoices as $inv) {
                fputcsv($handle, [
                    $inv->invoice_number,
                    $inv->customer->name ?? '-',
                    $inv->box->tracking_number ?? $inv->box->batch_name ?? '-',
                    $inv->weight,
                    $inv->volume,
                    number_format($inv->fee_tax, 2, '.', ''),
                    number_format($inv->fee_wh, 2, '.', ''),
                    number_format($inv->fee_packing, 2, '.', ''),
                    number_format($inv->add_on, 2, '.', ''),
                    number_format($inv->grand_total, 2, '.', ''),
                    $inv->status,
                    $inv->created_at->format('d M Y H:i'),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }

    private function exportExcel($invoices, string $filename)
    {
        $headers = [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}.xls\"",
        ];

        $callback = function () use ($invoices) {
            echo '<html><head><meta charset="utf-8"></head><body>';
            echo '<table border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse;font-family:Inter,sans-serif;font-size:12px;">';
            echo '<tr style="background:#1a1a2e;color:#fff;font-weight:bold;">';
            echo '<td>No Invoice</td><td>Customer</td><td>Box</td><td>Berat (kg)</td>';
            echo '<td>Volume (m³)</td><td>Fee TAX</td><td>Fee WH</td><td>Fee Packing</td>';
            echo '<td>Add On</td><td>Grand Total</td><td>Status</td><td>Tanggal</td></tr>';

            $rowBg = false;
            foreach ($invoices as $inv) {
                $bg = $rowBg ? 'background:#f8fafc;' : '';
                echo '<tr style="' . $bg . '">';
                echo '<td>' . e($inv->invoice_number) . '</td>';
                echo '<td>' . e($inv->customer->name ?? '-') . '</td>';
                echo '<td>' . e($inv->box->tracking_number ?? $inv->box->batch_name ?? '-') . '</td>';
                echo '<td style="text-align:right;">' . $inv->weight . '</td>';
                echo '<td style="text-align:right;">' . $inv->volume . '</td>';
                echo '<td style="text-align:right;">' . number_format($inv->fee_tax, 0, ',', '.') . '</td>';
                echo '<td style="text-align:right;">' . number_format($inv->fee_wh, 0, ',', '.') . '</td>';
                echo '<td style="text-align:right;">' . number_format($inv->fee_packing, 0, ',', '.') . '</td>';
                echo '<td style="text-align:right;">' . number_format($inv->add_on, 0, ',', '.') . '</td>';
                echo '<td style="text-align:right;font-weight:bold;">' . number_format($inv->grand_total, 0, ',', '.') . '</td>';
                echo '<td>' . e($inv->status) . '</td>';
                echo '<td>' . $inv->created_at->format('d M Y H:i') . '</td>';
                echo '</tr>';
                $rowBg = !$rowBg;
            }

            echo '</table></body></html>';
        };

        return response()->stream($callback, 200, $headers);
    }
}
