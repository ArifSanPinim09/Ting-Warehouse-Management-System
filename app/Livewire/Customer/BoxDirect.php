<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * My Box — Direct view (§4.3, §8.6)
 *
 * Box tipe direct milik customer, per batch.
 * Table view with detail modal on row click.
 */
class BoxDirect extends Component
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

        $boxes = Box::where('customer_id', $user->id)
            ->where('type', 'direct')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('tracking_number', 'like', "%{$this->search}%")
                      ->orWhere('batch_name', 'like', "%{$this->search}%");
                });
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

        // Detail box (only if belongs to customer)
        $detailBox = null;
        if ($this->detailBoxId) {
            $detailBox = Box::where('id', $this->detailBoxId)
                ->where('customer_id', $user->id)
                ->where('type', 'direct')
                ->with(['items' => function ($query) use ($user) {
                    $query->where('customer_id', $user->id)->with('whChinaData');
                }])
                ->first();
        }

        return view('livewire.customer.boxes.direct', compact('boxes', 'detailBox'))
            ->layout('layouts.app');
    }
}
