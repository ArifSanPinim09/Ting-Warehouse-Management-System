<?php

namespace App\Observers;

use App\Models\User;
use App\Services\AuditLogService;

/**
 * User Observer — logs activation/deactivation events.
 *
 * CLAUDE.md §3.3: Audit trail for User (aktivasi/nonaktivasi).
 */
class UserObserver
{
    public function __construct(
        private AuditLogService $auditLog,
    ) {}

    /**
     * Handle the User "updated" event.
     *
     * Logs when status changes (activate, deactivate, pending).
     */
    public function updated(User $user): void
    {
        if ($user->wasChanged('status')) {
            $this->auditLog->log(
                event: 'updated',
                subject: $user,
                old: ['status' => $user->getOriginal('status')],
                new: ['status' => $user->status],
            );
        }
    }
}
