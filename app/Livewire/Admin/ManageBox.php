<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Models\Item;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NoTuanClaimService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Manage Box — Ting Warehouse')]
class ManageBox extends Component
{
    // ─── Filter & Search ────────────────────────────────────────
    #[Url]
    public string $search = '';
    #[Url]
    public string $filterType = '';
    #[Url]
    public string $filterStatus = '';
    #[Url]
    public string $filterCustomer = '';
    #[Url]
    public string $filterDate = '';

    // ─── UI State ───────────────────────────────────────────────
    public ?int $selectedBoxId = null;
    public bool $showDetail = false;
    public bool $showStatusConfirm = false;
    public string $pendingStatus = '';
    public string $statusNote = '';

    // ─── Box Edit (All Fields) ──────────────────────────────────
    public bool $showEditModal = false;
    public string $editTrackingNumber = '';
    public string $editHurufBox = '';
    public string $editEta = '';
    public string $editStevedoringDate = '';
    public string $editTagihanUpdateDate = '';
    public string $editType = '';
    public string $editMethod = '';
    public string $editBatchName = '';
    public ?int $editCustomerId = null;
    public string $editNotes = '';

    // ─── Box Creation ───────────────────────────────────────────
    public bool $showCreateModal = false;
    public string $newType = 'sharing';
    public string $newMethod = 'air';
    public string $newTrackingNumber = '';
    public string $newBatchName = '';
    public string $newHurufBox = '';
    public ?int $newCustomerId = null;
    public string $newNotes = '';

    // ─── Watchers ───────────────────────────────────────────────
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilterType(): void
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

    // ─── Actions ────────────────────────────────────────────────
    public function selectBox(int $id): void
    {
        $this->selectedBoxId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedBoxId = null;
    }

    // ─── Edit Tracking Number + ETA ─────────────────────────────
    public function openEditModal(): void
    {
        $box = Box::findOrFail($this->selectedBoxId);
        $this->editTrackingNumber = $box->tracking_number ?? '';
        $this->editHurufBox = $box->huruf_box ?? '';
        $this->editEta = $box->eta ? $box->eta->format('Y-m-d') : '';
        $this->editStevedoringDate = $box->stevedoring_date ? $box->stevedoring_date->format('Y-m-d') : '';
        $this->editTagihanUpdateDate = $box->tagihan_update_date ? $box->tagihan_update_date->format('Y-m-d') : '';
        $this->editType = $box->type;
        $this->editMethod = $box->method;
        $this->editBatchName = $box->batch_name ?? '';
        $this->editCustomerId = $box->customer_id;
        $this->editNotes = $box->notes ?? '';
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editTrackingNumber = '';
        $this->editHurufBox = '';
        $this->editEta = '';
        $this->editStevedoringDate = '';
        $this->editTagihanUpdateDate = '';
        $this->editType = '';
        $this->editMethod = '';
        $this->editBatchName = '';
        $this->editCustomerId = null;
        $this->editNotes = '';
    }

