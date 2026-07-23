<?php

namespace App\Livewire\Customer;

use App\Models\Item;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Mesin Pencari Resi — Sprint 3
 * 
 * Client doc: "Mesin pencari nomor resi (statusnya keluar: 
 * 140 AIR/notuan/belum di klaim/Lelang/Hub Admin)"
 */
class ResiSearch extends Component
{
    public string $searchQuery = '';
    public ?array $result = null;
    public bool $searched = false;

    public function search(): void
    {
        $this->searched = true;

        if (strlen($this->searchQuery) < 3) {
            $this->result = null;
            return;
        }

        // Search in items by resi_number
        $item = Item::with(['box', 'customer', 'whChinaData'])
            ->where('resi_number', 'like', "%{$this->searchQuery}%")
            ->where('customer_id', auth()->id()) // Only customer's own items
            ->first();

        if (!$item) {
            $this->result = null;
            return;
        }

        // Determine display status
        $statusDisplay = $this->getDisplayStatus($item);

        $this->result = [
            'item_id' => $item->id,
            'resi_number' => $item->resi_number,
            'name' => $item->name,
            'quantity' => $item->quantity,
            'customer_name' => $item->customer?->name ?? '-',
            'is_sensitive' => $item->is_sensitive,
            'is_garment' => $item->is_garment,
            'request_note' => $item->notes ?? null,
            'add_on' => $item->add_on ?? null,
            'box_name' => $item->box?->display_name ?? '-',
            'box_status' => $item->box?->status ?? '-',
            'box_method' => $item->box?->method ?? '-',
            'box_number' => $item->box?->huruf_box ?? '-',
            'status_display' => $statusDisplay,
            'foto_china' => $item->whChinaData?->foto_arrived_china 
                ? \Illuminate\Support\Facades\Storage::url($item->whChinaData->foto_arrived_china) 
                : null,
            'foto_co' => $item->proof_co
                ? \Illuminate\Support\Facades\Storage::url($item->proof_co) 
                : null,
        ];
    }

    /**
     * Get human-readable status for display.
     * Client doc: "140 AIR/notuan/belum di klaim/Lelang/Hub Admin"
     */
    private function getDisplayStatus(Item $item): string
    {
        // Check item status first
        return match($item->status) {
            Item::STATUS_ACTIVE => $item->box 
                ? strtoupper($item->box->method) . ' — ' . $item->box->getStatusLabelAttribute()
                : 'Active',
            Item::STATUS_NO_TUAN => 'Notuan',
            Item::STATUS_CLAIMED => 'Claimed',
            Item::STATUS_KLAIM_WH => 'Belum di Klaim',
            Item::STATUS_HOLD => 'Hold — Hub Admin',
            Item::STATUS_DIJUAL => 'Terjual',
            Item::STATUS_LELANG => 'Lelang',
            Item::STATUS_SHIPPED => 'Shipped',
            default => ucfirst($item->status),
        };
    }

    public function render()
    {
        return view('livewire.customer.resi-search.index')
            ->layout('layouts.app');
    }
}
