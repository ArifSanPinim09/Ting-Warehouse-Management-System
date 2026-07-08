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
        // Box stats — 1 query with conditional aggregation
        $boxQuery = Box::query();
        if ($this->filterType) $boxQuery->where('type', $this->filterType);
        if ($this->filterMethod) $boxQuery->where('method', $this->filterMethod);
        if ($this->filterDateFrom) $boxQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        if ($this->filterDateTo) $boxQuery->whereDate('created_at', '<=', $this->filterDateTo);

        $this->totalBoxes = $boxQuery->count();

        // Items count — use box filters via subquery
        $itemQuery = Item::query();
        if ($this->filterType || $this->filterMethod || $this->filterDateFrom || $this->filterDateTo) {
            $itemQuery->whereHas('box', function ($q) {
                if ($this->filterType) $q->where('type', $this->filterType);
                if ($this->filterMethod) $q->where('method', $this->filterMethod);
                if ($this->filterDateFrom) $q->whereDate('created_at', '>=', $this->filterDateFrom);
                if ($this->filterDateTo) $q->whereDate('created_at', '<=', $this->filterDateTo);
            });
        }
        $this->totalItems = $itemQuery->count();

        // Invoice stats — 1 query with conditional aggregation
        $invoiceQuery = Invoice::query();
        if ($this->filterDateFrom) $invoiceQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        if ($this->filterDateTo) $invoiceQuery->whereDate('created_at', '<=', $this->filterDateTo);

        $invoiceStats = $invoiceQuery->selectRaw('COUNT(*) as total, COALESCE(SUM(grand_total), 0) as revenue')->first();
        $this->totalInvoices = (int) $invoiceStats->total;
        $this->totalRevenue = (float) $invoiceStats->revenue;

        // Checkouts + Complaints — 1 query each
        $checkoutQuery = Checkout::query();
        $complaintQuery = Complain::query();

        if ($this->filterDateFrom) {
            $checkoutQuery->whereDate('created_at', '>=', $this->filterDateFrom);
            $complaintQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $checkoutQuery->whereDate('created_at', '<=', $this->filterDateTo);
            $complaintQuery->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $this->totalCheckouts = $checkoutQuery->count();
        $this->totalComplaints = $complaintQuery->count();
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
