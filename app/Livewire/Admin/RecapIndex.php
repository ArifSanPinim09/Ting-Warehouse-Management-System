<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\WhChinaData;
use App\Services\RecapMatchingService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Recap page — Revisi §2.7.3: dual-source (Customer + WH China).
 *
 * Panel 1: Data Customer (from items / setor resi)
 * Panel 2: Data WH China (admin input)
 * Auto-matching by exact resi_number.
 */
#[Layout('layouts.admin')]
#[Title('Recap — Ting Warehouse')]
class RecapIndex extends Component
{
    use WithFileUploads;
    use WithPagination;

    // ─── Filters (shared) ──────────────────────────────────────
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

    // ─── Tab State ─────────────────────────────────────────────
    #[Url]
    public string $activeTab = 'customer';

    // ─── Summary Stats ─────────────────────────────────────────
    public int $totalBoxes = 0;
    public int $totalItems = 0;
    public int $totalInvoices = 0;
    public float $totalRevenue = 0;
    public int $totalCheckouts = 0;
    public int $totalComplaints = 0;
    public int $totalWhChina = 0;
    public int $totalMatched = 0;
    public int $totalUnmatched = 0;

    // ─── WH China Modal State ───────────────────────────────────
    public bool $showWhModal = false;

    // ─── WH China Form (§7.4) ──────────────────────────────────
    public string $resiNumber = '';
    public string $berat = '';
    public string $ukuranBox = '';
    public string $hurufBox = '';
    public string $biayaJasa = '';
    public $fotoBarang = null;
    public $fotoArrivedChina = null;
    public $fotoArrivedIna = null;
    public string $tanggalSetor = '';
    public ?int $editingWhId = null;

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
        if ($this->filterType) $boxQuery->where('type', $this->filterType);
        if ($this->filterMethod) $boxQuery->where('method', $this->filterMethod);
        if ($this->filterDateFrom) $boxQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        if ($this->filterDateTo) $boxQuery->whereDate('created_at', '<=', $this->filterDateTo);

        $this->totalBoxes = $boxQuery->count();

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

        $invoiceQuery = Invoice::query();
        if ($this->filterDateFrom) $invoiceQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        if ($this->filterDateTo) $invoiceQuery->whereDate('created_at', '<=', $this->filterDateTo);

        $invoiceStats = $invoiceQuery->selectRaw('COUNT(*) as total, COALESCE(SUM(grand_total), 0) as revenue')->first();
        $this->totalInvoices = (int) $invoiceStats->total;
        $this->totalRevenue = (float) $invoiceStats->revenue;

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

