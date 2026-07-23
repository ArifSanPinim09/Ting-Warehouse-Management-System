<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Models\Item;
use App\Models\Box;
use App\Models\DendaClaim;
use App\Services\NotificationService;
use Illuminate\Console\Command;

/**
 * Daily cron job for:
 * 1. Late payment denda: Invoice > 5 days → Rp5.000/hari, > 7 days → hold
 * 2. Notuan/Lelang: Items no_tuan > 15 days (12+3) since UP_INVOICE → lelang
 *
 * Flow Website:
 * - Tagihan berlaku 5 hari → lewat itu denda Rp5.000/hari
 * - Setelah 1 minggu barang ditahan gudang
 * - Jika tidak dihubungi dalam 24×3 jam (72 jam), masuk status Lelang
 * - Barang Notuan > 12 hari sejak UP INVOICE + 3 hari tanpa respon (>15 hari total) → Lelang
 */
class ProcessDendaAndLelang extends Command
{
    protected $signature = 'ting:process-denda-lelang';
    protected $description = 'Process late payment denda (Rp5.000/day after 5 days) and notuan lelang threshold (15 days)';

    private const DENDA_PER_DAY = 5000;
    private const DENDA_GRACE_DAYS = 5;
    private const HOLD_AFTER_DAYS = 7;
    private const NOTUAN_LELANG_DAYS = 15;

    public function handle(NotificationService $notifService): int
    {
        $dendaCount = $this->processDenda($notifService);
        $lelangCount = $this->processLelang($notifService);

        $this->info("Processed: {$dendaCount} denda updates, {$lelangCount} lelang items");

        return self::SUCCESS;
    }

    /**
     * Process late payment denda for overdue invoices.
     * - Invoice issued > 5 days ago and still unpaid → denda Rp5.000/day
     * - Invoice issued > 7 days ago → hold items
     */
    private function processDenda(NotificationService $notifService): int
    {
        $count = 0;

        // Get overdue invoices (waiting payment, past deadline)
        $overdueInvoices = Invoice::where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->whereNotNull('payment_deadline')
            ->where('payment_deadline', '<', now())
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $daysOverdue = now()->diffInDays($invoice->payment_deadline);
            $dendaAmount = max(0, $daysOverdue - self::DENDA_GRACE_DAYS) * self::DENDA_PER_DAY;

            // Update or create denda claim
            $denda = DendaClaim::firstOrNew([
                'invoice_id' => $invoice->id,
            ]);
            $denda->customer_id = $invoice->customer_id;

            $denda->jumlah_denda = $dendaAmount;
            $denda->status = $daysOverdue > self::DENDA_GRACE_DAYS
                ? DendaClaim::STATUS_TAGGED
                : DendaClaim::STATUS_PENDING;
            $denda->save();

            // Update invoice grand_total with denda
            $invoice->denda_total = $dendaAmount;
            $invoice->grand_total = $invoice->fee_tax + $invoice->fee_wh + $invoice->fee_packing + $invoice->add_on + $dendaAmount;
            $invoice->save();

            $count++;

            // Hold items after 7 days
            if ($daysOverdue >= self::HOLD_AFTER_DAYS) {
                Item::where('box_id', $invoice->box_id)->update([
                    'status' => Item::STATUS_HOLD,
                ]);
                $notifService->itemHold($invoice);
            }
        }

        return $count;
    }

    /**
     * Process Notuan items for lelang eligibility.
     * - Items with status no_tuan, > 15 days since invoice issued → lelang
     */
    private function processLelang(NotificationService $notifService): int
    {
        $count = 0;

        // Get notuan items whose box invoice was issued > 15 days ago
        $notuanItems = Item::where('status', Item::STATUS_NO_TUAN)
            ->whereHas('box', function ($q) {
                $q->where('status', Box::STATUS_INVOICE)
                    ->orWhere('status', Box::STATUS_DONE);
            })
            ->get();

        foreach ($notuanItems as $item) {
            // Get the invoice for this box
            $invoice = Invoice::where('box_id', $item->box_id)->first();
            if (!$invoice || !$invoice->created_at) {
                continue;
            }

            $daysSinceInvoice = now()->diffInDays($invoice->created_at);

            // > 15 days total (12 days + 3 days no response)
            if ($daysSinceInvoice >= self::NOTUAN_LELANG_DAYS) {
                $item->status = Item::STATUS_LELANG;
                $item->save();
                $count++;
            }
        }

        return $count;
    }
}
