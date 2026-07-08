<?php

namespace App\Observers;

use App\Models\Invoice;
use App\Services\AuditLogService;

/**
 * Invoice Observer — logs invoice lifecycle events.
 *
 * CLAUDE.md §3.3: Audit trail for Invoice (generate, verify, reject).
 *
 * - "generate" is logged on `created` event
 * - "verify" / "reject" are logged on `updated` when status changes
 */
class InvoiceObserver
{
    public function __construct(
        private AuditLogService $auditLog,
    ) {}

    /**
     * Handle the Invoice "created" event.
     *
     * Logs invoice generation with fee snapshot.
     */
    public function created(Invoice $invoice): void
    {
        $this->auditLog->logCustom(
            subject: $invoice,
            event: 'generated',
            description: "Invoice {$invoice->invoice_number} generated for customer #{$invoice->customer_id}",
            old: [],
            new: [
                'invoice_number' => $invoice->invoice_number,
                'customer_id'    => $invoice->customer_id,
                'box_id'         => $invoice->box_id,
                'weight'         => $invoice->weight,
                'volume'         => $invoice->volume,
                'fee_tax'        => $invoice->fee_tax,
                'fee_wh'         => $invoice->fee_wh,
                'fee_packing'    => $invoice->fee_packing,
                'add_on'         => $invoice->add_on,
                'grand_total'    => $invoice->grand_total,
                'status'         => $invoice->status,
            ],
        );
    }

    /**
     * Handle the Invoice "updated" event.
     *
     * Logs status changes (e.g. waiting_payment → waiting_verification → verified,
     * or waiting_verification → waiting_payment on rejection).
     */
    public function updated(Invoice $invoice): void
    {
        if ($invoice->wasChanged('status')) {
            $oldStatus = $invoice->getOriginal('status');
            $newStatus = $invoice->status;

            // Determine event label based on status transition
            $event = 'updated';

            if ($newStatus === Invoice::STATUS_VERIFIED) {
                $event = 'verified';
            } elseif ($oldStatus === Invoice::STATUS_WAITING_VERIFICATION
                      && $newStatus === Invoice::STATUS_WAITING_PAYMENT) {
                // Rejected: goes back to waiting_payment
                $event = 'rejected';
            }

            $this->auditLog->log(
                event: $event,
                subject: $invoice,
                old: ['status' => $oldStatus],
                new: ['status' => $newStatus],
            );
        }
    }
}