    public function saveBoxEdit(AuditLogService $auditService): void
    {
        $box = Box::findOrFail($this->selectedBoxId);

        $this->validate([
            'editTrackingNumber' => 'nullable|string|max:100',
            'editHurufBox' => 'nullable|string|max:10',
            'editEta' => 'nullable|date',
            'editStevedoringDate' => 'nullable|date',
            'editTagihanUpdateDate' => 'nullable|date',
            'editType' => 'required|in:sharing,direct,handcarry',
            'editMethod' => 'required|in:air,sea',
            'editBatchName' => 'nullable|string|max:100',
            'editCustomerId' => 'nullable|exists:users,id',
            'editNotes' => 'nullable|string|max:1000',
        ], [
            'editTrackingNumber.max' => 'Tracking number max 100 characters',
            'editHurufBox.max' => 'Huruf box max 10 characters',
            'editEta.date' => 'ETA must be a valid date',
            'editStevedoringDate.date' => 'Stevedoring date must be a valid date',
            'editTagihanUpdateDate.date' => 'Tagihan update date must be a valid date',
            'editType.required' => 'Tipe box wajib dipilih',
            'editMethod.required' => 'Metode wajib dipilih',
        ]);

        $oldValues = [
            'tracking_number' => $box->tracking_number,
            'huruf_box' => $box->huruf_box,
            'eta' => $box->eta?->format('Y-m-d'),
            'stevedoring_date' => $box->stevedoring_date?->format('Y-m-d'),
            'tagihan_update_date' => $box->tagihan_update_date?->format('Y-m-d'),
            'type' => $box->type,
            'method' => $box->method,
            'batch_name' => $box->batch_name,
            'customer_id' => $box->customer_id,
            'notes' => $box->notes,
        ];

        $box->tracking_number = $this->editTrackingNumber ?: null;
        $box->huruf_box = $this->editHurufBox ?: null;
        $box->eta = $this->editEta ?: null;
        $box->stevedoring_date = $this->editStevedoringDate ?: null;
        $box->tagihan_update_date = $this->editTagihanUpdateDate ?: null;
        $box->type = $this->editType;
        $box->method = $this->editMethod;
        $box->batch_name = $this->editBatchName ?: null;
        $box->customer_id = $this->editCustomerId;
        $box->notes = $this->editNotes ?: null;
        $box->save();

        $newValues = [
            'tracking_number' => $box->tracking_number,
            'huruf_box' => $box->huruf_box,
            'eta' => $box->eta?->format('Y-m-d'),
            'stevedoring_date' => $box->stevedoring_date?->format('Y-m-d'),
            'tagihan_update_date' => $box->tagihan_update_date?->format('Y-m-d'),
            'type' => $box->type,
            'method' => $box->method,
            'batch_name' => $box->batch_name,
            'customer_id' => $box->customer_id,
            'notes' => $box->notes,
        ];

        $auditService->log('updated', $box, $oldValues, $newValues);

        $this->showEditModal = false;
        $this->editTrackingNumber = '';
        $this->editHurufBox = '';
        $this->editEta = '';
        $this->editType = '';
        $this->editMethod = '';
        $this->editBatchName = '';
        $this->editCustomerId = null;
        $this->editNotes = '';

        $this->dispatch('toast', type: 'success', title: 'Success', message: 'Box updated successfully.');
    }

    public function confirmStatusChange(string $newStatus): void
    {
        $this->pendingStatus = $newStatus;
        $this->statusNote = '';
        $this->showStatusConfirm = true;
    }

    public function cancelStatusChange(): void
    {
        $this->showStatusConfirm = false;
        $this->pendingStatus = '';
        $this->statusNote = '';
    }

    public function updateStatus(NotificationService $notifService, AuditLogService $auditService): void
    {
        $box = Box::findOrFail($this->selectedBoxId);
        $oldStatus = $box->status;

        // Allow any status change (admin can fix mistakes)
        $validStatuses = [
            Box::STATUS_OPEN,
            Box::STATUS_CLOSED,
            Box::STATUS_SENT_TO_CARGO,
            Box::STATUS_OTW_INA,
            Box::STATUS_UP_INVOICE,
            Box::STATUS_DONE,
        ];

        if (!in_array($this->pendingStatus, $validStatuses)) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Status tidak valid.');
            return;
        }

        $box->status = $this->pendingStatus;
        $box->save();

        // Audit log
        $auditService->log('updated', $box, ['status' => $oldStatus], ['status' => $this->pendingStatus]);

        // Notification to customer
        if ($box->customer_id) {
            $notifService->boxStatusChanged($box, $oldStatus, $this->pendingStatus);
        }

