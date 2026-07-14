<?php

namespace App\Livewire\WhChina;

use App\Models\Item;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wh-china')]
#[Title('Requests — WH China')]
class Requests extends Component
{
    use WithPagination;

    public string $filterStatus = '';

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $requests = Item::query()
            ->whereNotNull('request_type')
            ->where('request_type', '!=', '')
            ->when($this->filterStatus, function ($query) {
                if ($this->filterStatus === 'pending') {
                    $query->whereNull('request_completed_at');
                } elseif ($this->filterStatus === 'completed') {
                    $query->whereNotNull('request_completed_at');
                }
            })
            ->with(['customer' => function ($q) {
                $q->select('id', 'name');
            }])
            ->latest()
            ->paginate(20);

        return view('livewire.wh-china.requests.index', compact('requests'));
    }
}
