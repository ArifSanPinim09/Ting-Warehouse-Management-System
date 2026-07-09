<?php

namespace App\Livewire\Customer;

use App\Models\Item;
use App\Services\NoTuanClaimService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * No Tuan — Revisi §2.1, §4.1, §7.1, §8.1/§8.2/§8.3
 *
 * Customer melihat barang status no_tuan, bisa klaim dengan upload bukti.
 * Race condition safe via DB::transaction + lockForUpdate.
 */
#[Layout('layouts.app')]
#[Title('Barang No Tuan — Ting Warehouse')]
class NoTuanIndex extends Component
{
    use WithFileUploads;

    // ─── Claim Form (§7.1) ─────────────────────────────────────
    public ?int $selectedItemId = null;
    public $proofPembelian = null;
    public string $keterangan = '';

    // ─── UI State ───────────────────────────────────────────────
    public bool $showClaimForm = false;
    public bool $submitting = false;

    protected function rules(): array
    {
        return [
            'selectedItemId' => ['required', 'exists:items,id'],
            'proofPembelian' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'keterangan' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'selectedItemId.required' => 'Barang tidak ditemukan',
            'selectedItemId.exists' => 'Barang tidak ditemukan',
            'proofPembelian.required' => 'Upload bukti pembelian (foto nota/resi)',
            'proofPembelian.mimes' => 'Format foto harus jpg, png',
            'proofPembelian.max' => 'Ukuran foto maksimal 5MB',
        ];
    }

    public function selectItem(int $itemId): void
    {
        $item = Item::find($itemId);

        if (!$item || $item->status !== Item::STATUS_NO_TUAN) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Barang sudah diklaim atau tidak tersedia.');
            return;
        }

        $this->selectedItemId = $itemId;
        $this->proofPembelian = null;
        $this->keterangan = '';
        $this->showClaimForm = true;
    }

    public function cancelClaim(): void
    {
        $this->reset(['selectedItemId', 'proofPembelian', 'keterangan', 'showClaimForm']);
    }

    public function submitClaim(NoTuanClaimService $claimService): void
    {
        $this->validate();
        $this->submitting = true;

        $item = Item::find($this->selectedItemId);

        if (!$item || $item->status !== Item::STATUS_NO_TUAN) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Barang sudah diklaim oleh customer lain.');
            $this->reset(['selectedItemId', 'proofPembelian', 'keterangan', 'showClaimForm']);
            $this->submitting = false;
            return;
        }

        // Upload proof photo
        $proofPath = $this->proofPembelian->store('proof-pembelian', 'public');

        try {
            $claimService->claimItem(
                item: $item,
                customer: auth()->user(),
                proofPath: $proofPath,
                keterangan: $this->keterangan ?: null,
            );

            $this->reset(['selectedItemId', 'proofPembelian', 'keterangan', 'showClaimForm']);
            $this->dispatch('toast',
                type: 'success',
                title: 'Berhasil',
                message: 'Barang berhasil diklaim. Denda Rp 5.000 ditambahkan.',
            );
        } catch (\InvalidArgumentException $e) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: $e->getMessage());
            // Clean up uploaded file on failure
            Storage::disk('public')->delete($proofPath);
        }

        $this->submitting = false;
    }

    public function render()
    {
        $noTuanItems = Item::where('status', Item::STATUS_NO_TUAN)
            ->with(['box', 'customer'])
            ->latest()
            ->paginate(20);

        return view('livewire.customer.no-tuan.index', [
            'noTuanItems' => $noTuanItems,
        ]);
    }
}
