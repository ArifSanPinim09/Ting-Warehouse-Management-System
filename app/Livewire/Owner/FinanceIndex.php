<?php

namespace App\Livewire\Owner;

use App\Models\Invoice;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Laporan Keuangan — Ting Warehouse')]
class FinanceIndex extends Component
{
    use WithPagination;

    // ─── Filters ─────────────────────────────────────────────────
    #[Url]
    public string $filterDateFrom = '';
    #[Url]
    public string $filterDateTo = '';
    #[Url]
    public string $filterMonth = '';
    #[Url]
    public string $filterYear = '';
    #[Url]
    public string $filterCustomer = '';
    #[Url]
    public string $filterStatus = '';
    #[Url]
    public string $search = '';
    #[Url]
    public int $perPage = 15;

    // ─── Summary Stats ───────────────────────────────────────────
    public float $totalRevenue = 0;
    public float $totalOutstanding = 0;
    public float $totalProfit = 0;
    public float $cashIn = 0;
    public float $cashOut = 0;
    public int $totalInvoiceCount = 0;

    // ─── Export State ────────────────────────────────────────────
    public bool $showExportConfirm = false;
    public string $exportType = '';

    // ─── REV-05.7: Edit Tax State ─────────────────────────────
    public ?int $editInvoiceId = null;
    public string $editFeeTax = '';
    public bool $showEditTax = false;

    public function openEditTax(int $invoiceId): void
    {
        $invoice = Invoice::find($invoiceId);
        if (!$invoice) return;

        $this->editInvoiceId = $invoiceId;
        $this->editFeeTax = (string) $invoice->fee_tax;
        $this->showEditTax = true;
    }

    public function closeEditTax(): void
    {
        $this->editInvoiceId = null;
        $this->editFeeTax = '';
        $this->showEditTax = false;
    }

    public function saveTax(): void
    {
        $this->validate([
            'editFeeTax' => 'required|numeric|min:0',
        ]);

        $invoice = Invoice::find($this->editInvoiceId);
        if (!$invoice) return;

        $oldTax = $invoice->fee_tax;
        $oldGrandTotal = (float) $invoice->grand_total;
        $newTax = (float) $this->editFeeTax;

        // Recalculate grand_total
        $newGrandTotal = $newTax + (float) $invoice->fee_wh + (float) $invoice->fee_packing
            + (float) $invoice->add_on + (float) $invoice->denda_total;

        $invoice->update([
            'fee_tax' => $newTax,
            'grand_total' => $newGrandTotal,
        ]);

        // Audit log
        \App\Models\ActivityLog::create([
            'user_id' => auth()->id(),
            'subject_type' => 'App\\Models\\Invoice',
            'subject_id' => $invoice->id,
            'event' => 'updated',
            'old_values' => ['fee_tax' => $oldTax, 'grand_total' => $oldGrandTotal],
            'new_values' => ['fee_tax' => $newTax, 'grand_total' => $newGrandTotal],
        ]);

        $this->closeEditTax();

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Fee Tax {$invoice->invoice_number} berhasil diupdate.");
    }

    public function resetFilters(): void
    {
        $this->reset([
            'filterDateFrom', 'filterDateTo', 'filterMonth',
            'filterYear', 'filterCustomer', 'filterStatus', 'search',
        ]);
        $this->resetPage();
    }

    public function updatedFilterDateFrom(): void { $this->resetPage(); }
    public function updatedFilterDateTo(): void { $this->resetPage(); }
    public function updatedFilterMonth(): void { $this->resetPage(); }
    public function updatedFilterYear(): void { $this->resetPage(); }
    public function updatedFilterCustomer(): void { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }
    public function updatedSearch(): void { $this->resetPage(); }

    public function confirmExport(string $type): void
    {
        $this->exportType = $type;
        $this->showExportConfirm = true;
    }

    public function cancelExport(): void
    {
        $this->showExportConfirm = false;
        $this->exportType = '';
    }

    /**
     * Get the export URL with current filters applied.
     */
    public function getExportUrlProperty(): string
    {
        return route('owner.export-finance', array_filter([
            'type' => $this->exportType,
            'search' => $this->search,
            'date_from' => $this->filterDateFrom,
            'date_to' => $this->filterDateTo,
            'month' => $this->filterMonth,
            'year' => $this->filterYear,
            'customer' => $this->filterCustomer,
            'status' => $this->filterStatus,
        ]));
    }

    private function buildQuery()
    {
        $query = Invoice::with(['customer:id,name,email', 'box:id,tracking_number,batch_name,type,method']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterDateFrom) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        if ($this->filterMonth) {
            $query->whereMonth('created_at', $this->filterMonth);
        }

        if ($this->filterYear) {
            $query->whereYear('created_at', $this->filterYear);
        }

        if ($this->filterCustomer) {
            $query->where('customer_id', $this->filterCustomer);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return $query;
    }

    public function render()
    {
        $query = $this->buildQuery();

        // Summary stats — 1 query with conditional aggregation instead of 3
        $summary = (clone $query)->selectRaw("
            SUM(CASE WHEN status = 'verified' THEN grand_total ELSE 0 END) as total_revenue,
            SUM(CASE WHEN status IN ('waiting_payment', 'waiting_verification') THEN grand_total ELSE 0 END) as total_outstanding,
            COUNT(*) as total_count
        ")->first();

        $this->totalRevenue = (float) ($summary->total_revenue ?? 0);
        $this->totalOutstanding = (float) ($summary->total_outstanding ?? 0);
        $this->cashIn = $this->totalRevenue;
        $this->cashOut = 0;
        $this->totalProfit = $this->cashIn - $this->cashOut;
        $this->totalInvoiceCount = (int) ($summary->total_count ?? 0);

        // Sprint 4: Shipping/Material/Goods fees summary (in Rupiah)
        $kurs = (float) \App\Models\Setting::getValue('kurs_yuan_idr', 2460);
        $shippingMaterialTotal = \App\Models\ShippingMaterialFee::sum('biaya_yuan');
        $goodsWeightTotal = \App\Models\GoodsWeightFee::sum('biaya_yuan');
        $totalFeesYuan = $shippingMaterialTotal + $goodsWeightTotal;
        $totalFeesRupiah = $totalFeesYuan * $kurs;

        $invoices = $query->latest()->paginate($this->perPage);

        $customers = User::where('role', 'customer')
            ->orderBy('name')
            ->get(['id', 'name']);

        // Get distinct years (DB-agnostic — extract year in PHP)
        $years = Invoice::select('created_at')
            ->distinct()
            ->pluck('created_at')
            ->map(fn ($d) => $d->format('Y'))
            ->unique()
            ->sortDesc()
            ->values();

        return view('livewire.owner.finance.index', [
            'invoices' => $invoices,
            'customers' => $customers,
            'years' => $years,
            'shippingMaterialTotalYuan' => $shippingMaterialTotal,
            'goodsWeightTotalYuan' => $goodsWeightTotal,
            'totalFeesYuan' => $totalFeesYuan,
            'totalFeesRupiah' => $totalFeesRupiah,
            'kursYuan' => $kurs,
        ]);
    }
}
