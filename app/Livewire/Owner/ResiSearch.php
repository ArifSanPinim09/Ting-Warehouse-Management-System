<?php

namespace App\Livewire\Owner;

use App\Models\Item;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

/**
 * Owner Mesin Pencari Resi — Sprint 5A
 *
 * Client doc: "Mau mesin pencari resi kayak customer."
 * Owner bisa cari resi semua customer.
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

        $item = Item::with(['box', 'customer', 'whChinaData'])
            ->where('resi_number', 'like', "%{$this->searchQuery}%")
            ->first();

        if (!$item) {
            $this->result = null;
            return;
        }

        $statusDisplay = $this->getDisplayStatus($item);

        $this->result = [
            'item_id' => $item->id,
            'resi_number' => $item->resi_number,
            'name' => $item->name,
            'quantity' => $item->quantity,
            'is_sensitive' => $item->is_sensitive,
            'is_garment' => $item->is_garment,
            'customer_name' => $item->customer?->name ?? '-',
            'customer_code' => $item->customer?->customer_code ?? '-',
            'box_name' => $item->box?->display_name ?? '-',
            'box_code' => $item->box?->box_code ?? '-',
            'box_status' => $item->box?->status ?? '-',
            'box_method' => $item->box?->method ?? '-',
            'status_display' => $statusDisplay,
            'foto_china' => $item->whChinaData?->foto_arrived_china
                ? \Illuminate\Support\Facades\Storage::url($item->whChinaData->foto_arrived_china)
                : null,
        ];
    }

    private function getDisplayStatus(Item $item): string
    {
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
        return view('livewire.owner.resi-search.index')
            ->layout('layouts.admin');
    }
}
