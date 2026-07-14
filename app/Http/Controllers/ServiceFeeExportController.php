<?php

namespace App\Http\Controllers;

use App\Models\WhChinaData;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ServiceFeeExportController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $filterHurufBox = $request->input('huruf_box', '');

        $query = WhChinaData::query()
            ->when($filterHurufBox, function ($q) use ($filterHurufBox) {
                $q->where('huruf_box', 'like', "%{$filterHurufBox}%");
            })
            ->orderBy('huruf_box')
            ->orderBy('resi_number');

        $records = $query->get();

        $filename = 'service_fee_' . ($filterHurufBox ?: 'all') . '_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($records) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM for Excel UTF-8
            fputcsv($handle, ['Resi Number', 'Huruf Box', 'Berat (kg)', 'P (cm)', 'L (cm)', 'T (cm)', 'Volume', 'Biaya Jasa (Rp)', 'Biaya Tax (Rp)', 'Tanggal Input']);

            foreach ($records as $r) {
                fputcsv($handle, [
                    $r->resi_number,
                    $r->huruf_box ?? '-',
                    $r->berat ?? '-',
                    $r->panjang ?? '-',
                    $r->lebar ?? '-',
                    $r->tinggi ?? '-',
                    $r->volume ?? '-',
                    $r->biaya_jasa ?? '-',
                    $r->biaya_tax ?? '-',
                    $r->created_at->format('d M Y H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
