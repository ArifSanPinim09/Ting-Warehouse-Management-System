<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Models\User;
use App\Services\AuditLogService;
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

    // ─── Box Creation ───────────────────────────────────────────
    public bool $showCreateModal = false;
    public string $newType = 'sharing';
    public string $newMethod = 'air';
    public string $newTrackingNumber = '';
    public string $newBatchName = '';
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

        // Validate transition order
        $validTransitions = [
            Box::STATUS_OPEN => Box::STATUS_SENT_TO_CARGO,
            Box::STATUS_SENT_TO_CARGO => Box::STATUS_OTW_INA,
            Box::STATUS_OTW_INA => Box::STATUS_UP_INVOICE,
            Box::STATUS_UP_INVOICE => Box::STATUS_DONE,
        ];

        if (!isset($validTransitions[$oldStatus]) || $validTransitions[$oldStatus] !== $this->pendingStatus) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Perubahan status tidak valid.');
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
            'newCustomerId' => 'nullable|exists:users,id',
            'newNotes' => 'nullable|string|max:1000',
        ]);

        $box = Box::create([
            'type' => $this->newType,
            'method' => $this->newMethod,
            'tracking_number' => $this->newTrackingNumber ?: null,
            'batch_name' => $this->newBatchName ?: null,
            'customer_id' => $this->newCustomerId,
            'notes' => $this->newNotes ?: null,
            'status' => Box::STATUS_OPEN,
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
