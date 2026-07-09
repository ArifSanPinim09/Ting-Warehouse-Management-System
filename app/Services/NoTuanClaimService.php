<?php

namespace App\Services;

use App\Models\DendaClaim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

/**
 * No Tuan Claim Service — Handles the full claim flow for No Tuan items.
 *
 * Revisi §2.1: Customer claims No Tuan item → denda Rp 5.000
 * Revisi §2.5: Klaim WH for deadline items
 *
 * CRITICAL: Uses DB::transaction + lockForUpdate() to prevent race conditions
 * when two customers claim the same item simultaneously.
 */
class NoTuanClaimService
{
    public function __construct(
        private readonly ItemStatusService $itemStatusService,
        private readonly NotificationService $notifService,
    ) {}

    /**
     * Admin marks an item as No Tuan (unclaimed).
     *
     * @param  Item   $item
     * @return Item
     *
     * @throws InvalidArgumentException If item is not in active status
     */
    public function markNoTuan(Item $item): Item
    {
        return $this->itemStatusService->markNoTuan($item);
    }

    /**
     * Admin marks a No Tuan item as Klaim WH (for auction/sale).
     *
     * @param  Item   $item
     * @return Item
     *
     * @throws InvalidArgumentException If item is not in no_tuan status
     */
    public function markKlaimWh(Item $item): Item
    {
        return $this->itemStatusService->claimByWh($item);
    }

    /**
     * Customer claims a No Tuan item with denda penalty.
     *
     * Uses DB::transaction + lockForUpdate() to prevent race conditions:
     * 1. Lock the item row with SELECT ... FOR UPDATE
     * 2. Verify status is still no_tuan
     * 3. Transition to claimed
     * 4. Create denda_claims entry
     * 5. Notify customer (Revisi §2.11.2)
     *
     * @param  Item       $item      The item to claim
     * @param  User       $customer  The customer claiming
     * @param  string     $proofPath Path to uploaded proof file
     * @param  string|null $keterangan Optional notes
     *
     * @return DendaClaim The created denda claim record
     *
     * @throws InvalidArgumentException If item is not available for claiming
     */
    public function claimItem(
        Item $item,
        User $customer,
        string $proofPath,
        ?string $keterangan = null,
    ): DendaClaim {
        return DB::transaction(function () use ($item, $customer, $proofPath, $keterangan) {
            // Lock the row to prevent concurrent claims (Revisi §2.1.4)
            $lockedItem = Item::where('id', $item->id)
                ->lockForUpdate()
                ->first();

            if (!$lockedItem) {
                throw new InvalidArgumentException('Barang tidak ditemukan.');
            }

            // Verify item is still no_tuan — §8.1: "Barang sudah diklaim oleh customer lain."
            if ($lockedItem->status !== Item::STATUS_NO_TUAN) {
                throw new InvalidArgumentException('Barang sudah diklaim oleh customer lain.');
            }

            // Transition to claimed
            $this->itemStatusService->claimItem($lockedItem);

            // Create denda_claims entry (Revisi §2.4.3)
            $dendaClaim = DendaClaim::create([
                'customer_id' => $customer->id,
                'item_id' => $lockedItem->id,
                'jumlah_denda' => 5000,
                'status' => DendaClaim::STATUS_PENDING,
            ]);

            // Revisi §2.11.2: Notify customer klaim berhasil
            $lockedItem->customer_id = $customer->id; // temporary for notification
            $this->notifService->claimSuccessful($lockedItem);

            return $dendaClaim;
        });
    }
}
