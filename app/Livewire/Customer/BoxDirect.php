<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * My Box — Direct view (§4.3, §8.6)
 *
 * Box tipe direct milik customer, per batch.
 * REV-04.5: Customer can create their own direct batch.
 * REV-04.6: Request to Close + auto-close after 1 month.
 */
class BoxDirect extends Component
{
    use WithPagination;

    public string $search = '';
    public string $filterStatus = '';

    // ─── Detail Modal State ─────────────────────────────────────
    public ?int $detailBoxId = null;
    public bool $showDetail = false;

    // ─── REV-04.5: Create Batch Modal ──────────────────────────
    public bool $showCreateModal = false;
    public string $newBatchName = '';
    public string $newOpenDate = '';
    public string $newCargoType = 'sea'; // Flow Website: SEA/AIR pilihan
    public string $newBatchNumber = ''; // Flow Website: Nomor Batch

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

    // ─── REV-04.5: Create Direct Batch ─────────────────────────
    public function openCreateModal(): void
    {
        $this->newBatchName = '';
        $this->newOpenDate = now()->format('Y-m-d');
        $this->newCargoType = 'sea'; // Flow Website: default SEA
        $this->newBatchNumber = '';
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->newBatchName = '';
        $this->newOpenDate = '';
        $this->newCargoType = 'sea';
        $this->newBatchNumber = '';
    }

    public function createDirectBatch(): void
    {
        $this->validate([
            'newBatchNumber' => 'required|integer|min:1',
            'newCargoType' => 'required|in:sea,air',
            'newOpenDate' => 'required|date',
        ], [
            'newBatchNumber.required' => 'Nomor batch wajib diisi.',
            'newCargoType.required' => 'Jenis cargo wajib dipilih.',
            'newOpenDate.required' => 'Tanggal buka wajib diisi.',
        ]);

        $user = auth()->user();

        // Flow Website: auto-generate NamaID_SEA/AIR_B-NoBatch
        $namaId = $user->customer_code ?: strtoupper(substr($user->name, 0, 3));
        $autoName = $namaId . '_' . strtoupper($this->newCargoType) . '_B-' . $this->newBatchNumber;

        Box::create([
            'batch_name' => $autoName,
            'type' => 'direct',
            'method' => $this->newCargoType,
            'status' => Box::STATUS_OPEN,
            'customer_id' => $user->id,
            'open_date' => $this->newOpenDate,
        ]);

        $this->closeCreateModal();
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Batch direct berhasil dibuat: ' . $autoName);
    }

    // ─── REV-04.6: Request to Close ────────────────────────────
    public function requestToClose(int $boxId): void
    {
        $user = auth()->user();
        $box = Box::where('id', $boxId)
            ->where('customer_id', $user->id)
            ->where('type', 'direct')
            ->firstOrFail();

        if ($box->status === Box::STATUS_OPEN) {
            $box->update(['status' => Box::STATUS_REQUEST_TO_CLOSE]);
            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Request to close berhasil dikirim.');
        }
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
