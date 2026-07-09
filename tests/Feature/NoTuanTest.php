<?php

namespace Tests\Feature;

use App\Livewire\Admin\ManageBox;
use App\Livewire\Customer\NoTuanIndex;
use App\Models\Box;
use App\Models\DendaClaim;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class NoTuanTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Box $box;
    private Item $activeItem;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $this->box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Box::STATUS_OPEN,
        ]);
        $this->activeItem = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'status' => Item::STATUS_ACTIVE,
        ]);
        // Create admin for notifications
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
    }

    // ─── Admin: Mark No Tuan ────────────────────────────────────────

    public function test_admin_can_mark_item_as_no_tuan(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('markItemNoTuan', $this->activeItem->id);

        $this->assertEquals(Item::STATUS_NO_TUAN, $this->activeItem->fresh()->status);
    }

    public function test_admin_cannot_mark_non_active_item_as_no_tuan(): void
    {
        $this->activeItem->update(['status' => Item::STATUS_SHIPPED]);

        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('markItemNoTuan', $this->activeItem->id)
            ->assertDispatched('toast');

        $this->assertEquals(Item::STATUS_SHIPPED, $this->activeItem->fresh()->status);
    }

    // ─── Admin: Mark Klaim WH ───────────────────────────────────────

    public function test_admin_can_mark_no_tuan_item_as_klaim_wh(): void
    {
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('markItemKlaimWh', $this->activeItem->id);

        $this->assertEquals(Item::STATUS_KLAIM_WH, $this->activeItem->fresh()->status);
    }

    public function test_admin_cannot_mark_active_item_as_klaim_wh(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('markItemKlaimWh', $this->activeItem->id)
            ->assertDispatched('toast');

        $this->assertEquals(Item::STATUS_ACTIVE, $this->activeItem->fresh()->status);
    }

    // ─── Customer: No Tuan Page ─────────────────────────────────────

    public function test_customer_can_view_no_tuan_page(): void
    {
        $this->actingAs($this->customer);

        $this->get('/no-tuan')->assertStatus(200);
    }

    public function test_no_tuan_page_shows_no_tuan_items(): void
    {
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->assertSee($this->activeItem->name);
    }

    public function test_no_tuan_page_does_not_show_active_items(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->assertDontSee($this->activeItem->name);
    }

    // ─── Customer: Claim Item ───────────────────────────────────────

    public function test_customer_can_claim_no_tuan_item(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->set('proofPembelian', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->set('keterangan', 'Ini barang saya')
            ->call('submitClaim');

        // Item status should be claimed
        $this->assertEquals(Item::STATUS_CLAIMED, $this->activeItem->fresh()->status);

        // Denda claim should be created
        $this->assertDatabaseHas('denda_claims', [
            'customer_id' => $this->customer->id,
            'item_id' => $this->activeItem->id,
            'jumlah_denda' => 5000,
            'status' => 'pending',
        ]);
    }

    public function test_claim_creates_denda_with_correct_amount(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->set('proofPembelian', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->call('submitClaim');

        $denda = DendaClaim::where('customer_id', $this->customer->id)
            ->where('item_id', $this->activeItem->id)
            ->first();

        $this->assertNotNull($denda);
        $this->assertEqualsWithDelta(5000.0, (float) $denda->jumlah_denda, 0.01);
        $this->assertEquals('pending', $denda->status);
    }

    public function test_claim_rejects_already_claimed_item(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_CLAIMED]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->assertDispatched('toast');
    }

    public function test_claim_rejects_klaim_wh_item(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_KLAIM_WH]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->assertDispatched('toast');
    }

    // ─── Validation Tests (§2.1.4) ──────────────────────────────────

    public function test_claim_requires_proof_file(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->set('proofPembelian', null)
            ->call('submitClaim')
            ->assertHasErrors(['proofPembelian']);
    }

    public function test_claim_validates_file_type(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->set('proofPembelian', UploadedFile::fake()->create('proof.pdf', 100))
            ->call('submitClaim')
            ->assertHasErrors(['proofPembelian']);
    }

    public function test_claim_validates_file_size(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->set('proofPembelian', UploadedFile::fake()->image('proof.jpg')->size(6000)) // 6MB > 5MB
            ->call('submitClaim')
            ->assertHasErrors(['proofPembelian']);
    }

    // ─── Race Condition Test ────────────────────────────────────────

    public function test_concurrent_claims_only_one_succeeds(): void
    {
        Storage::fake('public');
        $this->activeItem->update(['status' => Item::STATUS_NO_TUAN]);

        $customer2 = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);

        // Customer 1 claims
        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->set('proofPembelian', UploadedFile::fake()->image('proof1.jpg', 100, 100))
            ->call('submitClaim');

        // Item should be claimed now
        $this->assertEquals(Item::STATUS_CLAIMED, $this->activeItem->fresh()->status);

        // Customer 2 tries to claim the same item — should fail
        $this->actingAs($customer2);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->assertDispatched('toast');

        // Only one denda claim should exist
        $this->assertEquals(1, DendaClaim::where('item_id', $this->activeItem->id)->count());
    }

    // ─── Full Flow Test ─────────────────────────────────────────────

    public function test_full_no_tuan_flow(): void
    {
        Storage::fake('public');

        // Step 1: Admin marks item as No Tuan
        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('markItemNoTuan', $this->activeItem->id);

        $this->assertEquals(Item::STATUS_NO_TUAN, $this->activeItem->fresh()->status);

        // Step 2: Customer sees item on No Tuan page
        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->assertSee($this->activeItem->name);

        // Step 3: Customer claims item
        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->set('proofPembelian', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->set('keterangan', 'Ini barang saya yang hilang')
            ->call('submitClaim');

        // Step 4: Verify final state
        $this->assertEquals(Item::STATUS_CLAIMED, $this->activeItem->fresh()->status);
        $this->assertDatabaseHas('denda_claims', [
            'customer_id' => $this->customer->id,
            'item_id' => $this->activeItem->id,
            'jumlah_denda' => 5000,
            'status' => 'pending',
        ]);
    }

    public function test_full_klaim_wh_flow(): void
    {
        // Step 1: Admin marks item as No Tuan
        $this->actingAs($this->admin);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('markItemNoTuan', $this->activeItem->id);

        $this->assertEquals(Item::STATUS_NO_TUAN, $this->activeItem->fresh()->status);

        // Step 2: Admin marks item as Klaim WH (deadline passed)
        Livewire::test(ManageBox::class)
            ->call('selectBox', $this->box->id)
            ->call('markItemKlaimWh', $this->activeItem->id);

        $this->assertEquals(Item::STATUS_KLAIM_WH, $this->activeItem->fresh()->status);

        // Step 3: Customer cannot claim Klaim WH item
        $this->actingAs($this->customer);

        Livewire::test(NoTuanIndex::class)
            ->call('selectItem', $this->activeItem->id)
            ->assertDispatched('toast');
    }

    // ─── Customer Cannot Access ManageBox ───────────────────────────

    public function test_customer_cannot_access_manage_box(): void
    {
        $this->actingAs($this->customer);

        $this->get('/admin/manage-boxes')->assertForbidden();
    }
}
