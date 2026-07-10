<?php

namespace Tests\Feature\Admin;

use App\Livewire\Admin\RecapIndex;
use App\Models\Box;
use App\Models\Item;
use App\Models\User;
use App\Models\WhChinaData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class RecapRedesignTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $customer;
    private Box $box;
    private Item $item;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $this->box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Box::STATUS_OPEN,
        ]);
        $this->item = Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'RESI-001',
            'name' => 'Test Item',
            'quantity' => 5,
            'price_yuan' => 100.00,
            'status' => Item::STATUS_ACTIVE,
        ]);
    }

    // ─── Access Control ────────────────────────────────────────

    public function test_customer_cannot_access_recap(): void
    {
        $this->actingAs($this->customer)
            ->get('/admin/recap')
            ->assertForbidden();
    }

    public function test_admin_can_view_recap(): void
    {
        $this->actingAs($this->admin)
            ->get('/admin/recap')
            ->assertOk();
    }

    // ─── Tab Display ───────────────────────────────────────────

    public function test_recap_shows_two_tabs(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->assertSee('Customer')
            ->assertSee('WH China');
    }

    public function test_customer_tab_shows_items_with_resi(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->assertSee('RESI-001')
            ->assertSee('Test Item')
            ->assertSee('Unmatched');
    }

    // ─── WH China CRUD ─────────────────────────────────────────

    public function test_admin_can_input_wh_china_data(): void
    {
        $this->actingAs($this->admin);

        $photo = UploadedFile::fake()->image('barang.jpg', 640, 480);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->set('resiNumber', 'RESI-001')
            ->set('berat', '2.50')
            ->set('ukuranBox', '60x40x50')
            ->set('biayaJasa', '50000')
            ->set('fotoBarang', $photo)
            ->call('submitWhChinaData')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('wh_china_data', [
            'resi_number' => 'RESI-001',
            'berat' => 2.50,
            'ukuran_box' => '60x40x50',
            'biaya_jasa' => 50000,
            'input_by' => $this->admin->id,
        ]);
    }

    public function test_admin_can_input_wh_china_data_without_optional_fields(): void
    {
        $this->actingAs($this->admin);

        $photo = UploadedFile::fake()->image('barang.jpg', 640, 480);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->set('resiNumber', 'RESI-002')
            ->set('biayaJasa', '30000')
            ->set('fotoBarang', $photo)
            ->call('submitWhChinaData')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('wh_china_data', [
            'resi_number' => 'RESI-002',
            'biaya_jasa' => 30000,
        ]);
    }

    public function test_admin_can_input_wh_china_with_photo(): void
    {
        $this->actingAs($this->admin);

        $photo = UploadedFile::fake()->image('barang.jpg', 640, 480);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->set('resiNumber', 'RESI-003')
            ->set('berat', '3.00')
            ->set('ukuranBox', '50x50x50')
            ->set('biayaJasa', '45000')
            ->set('fotoBarang', $photo)
            ->call('submitWhChinaData')
            ->assertHasNoErrors();

        $whData = WhChinaData::where('resi_number', 'RESI-003')->first();
        $this->assertNotNull($whData->foto_barang);
        Storage::disk('public')->assertExists($whData->foto_barang);
    }

    // ─── Auto-Matching ─────────────────────────────────────────

    public function test_exact_resi_match_sets_status_matched(): void
    {
        $this->actingAs($this->admin);

        $photo = UploadedFile::fake()->image('test.jpg', 100, 100);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->set('resiNumber', 'RESI-001')
            ->set('berat', '2.50')
            ->set('ukuranBox', '60x40x50')
            ->set('biayaJasa', '50000')
            ->set('fotoBarang', $photo)
            ->call('submitWhChinaData');

        $whData = WhChinaData::where('resi_number', 'RESI-001')->first();
        $this->assertNotNull($whData->item_id);
        $this->assertEquals($this->item->id, $whData->item_id);
        $this->assertNotNull($whData->matched_at);
    }

    public function test_no_match_keeps_status_unmatched(): void
    {
        $this->actingAs($this->admin);

        $photo = UploadedFile::fake()->image('test.jpg', 100, 100);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->set('resiNumber', 'RESI-NOTEXIST')
            ->set('berat', '1.00')
            ->set('ukuranBox', '30x30x30')
            ->set('biayaJasa', '20000')
            ->set('fotoBarang', $photo)
            ->call('submitWhChinaData');

        $whData = WhChinaData::where('resi_number', 'RESI-NOTEXIST')->first();
        $this->assertNull($whData->item_id);
        $this->assertNull($whData->matched_at);
    }

    public function test_duplicate_resi_across_boxes_stays_unmatched(): void
    {
        // Create another item with same resi in different box
        $box2 = Box::factory()->create(['customer_id' => $this->customer->id]);
        Item::factory()->create([
            'box_id' => $box2->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'RESI-001', // same resi
            'status' => Item::STATUS_ACTIVE,
        ]);

        $this->actingAs($this->admin);

        $photo = UploadedFile::fake()->image('test.jpg', 100, 100);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->set('resiNumber', 'RESI-001')
            ->set('berat', '2.00')
            ->set('ukuranBox', '40x40x40')
            ->set('biayaJasa', '35000')
            ->set('fotoBarang', $photo)
            ->call('submitWhChinaData');

        $whData = WhChinaData::where('resi_number', 'RESI-001')->first();
        $this->assertNull($whData->item_id); // ambiguous — stays unmatched
    }

    public function test_batch_auto_match(): void
    {
        // Create item with different resi
        Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'RESI-002',
            'status' => Item::STATUS_ACTIVE,
        ]);

        // Create unmatched WH data
        WhChinaData::create(['resi_number' => 'RESI-001', 'berat' => 1, 'ukuran_box' => '30x30x30', 'input_by' => $this->admin->id]);
        WhChinaData::create(['resi_number' => 'RESI-002', 'berat' => 2, 'ukuran_box' => '40x40x40', 'input_by' => $this->admin->id]);
        WhChinaData::create(['resi_number' => 'RESI-NOMATCH', 'berat' => 3, 'ukuran_box' => '50x50x50', 'input_by' => $this->admin->id]);

        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->call('runAutoMatch');

        $this->assertNotNull(WhChinaData::where('resi_number', 'RESI-001')->first()->item_id);
        $this->assertNotNull(WhChinaData::where('resi_number', 'RESI-002')->first()->item_id);
        $this->assertNull(WhChinaData::where('resi_number', 'RESI-NOMATCH')->first()->item_id);
    }

    // ─── biaya_jasa Security ───────────────────────────────────

    public function test_biaya_jasa_hidden_from_customer_data_tab(): void
    {
        $whData = WhChinaData::create([
            'resi_number' => 'RESI-001',
            'berat' => 2,
            'ukuran_box' => '40x40x40',
            'biaya_jasa' => 75000,
            'input_by' => $this->admin->id,
        ]);
        $whData->update(['item_id' => $this->item->id, 'matched_at' => now()]);

        $this->actingAs($this->admin);

        // Customer data tab should not show biaya_jasa
        Livewire::test(RecapIndex::class)
            ->assertDontSee('75.000') // biaya_jasa formatted
            ->assertDontSee('75000');
    }

    public function test_biaya_jasa_visible_in_wh_china_admin_tab(): void
    {
        WhChinaData::create([
            'resi_number' => 'RESI-001',
            'berat' => 2,
            'ukuran_box' => '40x40x40',
            'biaya_jasa' => 75000,
            'input_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->assertSee('75.000'); // biaya_jasa formatted for admin
    }

    // ─── Validation ────────────────────────────────────────────

    public function test_validation_rejects_missing_required_fields(): void
    {
        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->call('submitWhChinaData')
            ->assertHasErrors(['resiNumber', 'biayaJasa', 'fotoBarang']);
    }

    public function test_validation_rejects_invalid_photo_type(): void
    {
        $this->actingAs($this->admin);

        $file = UploadedFile::fake()->create('document.pdf', 100);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->set('resiNumber', 'RESI-001')
            ->set('berat', '1.00')
            ->set('ukuranBox', '30x30x30')
            ->set('fotoBarang', $file)
            ->call('submitWhChinaData')
            ->assertHasErrors(['fotoBarang']);
    }

    // ─── Edit & Delete ─────────────────────────────────────────

    public function test_edit_wh_china_data_and_rematch(): void
    {
        $this->actingAs($this->admin);

        // Create unmatched WH data
        $whData = WhChinaData::create([
            'resi_number' => 'WRONG-RESI',
            'berat' => 1,
            'ukuran_box' => '30x30x30',
            'input_by' => $this->admin->id,
        ]);

        // Edit to correct resi
        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->call('editWhChinaData', $whData->id)
            ->set('resiNumber', 'RESI-001')
            ->set('biayaJasa', '50000')
            ->call('submitWhChinaData');

        $whData->refresh();
        $this->assertEquals($this->item->id, $whData->item_id);
        $this->assertNotNull($whData->matched_at);
    }

    public function test_delete_wh_china_data(): void
    {
        $whData = WhChinaData::create([
            'resi_number' => 'RESI-001',
            'berat' => 1,
            'ukuran_box' => '30x30x30',
            'input_by' => $this->admin->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->call('deleteWhChinaData', $whData->id);

        $this->assertDatabaseMissing('wh_china_data', ['id' => $whData->id]);
    }

    public function test_delete_wh_china_with_photo_cleans_up_file(): void
    {
        $photo = UploadedFile::fake()->image('barang.jpg');
        $path = $photo->store('wh-china-photos', 'public');

        $whData = WhChinaData::create([
            'resi_number' => 'RESI-001',
            'berat' => 1,
            'ukuran_box' => '30x30x30',
            'foto_barang' => $path,
            'input_by' => $this->admin->id,
        ]);

        Storage::disk('public')->assertExists($path);

        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->set('activeTab', 'wh-china')
            ->call('deleteWhChinaData', $whData->id);

        Storage::disk('public')->assertMissing($path);
    }

    // ─── Summary Stats ─────────────────────────────────────────

    public function test_summary_stats_include_wh_china_counts(): void
    {
        WhChinaData::create(['resi_number' => 'R1', 'berat' => 1, 'ukuran_box' => '30x30x30', 'input_by' => $this->admin->id]);
        $matched = WhChinaData::create(['resi_number' => 'RESI-001', 'berat' => 1, 'ukuran_box' => '30x30x30', 'input_by' => $this->admin->id]);
        $matched->update(['item_id' => $this->item->id, 'matched_at' => now()]);

        $this->actingAs($this->admin);

        Livewire::test(RecapIndex::class)
            ->assertSet('totalWhChina', 2)
            ->assertSet('totalMatched', 1)
            ->assertSet('totalUnmatched', 1);
    }
}
