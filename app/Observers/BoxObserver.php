<?php

namespace App\Observers;

use App\Models\Box;
use App\Models\Invoice;
use App\Services\AuditLogService;
use App\Services\NotificationService;

/**
 * Box Observer — logs status change events.
 *
 * CLAUDE.md §3.3: Audit trail for Box (status change).
 * Revisi §2.10.5: Set arrived_indonesia + storage_deadline on box arrival.
 * Revisi §2.11.2: Notify customer when box arrives at WH.
 */
class BoxObserver
{
    public function __construct(
        private AuditLogService $auditLog,
        private NotificationService $notifService,
    ) {}

    /**
     * Handle the Box "updated" event.
     *
     * Logs when status changes (OPEN → SENT_TO_CARGO → OTW_INA → UP_INVOICE → DONE).
     * When box arrives in Indonesia (OTW_INA/UP_INVOICE), marks items as arrived
     * and sets storage_deadline on related invoices.
     */
    public function updated(Box $box): void
    {
        if ($box->wasChanged('status')) {
            $this->auditLog->log(
                event: 'updated',
                subject: $box,
                old: ['status' => $box->getOriginal('status')],
                new: ['status' => $box->status],
            );

            // Revisi §2.10.5: When box arrives in Indonesia, mark items as arrived
            if (in_array($box->status, [Box::STATUS_OTW_INA, Box::STATUS_UP_INVOICE])) {
                $this->markItemsArrived($box);
                $this->setStorageDeadline($box);

                // Revisi §2.11.2: Notify customer barang sampai WH
                if ($box->customer_id && $box->status === Box::STATUS_UP_INVOICE) {
                    $this->notifService->itemArrivedWH($box);
                }
            }
        }
    }

    /**
     * Mark all items in the box as arrived in Indonesia.
     */
    private function markItemsArrived(Box $box): void
    {
        $box->items()
            ->where('arrived_indonesia', false)
            ->update(['arrived_indonesia' => true]);
    }

    /**
     * Set storage_deadline on invoices related to this box.
     *
     * For box-based invoices: matches by box_id.
     * For flexible invoices: matches by items belonging to this box.
     */
    private function setStorageDeadline(Box $box): void
    {
        $deadline = now()->addDays(30);

        // Box-based invoices
        Invoice::where('box_id', $box->id)
            ->whereNull('storage_deadline')
            ->update(['storage_deadline' => $deadline]);

        // Flexible invoices (items from this box)
        Invoice::whereNull('box_id')
            ->whereNull('storage_deadline')
            ->whereHas('items', fn ($q) => $q->where('items.box_id', $box->id))
            ->update(['storage_deadline' => $deadline]);
    }
}
