<?php

namespace App\Services;

use App\Models\Item;
use InvalidArgumentException;

/**
 * Item Status Service — Explicit state machine for item claim statuses.
 *
 * Revisi §2.5.2: 5 statuses with defined transitions.
 * Revisi §2.1.2 poin 5: claimed and klaim_wh are TERMINAL — once reached,
 * cannot be reverted to no_tuan or claimed again.
 *
 * Status Flow:
 *   active ──→ no_tuan ──→ claimed  (terminal, with denda)
 *                │
 *                └──→ klaim_wh (terminal, WH claims for auction/sale)
 *   active ──→ shipped (terminal, normal delivery)
 *
 * Valid Transitions:
 *   active   → [no_tuan, shipped]
 *   no_tuan  → [claimed, klaim_wh]
 *   claimed  → [] (terminal)
 *   klaim_wh → [] (terminal)
 *   shipped  → [] (terminal)
 */
class ItemStatusService
{
    /**
     * Valid status transitions map.
     * Key = from status, Value = array of allowed to statuses.
     *
     * @var array<string, array<string>>
     */
    private const TRANSITIONS = [
        Item::STATUS_ACTIVE => [
            Item::STATUS_NO_TUAN,
            Item::STATUS_SHIPPED,
        ],
        Item::STATUS_NO_TUAN => [
            Item::STATUS_CLAIMED,
            Item::STATUS_KLAIM_WH,
        ],
        // Terminal statuses — no transitions allowed
        Item::STATUS_CLAIMED => [],
        Item::STATUS_KLAIM_WH => [],
        Item::STATUS_SHIPPED => [],
    ];

    /**
     * Terminal statuses — once an item reaches these, it cannot transition further.
     *
     * @var array<string>
     */
    private const TERMINAL_STATUSES = [
        Item::STATUS_CLAIMED,
        Item::STATUS_KLAIM_WH,
        Item::STATUS_SHIPPED,
    ];

    /**
     * Transition an item to a new status.
     *
     * @param  Item    $item      The item to transition
     * @param  string  $newStatus Target status
     * @return Item    The updated item
     *
     * @throws InvalidArgumentException If transition is not valid
     */
    public function transition(Item $item, string $newStatus): Item
    {
        $oldStatus = $item->status;

        if (!$this->canTransition($oldStatus, $newStatus)) {
            throw new InvalidArgumentException(
                "Transisi status tidak valid: {$oldStatus} → {$newStatus}. " .
                "Item #{$item->id} tidak bisa berpindah dari status '{$oldStatus}' ke '{$newStatus}'."
            );
        }

        $item->status = $newStatus;
        $item->save();

        return $item;
    }

    /**
     * Check if a transition from one status to another is valid.
     *
     * @param  string  $fromStatus Current status
     * @param  string  $toStatus   Target status
     * @return bool
     */
    public function canTransition(string $fromStatus, string $toStatus): bool
    {
        $allowed = self::TRANSITIONS[$fromStatus] ?? [];

        return in_array($toStatus, $allowed, true);
    }

    /**
     * Check if a status is terminal (no further transitions allowed).
     *
     * @param  string  $status
     * @return bool
     */
    public function isTerminal(string $status): bool
    {
        return in_array($status, self::TERMINAL_STATUSES, true);
    }

    /**
     * Get all allowed transitions from a given status.
     *
     * @param  string  $fromStatus
     * @return array<string>
     */
    public function getAllowedTransitions(string $fromStatus): array
    {
        return self::TRANSITIONS[$fromStatus] ?? [];
    }

    /**
     * Get the terminal statuses.
     *
     * @return array<string>
     */
    public function getTerminalStatuses(): array
    {
        return self::TERMINAL_STATUSES;
    }

    /**
     * Mark an item as No Tuan (unclaimed).
     *
     * @param  Item  $item
     * @return Item
     *
     * @throws InvalidArgumentException If item is not in active status
     */
    public function markNoTuan(Item $item): Item
    {
        return $this->transition($item, Item::STATUS_NO_TUAN);
    }

    /**
     * Customer claims a No Tuan item (with denda penalty).
     *
     * @param  Item  $item
     * @return Item
     *
     * @throws InvalidArgumentException If item is not in no_tuan status
     */
    public function claimItem(Item $item): Item
    {
        return $this->transition($item, Item::STATUS_CLAIMED);
    }

    /**
     * WH claims an item for auction/sale (terminal).
     *
     * @param  Item  $item
     * @return Item
     *
     * @throws InvalidArgumentException If item is not in no_tuan status
     */
    public function claimByWh(Item $item): Item
    {
        return $this->transition($item, Item::STATUS_KLAIM_WH);
    }

    /**
     * Mark an item as shipped (normal delivery flow).
     *
     * @param  Item  $item
     * @return Item
     *
     * @throws InvalidArgumentException If item is not in active status
     */
    public function markShipped(Item $item): Item
    {
        return $this->transition($item, Item::STATUS_SHIPPED);
    }
}