        $this->showStatusConfirm = false;
        $this->pendingStatus = '';
        $this->statusNote = '';

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Status box berhasil diupdate.');
    }

    /**
     * Close a box — customer cannot setor resi anymore (Revisi §2.3, §4.2).
     * Warning message per §8.3.
     */
    public function closeBox(AuditLogService $auditService): void
    {
        $box = Box::findOrFail($this->selectedBoxId);

        if ($box->status !== Box::STATUS_OPEN) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Hanya box dengan status Open yang bisa ditutup.');
            return;
        }

        $oldStatus = $box->status;
        $box->status = Box::STATUS_CLOSED;
        $box->close_date = now();
        $box->save();

        $auditService->log('updated', $box, ['status' => $oldStatus], ['status' => Box::STATUS_CLOSED]);

        if ($box->customer_id) {
            $notifService = app(NotificationService::class);
            $notifService->boxStatusChanged($box, $oldStatus, Box::STATUS_CLOSED);
            // Revisi §2.11.2: Box ditutup notification
            $notifService->boxClosed($box);
        }

        $this->showStatusConfirm = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Box berhasil ditutup.');
    }

    /**
     * Re-open a closed box (Revisi §2.3).
     */
    public function openBox(AuditLogService $auditService): void
    {
        $box = Box::findOrFail($this->selectedBoxId);

        if ($box->status !== Box::STATUS_CLOSED) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Hanya box yang sudah ditutup yang bisa dibuka kembali.');
            return;
        }

        $oldStatus = $box->status;
        $box->status = Box::STATUS_OPEN;
        $box->close_date = null;
        $box->save();

        $auditService->log('updated', $box, ['status' => $oldStatus], ['status' => Box::STATUS_OPEN]);

        if ($box->customer_id) {
            $notifService = app(NotificationService::class);
            $notifService->boxStatusChanged($box, $oldStatus, Box::STATUS_OPEN);
        }

        $this->showStatusConfirm = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Box berhasil dibuka.');
    }

    /**
     * Mark an item as No Tuan (Revisi §2.1, §2.5.3).
     */
    public function markItemNoTuan(int $itemId, AuditLogService $auditService): void
    {
        $item = Item::findOrFail($itemId);

        if ($item->status !== Item::STATUS_ACTIVE) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Hanya barang aktif yang bisa ditandai sebagai No Tuan.');
            return;
        }

        $service = app(NoTuanClaimService::class);
        $service->markNoTuan($item);

        $auditService->logCustom($item, 'no_tuan_marked', "Barang '{$item->name}' ditandai sebagai No Tuan");

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Barang '{$item->name}' ditandai sebagai No Tuan.");
    }

    /**
     * Mark a No Tuan item as Klaim WH (Revisi §2.5.3).
     * Item becomes terminal — customer cannot claim anymore.
     */
    public function markItemKlaimWh(int $itemId, AuditLogService $auditService): void
    {
        $item = Item::findOrFail($itemId);

        if ($item->status !== Item::STATUS_NO_TUAN) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Hanya barang No Tuan yang bisa ditandai Klaim WH.');
            return;
        }

        $service = app(NoTuanClaimService::class);
        $service->markKlaimWh($item);

        $auditService->logCustom($item, 'klaim_wh_marked', "Barang '{$item->name}' ditandai Klaim WH untuk dijual/dilelang");

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Barang '{$item->name}' ditandai Klaim WH.");
    }

    public function openCreateModal(): void
    {
        $this->resetCreateForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetCreateForm();
    }

    public function createBox(AuditLogService $auditService): void
    {
        $this->validate([
            'newType' => 'required|in:sharing,direct,handcarry',
            'newMethod' => 'required|in:air,sea',
            'newTrackingNumber' => 'nullable|string|max:100',
            'newBatchName' => 'nullable|string|max:100',
            'newHurufBox' => 'nullable|string|max:10',
            'newCustomerId' => 'nullable|exists:users,id',
            'newNotes' => 'nullable|string|max:1000',
        ]);

        $box = Box::create([
            'type' => $this->newType,
            'method' => $this->newMethod,
            'tracking_number' => $this->newTrackingNumber ?: null,
            'batch_name' => $this->newBatchName ?: null,
            'huruf_box' => $this->newHurufBox ?: null,
            'customer_id' => $this->newCustomerId,
            'notes' => $this->newNotes ?: null,
            'status' => Box::STATUS_OPEN,
            'open_date' => now(),
        ]);

        $auditService->logCustom($box, 'created', 'Box baru dibuat oleh admin');

        $this->showCreateModal = false;
        $this->resetCreateForm();

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Box baru berhasil dibuat.');
    }

    private function resetCreateForm(): void
    {
        $this->newType = 'sharing';
        $this->newMethod = 'air';
        $this->newTrackingNumber = '';
        $this->newBatchName = '';
        $this->newHurufBox = '';
        $this->newCustomerId = null;
        $this->newNotes = '';
    }

    private function resetPage(): void
    {
        // Livewire pagination reset is handled by #[Url]
    }

    // ─── Computed Properties ────────────────────────────────────
    public function getSelectedBoxProperty(): ?Box
    {
        if (!$this->selectedBoxId) return null;

        return Box::with(['customer', 'items', 'invoices'])
            ->find($this->selectedBoxId);
    }

    public function getCustomersProperty()
    {
        return User::where('role', 'customer')
            ->where('status', User::STATUS_ACTIVE)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        $query = Box::with('customer')
            ->withCount('items');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tracking_number', 'like', "%{$this->search}%")
                  ->orWhere('batch_name', 'like', "%{$this->search}%")
                  ->orWhere('huruf_box', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterType) {
            $query->where('type', $this->filterType);
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

        $boxes = $query->latest()->paginate(15);

        return view('livewire.admin.boxes.index', [
            'boxes' => $boxes,
            'selectedBox' => $this->selected_box,
            'customers' => $this->customers,
        ]);
    }
}