        // WH China stats
        $this->totalWhChina = WhChinaData::count();
        $this->totalMatched = WhChinaData::whereNotNull('item_id')->count();
        $this->totalUnmatched = WhChinaData::whereNull('item_id')->count();
    }

    // ─── Tab Switch ────────────────────────────────────────────

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    // ─── WH China Modal ────────────────────────────────────────

    public function openWhModal(): void
    {
        $this->resetForm();
        $this->showWhModal = true;
    }

    public function closeWhModal(): void
    {
        $this->resetForm();
        $this->showWhModal = false;
    }

    // ─── WH China CRUD ─────────────────────────────────────────

    public function submitWhChinaData(RecapMatchingService $matching): void
    {
        $this->validate([
            'resiNumber' => 'required|string|max:100',
            'berat' => 'nullable|numeric|min:0.01',
            'ukuranBox' => 'nullable|string|max:100',
            'hurufBox' => 'nullable|string|max:10',
            'biayaJasa' => 'required|numeric|min:0',
            'fotoBarang' => 'required_if:editingWhId,null|nullable|image|max:5120',
            'fotoArrivedChina' => 'nullable|image|max:5120',
            'fotoArrivedIna' => 'nullable|image|max:5120',
            'tanggalSetor' => 'nullable|date',
        ], [
            'resiNumber.required' => 'Resi number is required',
            'berat.numeric' => 'Weight must be a number',
            'berat.min' => 'Weight must be at least 0.01 kg',
            'hurufBox.max' => 'Huruf box max 10 characters',
            'biayaJasa.required' => 'Service fee is required',
            'biayaJasa.numeric' => 'Service fee must be a number',
            'fotoBarang.required_if' => 'Photo is required',
            'fotoBarang.image' => 'File must be an image',
            'fotoBarang.max' => 'Photo max 5MB',
            'fotoArrivedChina.image' => 'File must be an image',
            'fotoArrivedChina.max' => 'Photo max 5MB',
            'fotoArrivedIna.image' => 'File must be an image',
            'fotoArrivedIna.max' => 'Photo max 5MB',
        ]);

        $fotoPath = null;
        if ($this->fotoBarang) {
            $fotoPath = $this->fotoBarang->store('wh-china-photos', 'public');
        }

        $fotoChinaPath = null;
        if ($this->fotoArrivedChina) {
            $fotoChinaPath = $this->fotoArrivedChina->store('wh-arrived-china', 'public');
        }

        $fotoInaPath = null;
        if ($this->fotoArrivedIna) {
            $fotoInaPath = $this->fotoArrivedIna->store('wh-arrived-ina', 'public');
        }

        if ($this->editingWhId) {
            $whData = WhChinaData::findOrFail($this->editingWhId);

            // If resi changed, clear existing match
            if ($whData->resi_number !== $this->resiNumber && $whData->item_id) {
                $whData->item_id = null;
                $whData->matched_at = null;
            }

            // Delete old photos if replaced
            if ($fotoPath && $whData->foto_barang) {
                Storage::disk('public')->delete($whData->foto_barang);
            }
            if ($fotoChinaPath && $whData->foto_arrived_china) {
                Storage::disk('public')->delete($whData->foto_arrived_china);
            }
            if ($fotoInaPath && $whData->foto_arrived_ina) {
                Storage::disk('public')->delete($whData->foto_arrived_ina);
            }

            $whData->update([
                'resi_number' => $this->resiNumber,
                'berat' => (float) $this->berat,
                'ukuran_box' => $this->ukuranBox,
                'huruf_box' => $this->hurufBox ?: null,
                'biaya_jasa' => $this->biayaJasa !== '' ? (float) $this->biayaJasa : null,
                'foto_barang' => $fotoPath ?? $whData->foto_barang,
                'foto_arrived_china' => $fotoChinaPath ?? $whData->foto_arrived_china,
                'foto_arrived_ina' => $fotoInaPath ?? $whData->foto_arrived_ina,
                'tanggal_setor' => $this->tanggalSetor ?: $whData->tanggal_setor,
            ]);

            $matching->matchByResi($whData);
        } else {
            $whData = WhChinaData::create([
                'resi_number' => $this->resiNumber,
                'berat' => (float) $this->berat,
                'ukuran_box' => $this->ukuranBox,
                'huruf_box' => $this->hurufBox ?: null,
                'biaya_jasa' => $this->biayaJasa !== '' ? (float) $this->biayaJasa : null,
                'foto_barang' => $fotoPath,
                'foto_arrived_china' => $fotoChinaPath,
                'foto_arrived_ina' => $fotoInaPath,
                'tanggal_setor' => $this->tanggalSetor ?: null,
                'input_by' => auth()->id(),
            ]);

            $matching->matchByResi($whData);
        }

        $this->showWhModal = false;
        $this->resetForm();
        $this->loadStats();

        $this->dispatch('toast',
            type: 'success',
            title: 'Berhasil',
            message: 'Data WH China berhasil diinput.',
        );
    }

    public function editWhChinaData(int $id): void
    {
        $whData = WhChinaData::findOrFail($id);
        $this->editingWhId = $whData->id;
        $this->resiNumber = $whData->resi_number;
        $this->berat = (string) $whData->berat;
        $this->ukuranBox = $whData->ukuran_box;
        $this->hurufBox = $whData->huruf_box ?? '';
        $this->biayaJasa = $whData->biaya_jasa !== null ? (string) $whData->biaya_jasa : '';
        $this->tanggalSetor = $whData->tanggal_setor ? $whData->tanggal_setor->format('Y-m-d') : '';
        $this->showWhModal = true;
    }

    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    public function deleteWhChinaData(int $id): void
    {
        $whData = WhChinaData::findOrFail($id);

        if ($whData->foto_barang) {
            Storage::disk('public')->delete($whData->foto_barang);
        }

        $whData->delete();
        $this->loadStats();

        $this->dispatch('toast',
            type: 'success',
            title: 'Berhasil',
            message: 'Data WH China berhasil dihapus.',
        );
    }

    public function runAutoMatch(RecapMatchingService $matching): void
    {
        $count = $matching->tryMatchAll();
        $this->loadStats();

        $this->dispatch('toast',
            type: $count > 0 ? 'success' : 'info',
            title: 'Auto Match',
            message: $count > 0
                ? "{$count} data berhasil di-match."
                : 'Tidak ada data baru yang cocok.',
        );
    }

    private function resetForm(): void
    {
        $this->resiNumber = '';
        $this->berat = '';
        $this->ukuranBox = '';
        $this->hurufBox = '';
        $this->biayaJasa = '';
        $this->fotoBarang = null;
        $this->fotoArrivedChina = null;
        $this->fotoArrivedIna = null;
        $this->tanggalSetor = '';
        $this->editingWhId = null;
    }

    public function render()
    {
        // Customer data: items with box + customer
        $customerQuery = Item::with(['box', 'customer', 'whChinaData'])
            ->whereNotNull('resi_number');

        if ($this->search) {
            $customerQuery->where(function ($q) {
                $q->where('resi_number', 'like', "%{$this->search}%")
                  ->orWhere('name', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterType || $this->filterMethod || $this->filterDateFrom || $this->filterDateTo) {
            $customerQuery->whereHas('box', function ($q) {
                if ($this->filterType) $q->where('type', $this->filterType);
                if ($this->filterMethod) $q->where('method', $this->filterMethod);
                if ($this->filterDateFrom) $q->whereDate('created_at', '>=', $this->filterDateFrom);
                if ($this->filterDateTo) $q->whereDate('created_at', '<=', $this->filterDateTo);
            });
        }

        $customerItems = $customerQuery->latest()->paginate(20, ['*'], 'customer_page');

        // WH China data
        $whChinaQuery = WhChinaData::with(['item', 'admin']);

        if ($this->search) {
            $whChinaQuery->where('resi_number', 'like', "%{$this->search}%");
        }
        if ($this->filterDateFrom) {
            $whChinaQuery->whereDate('created_at', '>=', $this->filterDateFrom);
        }
        if ($this->filterDateTo) {
            $whChinaQuery->whereDate('created_at', '<=', $this->filterDateTo);
        }

        $whChinaData = $whChinaQuery->latest()->paginate(20, ['*'], 'wh_page');

        return view('livewire.admin.recap.index', [
            'customerItems' => $customerItems,
            'whChinaData' => $whChinaData,
        ]);
    }
}
