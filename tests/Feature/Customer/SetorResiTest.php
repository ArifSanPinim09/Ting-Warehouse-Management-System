<?php

namespace Tests\Feature\Customer;

use App\Livewire\Customer\SetorResi;
use App\Models\Box;
use App\Models\Item;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class SetorResiTest extends TestCase
{
    use RefreshDatabase;

    private User $customer;
    private Box $box;

    protected function setUp(): void
    {
        parent::setUp();
        $this->customer = User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
        $this->box = Box::factory()->create([
            'customer_id' => $this->customer->id,
            'status' => Box::STATUS_OPEN,
        ]);
        // Create admin so notifyAdmins() has a target (avoids null return)
        User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
    }

    /**
     * PRD §4.4: Customer can submit resi
     */
    public function test_customer_can_submit_resi(): void
    {
        Storage::fake('public');

        $this->actingAs($this->customer);

        Livewire::test(SetorResi::class)
            ->set('boxId', $this->box->id)
            ->set('name', 'Test Barang')
            ->set('quantity', 5)
            ->set('priceYuan', '100.50')
            ->set('resiNumber', 'RESI-123456')
            ->set('proofCo', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->set('isSensitive', false)
            ->call('submit');

        $this->assertDatabaseHas('items', [
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'name' => 'Test Barang',
            'quantity' => 5,
            'resi_number' => 'RESI-123456',
        ]);
    }

    /**
     * PRD §4.4: Duplicate resi in same box rejected
     */
    public function test_resi_duplicate_in_same_box_rejected(): void
    {
        Storage::fake('public');

        Item::factory()->create([
            'box_id' => $this->box->id,
            'customer_id' => $this->customer->id,
            'resi_number' => 'RESI-DUPLICATE',
        ]);

        $this->actingAs($this->customer);

        Livewire::test(SetorResi::class)
            ->set('boxId', $this->box->id)
            ->set('name', 'Test Barang')
            ->set('quantity', 1)
            ->set('priceYuan', '50')
            ->set('resiNumber', 'RESI-DUPLICATE')
            ->set('proofCo', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->set('isSensitive', false)
            ->call('submit')
            ->assertHasErrors(['resiNumber']);
    }

    /**
     * PRD §12.5: File upload validated (mimes, size)
     */
    public function test_file_upload_validated(): void
    {
        $this->actingAs($this->customer);

        Livewire::test(SetorResi::class)
            ->set('boxId', $this->box->id)
            ->set('name', 'Test Barang')
            ->set('quantity', 1)
            ->set('priceYuan', '50')
            ->set('resiNumber', 'RESI-VALIDATE')
            ->set('proofCo', UploadedFile::fake()->image('proof.gif', 100, 100))
            ->set('isSensitive', false)
            ->call('submit')
            ->assertHasErrors(['proofCo']);
    }

    /**
     * Closed box rejected
     */
    public function test_closed_box_rejected(): void
    {
        $this->box->update(['status' => Box::STATUS_DONE]);

        $this->actingAs($this->customer);

        Livewire::test(SetorResi::class)
            ->set('boxId', $this->box->id)
            ->set('name', 'Test Barang')
            ->set('quantity', 1)
            ->set('priceYuan', '50')
            ->set('resiNumber', 'RESI-CLOSEDBOX')
            ->set('proofCo', UploadedFile::fake()->image('proof.jpg', 100, 100))
            ->set('isSensitive', false)
            ->call('submit')
            ->assertHasErrors(['boxId']);
    }
}
