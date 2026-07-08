<?php

namespace App\Observers;

use App\Models\Box;
use App\Services\AuditLogService;

/**
 * Box Observer — logs status change events.
 *
 * CLAUDE.md §3.3: Audit trail for Box (status change).
 */
class BoxObserver
{
    public function __construct(
        private AuditLogService $auditLog,
    ) {}

    /**
     * Handle the Box "updated" event.
     *
     * Logs when status changes (OPEN → SENT_TO_CARGO → OTW_INA → UP_INVOICE → DONE).
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
        }
    }
}
