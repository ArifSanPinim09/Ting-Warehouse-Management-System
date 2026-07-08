<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Recap — Ting Warehouse')]
class RecapIndex extends Component
{
    #[Url]
    public string $search = '';
    #[Url]
    public string $filterType = '';
    #[Url]
    public string $filterMethod = '';
    #[Url]
    public string $filterDateFrom = '';
    #[Url]
    public string $filterDateTo = '';

    // ─── Summary Stats ──────────────────────────────────────────
    public int $totalBoxes = 0;
    public int $totalItems = 0;
    public int $totalInvoices = 0;
    public float $totalRevenue = 0;
    public int $totalCheckouts = 0;
    public int $totalComplaints = 0;

    public function mount(): void
    {
        $this->loadStats();
    }

    public function updatedFilterType(): void { $this->loadStats(); }
    public function updatedFilterMethod(): void { $this->loadStats(); }
    public function updatedFilterDateFrom(): void { $this->loadStats(); }
    public function updatedFilterDateTo(): void { $this->loadStats(); }

    public function loadStats(): void
    {
        $boxQuery = Box::query();
        $invoiceQuery = Invoice::query();

        if ($this->filterType) {
            $boxQuery->where('type', $this->filterType);
        }
        if ($this->filterMethod) {
            $boxQuery->where('method', $this->filterMethod);
        }
        if ($this->filterDateFrom) {
            $boxQuery->whereDate('created_at', '>=', $this->filterDateFrom);
            $invoiceQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $boxQuery->whereDate('created_at', '<=', $this->filterDateTo);
            $invoiceQuery->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $this->totalBoxes = $boxQuery->count();
        $this->totalItems = Item::whereHas('box', function ($q) {
            if ($this->filterType) $q->where('type', $this->filterType);
            if ($this->filterMethod) $q->where('method', $this->filterMethod);
            if ($this->filterDateFrom) $q->whereDate('created_at', '>=', $this->filterDateFrom);
            if ($this->filterDateTo) $q->whereDate('created_at', '<=', $this->filterDateTo);
        })->count();

        $this->totalInvoices = $invoiceQuery->count();
        $this->totalRevenue = (float) $invoiceQuery->sum('grand_total');
        $this->totalCheckouts = Checkout::whereHas('invoice', function ($q) {
            if ($this->filterDateFrom) $q->whereDate('created_at', '>=', $this->filterDateFrom);
            if ($this->filterDateTo) $q->whereDate('created_at', '<=', $this->filterDateTo);
        })->count();
        $this->totalComplaints = Complain::when($this->filterDateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->filterDateFrom))
            ->when($this->filterDateTo, fn($q) => $q->whereDate('created_at', '<=', $this->filterDateTo))
            ->count();
    }

    public function render()
    {
        $query = Box::with('customer')
            ->withCount('items');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tracking_number', 'like', "%{$this->search}%")
                  ->orWhere('batch_name', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
        }
        if ($this->filterMethod) {
            $query->where('method', $this->filterMethod);
        }
        if ($this->filterDateFrom) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $boxes = $query->latest()->paginate(20);

        return view('livewire.admin.recap.index', [
            'boxes' => $boxes,
        ]);
    }
}
