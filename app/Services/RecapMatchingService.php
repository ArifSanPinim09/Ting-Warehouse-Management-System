<?php

namespace App\Services;

use App\Models\Item;
use App\Models\WhChinaData;
use Illuminate\Database\Eloquent\Collection;

/**
 * Auto-matching service for WH China data ↔ customer items.
 * Exact match on resi_number only — NO fuzzy matching.
 * Reason: fuzzy match risks assigning wrong customer's items to WH data.
 */
class RecapMatchingService
{
    /**
     * Try to match a single WH China record with a customer item by exact resi_number.
     *
     * Rules:
     * - Exact match on resi_number only
     * - If multiple items share the same resi (across different boxes), skip — admin resolves manually
     * - Returns the matched Item or null
     */
    public function matchByResi(WhChinaData $whData): ?Item
    {
        if ($whData->isMatched()) {
            return $whData->item;
        }

        $items = Item::where('resi_number', $whData->resi_number)->get();

        // Multiple items with same resi across boxes — ambiguous, let admin resolve
        if ($items->count() !== 1) {
            return null;
        }

        $item = $items->first();

        // Check if another WH data already matched this item
        $alreadyMatched = WhChinaData::where('item_id', $item->id)
            ->where('id', '!=', $whData->id)
            ->exists();

        if ($alreadyMatched) {
            return null;
        }

        $whData->update([
            'item_id' => $item->id,
            'matched_at' => now(),
        ]);

        return $item;
    }

    /**
     * Batch match all unmatched WH China data.
     * Returns count of newly matched records.
     */
    public function tryMatchAll(): int
    {
        $unmatched = WhChinaData::whereNull('item_id')->get();
        $matched = 0;

        foreach ($unmatched as $whData) {
            if ($this->matchByResi($whData)) {
                $matched++;
            }
        }

        return $matched;
    }

    /**
     * Customer items that don't have WH China data yet.
     */
    public function getUnmatchedCustomerItems(): Collection
    {
        return Item::whereDoesntHave('whChinaData')
            ->whereNotNull('resi_number')
            ->with(['box', 'customer'])
            ->latest()
            ->get();
    }

    /**
     * WH China data that hasn't been matched to any customer item.
     */
    public function getUnmatchedWhData(): Collection
    {
        return WhChinaData::whereNull('item_id')
            ->with('admin')
            ->latest()
            ->get();
    }
}
