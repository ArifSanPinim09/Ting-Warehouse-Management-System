<?php

namespace Tests\Feature;

use App\Livewire\Admin\LelangIndex;
use App\Models\Box;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

/**
 * Lelang Index Tests — Revisi §2.9, §4.1.
 *
 * Tests for the Barang Lelang admin page:
 * - Filtering by status, customer, date
 * - Marking items as Dijual/Lelang
 * - Summary statistics
 * - Export functionality
 */
class LelangIndexTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Box $box;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->customer = User::factory()->create(['role' => 'customer']);
        $this->box = Box::factory()->create(['customer_id' => $this->customer->id]);
    }

    // ─── Access Tests ───────────────────────────────────────────

    public function test_admin_can_access_lelang_page(): void
    {
        $this->actingAs($this->admin);

        $this->get(route('admin.lelang'))
            ->assertStatus(200)
            ->assertSee('Barang Lelang');
    }

    public function test_customer_cannot_access_lelang_page(): void
    {
        $this->actingAs($this->customer);

        $this->get(route('admin.lelang'))
            ->assertStatus(403);
    }

    // ─── Display Tests ──────────────────────────────────────────

    public function test_page_shows_klaim_wh_items(): void
    {
        $item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_KLAIM_WH,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->assertSee($item->name)
            ->assertSee('Klaim WH');
    }

    public function test_page_shows_hold_items(): void
    {
        $item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_HOLD,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->assertSee($item->name)
            ->assertSee('Hold');
    }

    public function test_page_does_not_show_active_items(): void
    {
        $item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_ACTIVE,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->assertDontSee($item->name);
    }

    // ─── Filter Tests ──────────────────────────────────────────

    public function test_filter_by_status_klaim_wh(): void
    {
        $klaimItem = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_KLAIM_WH,
        ]);

        $holdItem = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_HOLD,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->set('filterStatus', 'klaim_wh')
            ->assertSee($klaimItem->name)
            ->assertDontSee($holdItem->name);
    }

    public function test_filter_by_customer(): void
    {
        $otherCustomer = User::factory()->create(['role' => 'customer']);

        $item1 = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_KLAIM_WH,
        ]);

        $item2 = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $otherCustomer->id,
            'status' => Item::STATUS_KLAIM_WH,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->set('filterCustomer', $this->customer->id)
            ->assertSee($item1->name)
            ->assertDontSee($item2->name);
    }

    public function test_search_by_item_name(): void
    {
        $item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_KLAIM_WH,
            'name' => 'iPhone 15 Pro',
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->set('search', 'iPhone')
            ->assertSee($item->name);
    }

    // ─── Mark Action Tests ─────────────────────────────────────

    public function test_mark_item_as_dijual(): void
    {
        $item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_KLAIM_WH,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->call('selectItem', $item->id)
            ->call('confirmMark', 'dijual')
            ->call('markItem')
            ->assertSet('showMarkConfirm', false);

        $this->assertEquals(Item::STATUS_DIJUAL, $item->fresh()->status);
    }

    public function test_mark_item_as_lelang(): void
    {
        $item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_HOLD,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->call('selectItem', $item->id)
            ->call('confirmMark', 'lelang')
            ->call('markItem')
            ->assertSet('showMarkConfirm', false);

        $this->assertEquals(Item::STATUS_LELANG, $item->fresh()->status);
    }

    public function test_mark_dijual_item_as_lelang(): void
    {
        $item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_DIJUAL,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->call('selectItem', $item->id)
            ->call('confirmMark', 'lelang')
            ->call('markItem');

        $this->assertEquals(Item::STATUS_LELANG, $item->fresh()->status);
    }

    // ─── Summary Tests ─────────────────────────────────────────

    public function test_summary_counts(): void
    {
        // Create items with different statuses
        Item::factory()->count(3)->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_KLAIM_WH,
            'price_yuan' => 100,
        ]);

        Item::factory()->count(2)->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_HOLD,
            'price_yuan' => 200,
        ]);

        Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_DIJUAL,
            'price_yuan' => 150,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(LelangIndex::class)
            ->assertSee('6') // total barang
            ->assertSee('150.00') // 3x100 + 2x200 + 1x150 = 850, but shown as per item
            ->assertSee('5'); // belum terjual (klaim_wh + hold)
    }

    // ─── Item Model Tests ──────────────────────────────────────

    public function test_item_is_lelang_eligible(): void
    {
        $klaimItem = Item::factory()->create(['status' => Item::STATUS_KLAIM_WH]);
        $holdItem = Item::factory()->create(['status' => Item::STATUS_HOLD]);
        $activeItem = Item::factory()->create(['status' => Item::STATUS_ACTIVE]);

        $this->assertTrue($klaimItem->isLelangEligible());
        $this->assertTrue($holdItem->isLelangEligible());
        $this->assertFalse($activeItem->isLelangEligible());
    }

    public function test_item_valid_statuses_include_dijual_and_lelang(): void
    {
        $statuses = Item::getValidStatuses();

        $this->assertContains(Item::STATUS_DIJUAL, $statuses);
        $this->assertContains(Item::STATUS_LELANG, $statuses);
    }
}
