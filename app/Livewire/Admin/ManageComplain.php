<?php

namespace App\Livewire\Admin;

use App\Models\Complain;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Admin Manage Complains — §4.7, §7.3, §8.10
 *
 * List complains, update status (OPEN → IN_REVIEW → PROCESSING → RESOLVED).
 */
#[Layout('layouts.admin')]
#[Title('Kelola Komplain — Ting Warehouse')]
class ManageComplain extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = '';

    // ─── Detail & Actions ───────────────────────────────────────
    public ?int $selectedId = null;
    public bool $showDetail = false;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function selectComplain(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    public function updateStatus(string $newStatus, NotificationService $notifService, AuditLogService $auditService): void
    {
        $complain = Complain::with('customer')->findOrFail($this->selectedId);

        $validTransitions = [
            Complain::STATUS_OPEN => Complain::STATUS_IN_REVIEW,
            Complain::STATUS_IN_REVIEW => Complain::STATUS_PROCESSING,
            Complain::STATUS_PROCESSING => Complain::STATUS_RESOLVED,
        ];

        if (!isset($validTransitions[$complain->status]) || $validTransitions[$complain->status] !== $newStatus) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Transisi status tidak valid.');
            return;
        }

        $oldStatus = $complain->status;
        $complain->update(['status' => $newStatus]);

        $auditService->logCustom($complain, 'complaint_status_changed', "Status komplain diubah: {$oldStatus} → {$newStatus}");
        $notifService->complaintUpdated($complain, $oldStatus, $newStatus);

        $statusLabels = [
            Complain::STATUS_IN_REVIEW => 'In Review',
            Complain::STATUS_PROCESSING => 'Processing',
            Complain::STATUS_RESOLVED => 'Resolved',
        ];

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Status komplain diubah ke {$statusLabels[$newStatus]}.");
    }

    public function getSelectedComplainProperty(): ?Complain
    {
        if (!$this->selectedId) return null;
        return Complain::with(['customer', 'box'])->find($this->selectedId);
    }

    public function render()
    {
        $query = Complain::with(['customer', 'box']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('type', 'like', "%{$this->search}%")
                  ->orWhere('invoice_number', 'like', "%{$this->search}%")
                  ->orWhere('resi_number', 'like', "%{$this->search}%")
                  ->orWhere('description', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $complains = $query->latest()->paginate(15);

        return view('livewire.admin.complains.index', [
            'complains' => $complains,
            'selectedComplain' => $this->selected_complain,
        ]);
    }
}
