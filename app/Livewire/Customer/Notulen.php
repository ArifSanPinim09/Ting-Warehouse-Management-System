<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use App\Models\Item;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Notulen — Sprint 5A
 *
 * Flow Website P47: "Notulen (Setelah dikirim ke INA)*"
 * Perbedaan dengan Resi Belum Dikenali (P46):
 *   - Resi Belum Dikenali: barang belum match data Admin China (unmatched)
 *   - Notulen: barang sudah sampai di INA, box status ARRIVED_INA ke atas
 *
 * Customer bisa lihat barang mereka yang sudah sampai di Indonesia
 * tapi belum di-claim / belum di-invoice.
 */
#[Layout('layouts.app')]
#[Title('Notulen — Ting Warehouse')]
class Notulen extends Component
{
    use WithPagination;

    public string $search = '';

    protected $paginationTheme = 'tailwind';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $notulenStatuses = [
            Box::STATUS_ARRIVED_INA,
            Box::STATUS_REDLINE,
            Box::STATUS_STEVEDORING,
            Box::STATUS_CHECKED_BY_WH,
            Box::STATUS_INVOICE,
            Box::STATUS_DONE,
        ];

        $items = Item::with(['box', 'customer'])
            ->where('customer_id', auth()->id())
            ->whereHas('box', function ($query) use ($notulenStatuses) {
                $query->whereIn('status', $notulenStatuses);
            })
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('resi_number', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->orderByDesc('updated_at')
            ->paginate(10);

        return view('livewire.customer.notulen.index', compact('items'));
    }
}
