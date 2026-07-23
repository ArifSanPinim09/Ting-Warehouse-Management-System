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

    // ─── Sprint 5B: Finance Transaction CRUD ───────────────────
    public bool $showTrxModal = false;
    public ?int $editTrxId = null;
    public string $trxCategory = 'operasional';
    public string $trxDescription = '';
    public string $trxAmount = '';
    public string $trxDate = '';
    public string $trxNotes = '';

    public function openTrxModal(): void
    {
        $this->resetTrxForm();
        $this->trxDate = now()->format('Y-m-d');
        $this->showTrxModal = true;
    }

    public function closeTrxModal(): void
    {
        $this->showTrxModal = false;
        $this->resetTrxForm();
    }

    public function editTrx(int $id): void
    {
        $trx = \App\Models\FinanceTransaction::findOrFail($id);
        $this->editTrxId = $id;
        $this->trxCategory = $trx->category;
        $this->trxDescription = $trx->description;
        $this->trxAmount = (string) $trx->amount;
        $this->trxDate = $trx->transaction_date->format('Y-m-d');
        $this->trxNotes = $trx->notes ?? '';
        $this->showTrxModal = true;
    }

    public function saveTrx(): void
    {
        $this->validate([
            'trxCategory' => 'required|in:operasional,refund,pemasukan_lain',
            'trxDescription' => 'required|string|max:255',
            'trxAmount' => 'required|numeric|min:0.01',
            'trxDate' => 'required|date',
            'trxNotes' => 'nullable|string|max:1000',
        ], [
            'trxDescription.required' => 'Deskripsi wajib diisi.',
            'trxAmount.required' => 'Jumlah wajib diisi.',
            'trxAmount.numeric' => 'Jumlah harus berupa angka.',
            'trxDate.required' => 'Tanggal wajib diisi.',
        ]);

        $data = [
            'category' => $this->trxCategory,
            'description' => $this->trxDescription,
            'amount' => (float) $this->trxAmount,
            'transaction_date' => $this->trxDate,
            'input_by' => auth()->id(),
            'notes' => $this->trxNotes ?: null,
        ];

        if ($this->editTrxId) {
            \App\Models\FinanceTransaction::find($this->editTrxId)->update($data);
            $msg = 'Transaksi berhasil diupdate.';
        } else {
            \App\Models\FinanceTransaction::create($data);
            $msg = 'Transaksi berhasil ditambahkan.';
        }

        $this->closeTrxModal();
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: $msg);
    }

    public function deleteTrx(int $id): void
    {
        \App\Models\FinanceTransaction::findOrFail($id)->delete();
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Transaksi berhasil dihapus.');
    }

    private function resetTrxForm(): void
    {
        $this->editTrxId = null;
        $this->trxCategory = 'operasional';
        $this->trxDescription = '';
        $this->trxAmount = '';
        $this->trxDate = '';
        $this->trxNotes = '';
    }

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

        // Sprint 5B: Finance transactions by category
        $operasionalTotal = \App\Models\FinanceTransaction::where('category', 'operasional')->sum('amount');
        $refundTotal = \App\Models\FinanceTransaction::where('category', 'refund')->sum('amount');
        $pemasukanLainTotal = \App\Models\FinanceTransaction::where('category', 'pemasukan_lain')->sum('amount');

        // Total pengeluaran = China fees + Box fees + Operasional + Refund
        $this->cashOut = $totalFeesRupiah + $operasionalTotal + $refundTotal;
        // Total pemasukan = Invoice revenue + Pemasukan lain
        $this->cashIn = $this->totalRevenue + $pemasukanLainTotal;
        $this->totalProfit = $this->cashIn - $this->cashOut;

        $recentTransactions = \App\Models\FinanceTransaction::with('inputBy')
            ->latest()
            ->limit(10)
            ->get();

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
            'operasionalTotal' => $operasionalTotal,
            'refundTotal' => $refundTotal,
            'pemasukanLainTotal' => $pemasukanLainTotal,
            'recentTransactions' => $recentTransactions,
        ]);
    }
}
