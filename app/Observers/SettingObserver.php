<?php

namespace App\Observers;

use App\Models\Setting;
use App\Services\AuditLogService;

/**
 * Setting Observer — logs rate/configuration changes.
 *
 * CLAUDE.md §3.3: Audit trail for Setting (rate update).
 * CLAUDE.md §3.3: "Admin harus bisa update rate kapan saja"
 */
class SettingObserver
{
    /** @var array<string, string> Old values captured before update */
    private static array $oldValues = [];

    public function __construct(
        private AuditLogService $auditLog,
    ) {}

    /**
     * Handle the Setting "updating" event.
     *
     * Captures old value BEFORE the update is applied, so we can diff.
     */
    public function updating(Setting $setting): void
    {
        if ($setting->wasChanged('value')) {
            static::$oldValues[$setting->getKey()] = $setting->getOriginal('value');
        }
    }

    /**
     * Handle the Setting "updated" event.
     *
     * Logs when rate or configuration values change.
     */
    public function updated(Setting $setting): void
    {
        if ($setting->wasChanged('value')) {
            $oldValue = static::$oldValues[$setting->getKey()] ?? null;

            $this->auditLog->log(
                event: 'updated',
                subject: $setting,
                old: [
                    'key'   => $setting->key,
                    'value' => $oldValue,
                ],
                new: [
                    'key'   => $setting->key,
                    'value' => $setting->value,
                ],
            );

            unset(static::$oldValues[$setting->getKey()]);
        }
    }

    /**
     * Handle the Setting "created" event.
     *
     * Logs new setting creation.
     */
    public function created(Setting $setting): void
    {
        $this->auditLog->log(
            event: 'created',
            subject: $setting,
            old: [],
            new: [
                'key'   => $setting->key,
                'value' => $setting->value,
                'group' => $setting->group,
            ],
        );
    }
}
