<?php

namespace Tests\Unit\Services;

use App\Models\Item;
use App\Models\User;
use App\Services\ItemStatusService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use InvalidArgumentException;
use Tests\TestCase;

/**
 * Unit tests for ItemStatusService — Revisi §2.5.2
 *
 * Explicit state machine for item claim statuses.
 * claimed and klaim_wh are TERMINAL — cannot revert.
 *
 * Status Flow:
 *   active → no_tuan → claimed  (terminal, with denda)
 *              └──→ klaim_wh (terminal, WH auction)
 *   active → shipped (terminal)
 */
class ItemStatusServiceTest extends TestCase
{
    use RefreshDatabase;

    private ItemStatusService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ItemStatusService();
    }

    // ─── Valid Transitions ──────────────────────────────────────────

    public function test_active_to_no_tuan(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $result = $this->service->transition($item, Item::STATUS_NO_TUAN);

        $this->assertEquals(Item::STATUS_NO_TUAN, $result->status);
    }

    public function test_active_to_shipped(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $result = $this->service->transition($item, Item::STATUS_SHIPPED);

        $this->assertEquals(Item::STATUS_SHIPPED, $result->status);
    }

    public function test_no_tuan_to_claimed(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_NO_TUAN]);

        $result = $this->service->transition($item, Item::STATUS_CLAIMED);

        $this->assertEquals(Item::STATUS_CLAIMED, $result->status);
    }

    public function test_no_tuan_to_klaim_wh(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_NO_TUAN]);

        $result = $this->service->transition($item, Item::STATUS_KLAIM_WH);

        $this->assertEquals(Item::STATUS_KLAIM_WH, $result->status);
    }

    // ─── Terminal Status Enforcement (§2.1.2 poin 5) ────────────────

    public function test_claimed_is_terminal(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_CLAIMED]);

        $this->assertTrue($this->service->isTerminal(Item::STATUS_CLAIMED));
        $this->assertEmpty($this->service->getAllowedTransitions(Item::STATUS_CLAIMED));
    }

    public function test_claimed_cannot_transition_to_any_status(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_CLAIMED]);

        $allStatuses = Item::getValidStatuses();
        foreach ($allStatuses as $targetStatus) {
            $this->assertFalse(
                $this->service->canTransition(Item::STATUS_CLAIMED, $targetStatus),
                "claimed should NOT be able to transition to {$targetStatus}"
            );
        }
    }

    public function test_klaim_wh_is_terminal(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_KLAIM_WH]);

        $this->assertTrue($this->service->isTerminal(Item::STATUS_KLAIM_WH));
        $this->assertEmpty($this->service->getAllowedTransitions(Item::STATUS_KLAIM_WH));
    }

    public function test_klaim_wh_cannot_transition_to_any_status(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_KLAIM_WH]);

        $allStatuses = Item::getValidStatuses();
        foreach ($allStatuses as $targetStatus) {
            $this->assertFalse(
                $this->service->canTransition(Item::STATUS_KLAIM_WH, $targetStatus),
                "klaim_wh should NOT be able to transition to {$targetStatus}"
            );
        }
    }

    public function test_shipped_is_terminal(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_SHIPPED]);

        $this->assertTrue($this->service->isTerminal(Item::STATUS_SHIPPED));
        $this->assertEmpty($this->service->getAllowedTransitions(Item::STATUS_SHIPPED));
    }

    public function test_shipped_cannot_transition_to_any_status(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_SHIPPED]);

        $allStatuses = Item::getValidStatuses();
        foreach ($allStatuses as $targetStatus) {
            $this->assertFalse(
                $this->service->canTransition(Item::STATUS_SHIPPED, $targetStatus),
                "shipped should NOT be able to transition to {$targetStatus}"
            );
        }
    }

    // ─── Invalid Transitions ────────────────────────────────────────

    public function test_claimed_cannot_go_back_to_no_tuan(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_CLAIMED]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_NO_TUAN);
    }

    public function test_klaim_wh_cannot_go_back_to_no_tuan(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_KLAIM_WH]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_NO_TUAN);
    }

    public function test_active_cannot_go_directly_to_claimed(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_CLAIMED);
    }

    public function test_active_cannot_go_directly_to_klaim_wh(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_KLAIM_WH);
    }

    public function test_no_tuan_cannot_go_back_to_active(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_NO_TUAN]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_ACTIVE);
    }

    public function test_no_tuan_cannot_go_to_shipped(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_NO_TUAN]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_SHIPPED);
    }

    public function test_shipped_cannot_go_to_no_tuan(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_SHIPPED]);

        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_NO_TUAN);
    }

    // ─── canTransition Tests ────────────────────────────────────────

    public function test_can_transition_returns_true_for_valid(): void
    {
        $this->assertTrue($this->service->canTransition(Item::STATUS_ACTIVE, Item::STATUS_NO_TUAN));
        $this->assertTrue($this->service->canTransition(Item::STATUS_ACTIVE, Item::STATUS_SHIPPED));
        $this->assertTrue($this->service->canTransition(Item::STATUS_NO_TUAN, Item::STATUS_CLAIMED));
        $this->assertTrue($this->service->canTransition(Item::STATUS_NO_TUAN, Item::STATUS_KLAIM_WH));
    }

    public function test_can_transition_returns_false_for_invalid(): void
    {
        $this->assertFalse($this->service->canTransition(Item::STATUS_ACTIVE, Item::STATUS_CLAIMED));
        $this->assertFalse($this->service->canTransition(Item::STATUS_ACTIVE, Item::STATUS_KLAIM_WH));
        $this->assertFalse($this->service->canTransition(Item::STATUS_CLAIMED, Item::STATUS_NO_TUAN));
        $this->assertFalse($this->service->canTransition(Item::STATUS_KLAIM_WH, Item::STATUS_ACTIVE));
        $this->assertFalse($this->service->canTransition(Item::STATUS_SHIPPED, Item::STATUS_ACTIVE));
    }

    // ─── Helper Method Tests ────────────────────────────────────────

    public function test_mark_no_tuan(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $result = $this->service->markNoTuan($item);

        $this->assertEquals(Item::STATUS_NO_TUAN, $result->fresh()->status);
    }

    public function test_claim_item(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_NO_TUAN]);

        $result = $this->service->claimItem($item);

        $this->assertEquals(Item::STATUS_CLAIMED, $result->fresh()->status);
    }

    public function test_claim_by_wh(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_NO_TUAN]);

        $result = $this->service->claimByWh($item);

        $this->assertEquals(Item::STATUS_KLAIM_WH, $result->fresh()->status);
    }

    public function test_mark_shipped(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $result = $this->service->markShipped($item);

        $this->assertEquals(Item::STATUS_SHIPPED, $result->fresh()->status);
    }

    // ─── Terminal Statuses List ─────────────────────────────────────

    public function test_get_terminal_statuses(): void
    {
        $terminals = $this->service->getTerminalStatuses();

        $this->assertCount(3, $terminals);
        $this->assertContains(Item::STATUS_CLAIMED, $terminals);
        $this->assertContains(Item::STATUS_KLAIM_WH, $terminals);
        $this->assertContains(Item::STATUS_SHIPPED, $terminals);
    }

    // ─── Item Model Status Constants ────────────────────────────────

    public function test_item_status_constants(): void
    {
        $this->assertEquals('active', Item::STATUS_ACTIVE);
        $this->assertEquals('no_tuan', Item::STATUS_NO_TUAN);
        $this->assertEquals('claimed', Item::STATUS_CLAIMED);
        $this->assertEquals('klaim_wh', Item::STATUS_KLAIM_WH);
        $this->assertEquals('shipped', Item::STATUS_SHIPPED);
    }

    public function test_item_get_valid_statuses(): void
    {
        $statuses = Item::getValidStatuses();

        $this->assertCount(5, $statuses);
        $this->assertContains(Item::STATUS_ACTIVE, $statuses);
        $this->assertContains(Item::STATUS_NO_TUAN, $statuses);
        $this->assertContains(Item::STATUS_CLAIMED, $statuses);
        $this->assertContains(Item::STATUS_KLAIM_WH, $statuses);
        $this->assertContains(Item::STATUS_SHIPPED, $statuses);
    }

    // ─── Full Flow Test ─────────────────────────────────────────────

    public function test_full_claim_flow(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        // Step 1: active → no_tuan
        $this->service->markNoTuan($item);
        $this->assertEquals(Item::STATUS_NO_TUAN, $item->fresh()->status);

        // Step 2: no_tuan → claimed
        $this->service->claimItem($item);
        $this->assertEquals(Item::STATUS_CLAIMED, $item->fresh()->status);

        // Step 3: claimed → no_tuan should FAIL (terminal)
        $item = $item->fresh();
        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_NO_TUAN);
    }

    public function test_full_klaim_wh_flow(): void
    {
        $item = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        // Step 1: active → no_tuan
        $this->service->markNoTuan($item);
        $this->assertEquals(Item::STATUS_NO_TUAN, $item->fresh()->status);

        // Step 2: no_tuan → klaim_wh
        $this->service->claimByWh($item);
        $this->assertEquals(Item::STATUS_KLAIM_WH, $item->fresh()->status);

        // Step 3: klaim_wh → claimed should FAIL (terminal)
        $item = $item->fresh();
        $this->expectException(InvalidArgumentException::class);
        $this->service->transition($item, Item::STATUS_CLAIMED);
    }
}
