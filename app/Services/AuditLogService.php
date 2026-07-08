<?php

namespace App\Services;

use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

/**
 * Audit Log Service — records changes to critical models.
 *
 * CLAUDE.md §3.3: "Audit trail — log setiap perubahan data"
 * PRD §1.2 poin 7: "Tidak ada audit trail → Log setiap perubahan data"
 *
 * Used by Eloquent Observers to record who changed what, when.
 */
class AuditLogService
{
    /**
     * Log a model event (created, updated, deleted).
     *
     * For "updated" events, compares old/new attributes and stores only
     * the fields that actually changed (old_values vs new_values).
     *
     * @param  string   $event    Event name: 'created', 'updated', 'deleted'
     * @param  Model    $subject  The model that was changed
     * @param  array    $old      Old attributes (before update)
     * @param  array    $new      New attributes (after update)
     * @return ActivityLog|null   Null if nothing changed (for updates with no diff)
     */
    public function log(string $event, Model $subject, array $old = [], array $new = []): ?ActivityLog
    {
        // For updates, compute diff — only log if something actually changed
        if ($event === 'updated') {
            $diff = $this->computeDiff($old, $new);

            if (empty($diff['old']) && empty($diff['new'])) {
                return null; // Nothing changed, skip log
            }

            $old = $diff['old'];
            $new = $diff['new'];
        }

        return ActivityLog::create([
            'user_id'       => Auth::id(),
            'subject_type'  => get_class($subject),
            'subject_id'    => $subject->getKey(),
            'event'         => $event,
            'old_values'    => $old ?: null,
            'new_values'    => $new ?: null,
        ]);
    }

    /**
     * Log a custom event with explicit description.
     *
     * Use for events that don't map to a standard Eloquent lifecycle
     * (e.g. invoice "generated" vs "created").
     *
     * @param  Model         $subject     The model
     * @param  string        $event       Custom event name
     * @param  string        $description Human-readable description
     * @param  array<string, mixed>  $old  Old values
     * @param  array<string, mixed>  $new  New values
     * @return ActivityLog
     */
    public function logCustom(
        Model $subject,
        string $event,
        string $description = '',
        array $old = [],
        array $new = [],
    ): ActivityLog {
        $newValues = $new;

        if ($description) {
            $newValues['_description'] = $description;
        }

        return ActivityLog::create([
            'user_id'       => Auth::id(),
            'subject_type'  => get_class($subject),
            'subject_id'    => $subject->getKey(),
            'event'         => $event,
            'old_values'    => $old ?: null,
            'new_values'    => $newValues ?: null,
        ]);
    }

    /**
     * Compute the diff between old and new attribute arrays.
     *
     * Filters out timestamps, casts, and unchanged fields.
     * Only returns fields that actually changed.
     *
     * @param  array  $old  Old attributes
     * @param  array  $new  New attributes
     * @return array{old: array, new: array}
     */
    private function computeDiff(array $old, array $new): array
    {
        // Fields to always exclude from audit logs
        $excluded = ['updated_at', 'created_at', 'password', 'remember_token'];

        $oldFiltered = [];
        $newFiltered = [];

        $allKeys = array_unique(array_merge(array_keys($old), array_keys($new)));

        foreach ($allKeys as $key) {
            if (in_array($key, $excluded)) {
                continue;
            }

            $oldVal = $old[$key] ?? null;
            $newVal = $new[$key] ?? null;

            // Cast to string for comparison to handle type differences
            if ((string) $oldVal !== (string) $newVal) {
                $oldFiltered[$key] = $oldVal;
                $newFiltered[$key] = $newVal;
            }
        }

        return [
            'old' => $oldFiltered,
            'new' => $newFiltered,
        ];
    }
}
