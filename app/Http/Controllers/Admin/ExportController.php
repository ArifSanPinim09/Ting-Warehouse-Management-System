<?php

namespace App\Http\Controllers\Admin;

use App\Models\Checkout;
use App\Http\Controllers\Controller;
use Barryvdh\DomPDF\Facade\Pdf;

/**
 * Sprint 5B: Export Controller — Thermal Label, Faktur, Import per Box.
 */
class ExportController extends Controller
{
    /**
     * 5B-6: Thermal label 100×150mm auto download.
     * Flow Website P422: "Auto download alamat ke settingan thermal 100mm x 150 mm"
     */
    public function thermalLabel(Checkout $checkout)
    {
        $checkout->load(['invoice.box', 'customer']);

        $pdf = Pdf::loadView('exports.thermal-label', compact('checkout'))
            ->setPaper([0, 0, 283.46, 425.20]); // 100mm × 150mm in points (1mm = 2.8346pt)

        return $pdf->download("label-{$checkout->id}.pdf");
    }

    /**
     * 5B-7: Download faktur per invoice.
     * Flow Website P420: "Nomor invoice jika di klik bisa lihat List data barang yang mau dikirim
     *                     (bisa di download seukuran kertas faktur)"
     */
    public function invoiceFaktur(Checkout $checkout)
    {
        $checkout->load(['invoice.box.items.customer', 'customer']);

        $pdf = Pdf::loadView('exports.invoice-faktur', compact('checkout'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("faktur-{$checkout->invoice->invoice_number}.pdf");
    }

    /**
     * 5B-8: Import per box (PDF per box A/B/C untuk pihak Becuk).
     * Flow Website P500-504: "data terpisah sesuai box number yang ud di isi oleh ADMIN CINA.
     *                         Perlu foto nya jg, import pdf atau excel"
     */
    public function importPerBox(int $boxId)
    {
        $box = \App\Models\Box::with(['items.customer', 'items.whChinaData'])->findOrFail($boxId);

        $pdf = Pdf::loadView('exports.import-per-box', compact('box'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("import-box-{$box->box_code}.pdf");
    }
}
