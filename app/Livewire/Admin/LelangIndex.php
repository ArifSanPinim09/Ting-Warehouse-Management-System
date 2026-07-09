<?php

namespace App\Livewire\Admin;

use App\Models\Item;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Barang Klaim/Lelang — Revisi §2.9, §4.1
 *
 * Halaman untuk admin mengelola barang yang sudah di-klaim WH atau hold.
 * Admin bisa filter, tandai Dijual/Lelang, dan export ke Excel.
 */
#[Layout('layouts.admin')]
#[Title('Barang Lelang — Ting Warehouse')]
class LelangIndex extends Component
{
    use WithPagination;

    // ─── Filter & Search ────────────────────────────────────────
    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = '';
    #[Url]
    public string $filterCustomer = '';
    #[Url]
    public string $filterDate = '';

    // ─── UI State ───────────────────────────────────────────────
    public ?int $selectedItemId = null;
    public bool $showDetail = false;
    public bool $showMarkConfirm = false;
    public string $pendingMarkStatus = '';

    // ─── Watchers ───────────────────────────────────────────────
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterStatus(): void
    {
        $this->resetPage();
    }

    public function updatedFilterCustomer(): void
    {
        $this->resetPage();
    }

    public function updatedFilterDate(): void
    {
        $this->resetPage();
    }

    // ─── Actions ────────────────────────────────────────────────
    public function selectItem(int $id): void
    {
        $this->selectedItemId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedItemId = null;
    }

    public function confirmMark(string $newStatus): void
    {
        $this->pendingMarkStatus = $newStatus;
        $this->showMarkConfirm = true;
    }

    public function cancelMark(): void
    {
        $this->showMarkConfirm = false;
        $this->pendingMarkStatus = '';
    }

    /**
     * Mark item as Dijual or Lelang.
     */
    public function markItem(AuditLogService $auditService): void
    {
        $item = Item::findOrFail($this->selectedItemId);

        if (!in_array($item->status, [Item::STATUS_KLAIM_WH, Item::STATUS_HOLD, Item::STATUS_DIJUAL, Item::STATUS_LELANG])) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Status barang tidak valid untuk perubahan ini.');
            return;
        }

        $oldStatus = $item->status;
        $item->update(['status' => $this->pendingMarkStatus]);

        $auditService->logCustom(
            $item,
            'lelang_status_changed',
            "Barang '{$item->name}' ditandai sebagai {$this->pendingMarkStatus}"
        );

        $this->showMarkConfirm = false;
        $this->pendingMarkStatus = '';

        $this->dispatch('toast',
            type: 'success',
            title: 'Berhasil',
            message: "Barang berhasil ditandai sebagai {$item->status}."
        );
    }

    /**
     * Export data to Excel (CSV format).
     */
    public function exportExcel(): StreamedResponse
    {
        $items = $this->buildQuery()->get();

        $filename = 'lelang_' . now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($items) {
            $handle = fopen('php://output', 'w');

            // Header row
            fputcsv($handle, [
                'Nama Barang',
                'No Resi',
                'Customer',
                'Box',
                'Berat (kg)',
                'Harga Asli (Yuan)',
                'Status',
                'Tanggal Input',
            ]);

            // Data rows
            foreach ($items as $item) {
                fputcsv($handle, [
                    $item->name,
                    $item->resi_number ?? '-',
                    $item->customer->name ?? '-',
                    $item->box->tracking_number ?? $item->box->batch_name ?? '-',
                    $item->whChinaData->berat ?? 0,
                    $item->price_yuan ?? 0,
                    $item->status,
                    $item->created_at->format('Y-m-d H:i'),
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }

    // ─── Computed Properties ────────────────────────────────────

    /**
     * Build query with filters applied.
     */
    private function buildQuery()
    {
        $query = Item::with(['customer', 'box', 'whChinaData'])
            ->whereIn('status', [
                Item::STATUS_KLAIM_WH,
                Item::STATUS_HOLD,
                Item::STATUS_DIJUAL,
                Item::STATUS_LELANG,
            ]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('resi_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterCustomer) {
            $query->where('customer_id', $this->filterCustomer);
        }

        if ($this->filterDate) {
            $query->whereDate('created_at', $this->filterDate);
        }

        return $query;
    }

    public function getSelectedItemProperty(): ?Item
    {
        if (!$this->selectedItemId) return null;

        return Item::with(['customer', 'box', 'whChinaData'])->find($this->selectedItemId);
    }

    public function getCustomersProperty()
    {
        return User::where('role', 'customer')
            ->where('status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();
    }

    /**
     * Summary statistics.
     */
    public function getSummaryProperty(): array
    {
        $items = Item::whereIn('status', [
            Item::STATUS_KLAIM_WH,
            Item::STATUS_HOLD,
            Item::STATUS_DIJUAL,
            Item::STATUS_LELANG,
        ])->with('whChinaData')->get();

        $totalBarang = $items->count();
        $totalNilai = $items->sum(fn ($item) => ($item->price_yuan ?? 0) * ($item->quantity ?? 1));
        $belumTerjual = $items->whereIn('status', [Item::STATUS_KLAIM_WH, Item::STATUS_HOLD])->count();

        return [
            'total_barang' => $totalBarang,
            'total_nilai' => $totalNilai,
            'belum_terjual' => $belumTerjual,
        ];
    }

    public function render()
    {
        $items = $this->buildQuery()->latest()->paginate(15);

        return view('livewire.admin.lelang.index', [
            'items' => $items,
            'customers' => $this->customers,
            'summary' => $this->summary,
            'selectedItem' => $this->selected_item,
        ]);
    }
}
