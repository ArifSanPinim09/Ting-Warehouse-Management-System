<?php

namespace App\Livewire\WhChina;

use App\Models\Box;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wh-china')]
#[Title('History — Ting Warehouse')]
class History extends Component
{
    use WithPagination;

    public string $searchBatch = '';
    public string $filterMethod = '';
    public string $filterStatus = '';
    public string $dateFrom = '';
    public string $dateTo = '';

    public function updating($property): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = Box::with(['customer', 'items'])
            ->whereNotIn('status', [Box::STATUS_OPEN, Box::STATUS_LAST_CLAIM, Box::STATUS_CLOSED])
            ->when($this->searchBatch, fn($q) => $q->where('batch_name', 'like', "%{$this->searchBatch}%"))
            ->when($this->filterMethod, fn($q) => $q->where('method', $this->filterMethod))
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->dateFrom, fn($q) => $q->whereDate('updated_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn($q) => $q->whereDate('updated_at', '<=', $this->dateTo))
            ->orderByDesc('updated_at');

        return view('livewire.wh-china.history.index', [
            'boxes' => $query->paginate(15),
            'statusOptions' => [
                Box::STATUS_REQUEST_TO_SEND => 'Request to Send',
                Box::STATUS_SEND_TO_CARGO => 'Send to Cargo',
                Box::STATUS_ARRIVED_AT_CARGO => 'Arrived at Cargo',
                Box::STATUS_WAITING_FOR_DEPARTURE => 'Waiting for Departure',
                Box::STATUS_DEPARTURE => 'Departure',
                Box::STATUS_ARRIVED_INA => 'Arrived INA',
                Box::STATUS_REDLINE => 'Redline',
                Box::STATUS_STEVEDORING => 'Stevedoring',
                Box::STATUS_CHECKED_BY_WH => 'Checked by WH',
                Box::STATUS_INVOICE => 'Invoice',
                Box::STATUS_DONE => 'Done',
            ],
        ]);
    }
}
