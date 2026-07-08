<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * My Box — Direct view (§4.3, §8.6)
 *
 * Box tipe direct milik customer, per batch.
 */
class BoxDirect extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
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
                $query->where('customer_id', $user->id);
            }])
            ->withCount(['items' => function ($query) use ($user) {
                $query->where('customer_id', $user->id);
            }])
            ->latest()
            ->paginate(10);

        return view('livewire.customer.boxes.direct', compact('boxes'))
            ->layout('layouts.app');
    }
}
