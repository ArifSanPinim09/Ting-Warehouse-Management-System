<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * My Box — Sharing view (§4.3, §8.5)
 *
 * Box tipe sharing + barang milik customer.
 * Table view with detail modal on row click.
 */
class BoxSharing extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // ─── Detail Modal State ─────────────────────────────────────
    public ?int $detailBoxId = null;
    public bool $showDetail = false;

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openBoxDetail(int $boxId): void
    {
        $this->detailBoxId = $boxId;
        $this->showDetail = true;
    }

    public function closeBoxDetail(): void
    {
        $this->showDetail = false;
        $this->detailBoxId = null;
    }

    public function render()
    {
        $user = auth()->user();

        $boxes = Box::where('type', 'sharing')
            ->where(function ($query) use ($user) {
                // REV-01.3: Hanya sharing box dimana customer punya item
                // (1) customer_id NULL + customer punya item di box ini
                $query->where(function ($q) use ($user) {
                    $q->whereNull('customer_id')
                        ->whereHas('items', function ($items) use ($user) {
                            $items->where('customer_id', $user->id);
                        });
                })
                // (2) sharing box yang memang milik customer ini
                ->orWhere('customer_id', $user->id);
            })
            ->when($this->search, function ($query) {
                $query->where('tracking_number', 'like', "%{$this->search}%");
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->with(['items' => function ($query) use ($user) {
                $query->where('customer_id', $user->id)->with('whChinaData');
            }])
            ->withCount(['items' => function ($query) use ($user) {
                $query->where('customer_id', $user->id);
            }])
            ->latest()
            ->paginate(15);

        // REV-01.3: Detail box - hanya sharing box yang ada barang customer
        $detailBox = null;
        if ($this->detailBoxId) {
            $detailBox = Box::where('id', $this->detailBoxId)
                ->where('type', 'sharing')
                ->where(function ($query) use ($user) {
                    // customer_id NULL + customer punya item
                    $query->where(function ($q) use ($user) {
                        $q->whereNull('customer_id')
                            ->whereHas('items', function ($items) use ($user) {
                                $items->where('customer_id', $user->id);
                            });
                    })
                    // atau sharing box milik customer ini
                    ->orWhere('customer_id', $user->id);
                })
                ->with(['items' => function ($query) use ($user) {
                    $query->where('customer_id', $user->id)->with('whChinaData');
                }])
                ->first();
        }

        return view('livewire.customer.boxes.sharing', compact('boxes', 'detailBox'))
            ->layout('layouts.app');
    }
}
