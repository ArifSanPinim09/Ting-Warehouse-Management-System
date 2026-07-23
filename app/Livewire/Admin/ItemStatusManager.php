<?php

namespace App\Livewire\Admin;

use App\Models\Item;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Sprint 5C-2: Page edit status item — admin CRUD status options.
 *
 * Admin bisa lihat semua items, filter by status, dan edit status satu per satu.
 */
#[Layout('layouts.admin')]
#[Title('Edit Status Barang — Ting Warehouse')]
class ItemStatusManager extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = '';

    public bool $showEditModal = false;
    public ?int $editId = null;
    public string $editStatus = '';
    public string $editNote = '';

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingFilterStatus(): void
    {
        $this->resetPage();
    }

    public function openEditModal(int $id): void
    {
        $item = Item::findOrFail($id);
        $this->editId = $id;
        $this->editStatus = $item->status;
        $this->editNote = '';
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editId = null;
        $this->editStatus = '';
        $this->editNote = '';
    }

    public function saveStatus(AuditLogService $auditService): void
    {
        $this->validate([
            'editStatus' => 'required|in:' . implode(',', Item::getValidStatuses()),
        ]);

        $item = Item::findOrFail($this->editId);
        $oldStatus = $item->status;
        $item->status = $this->editStatus;
        $item->save();

        $auditService->logCustom(
            $item,
            'status_changed',
            "Status barang {$item->resi_number} diubah dari {$oldStatus} ke {$this->editStatus}" . ($this->editNote ? " — {$this->editNote}" : '')
        );

        $this->showEditModal = false;
        $this->editId = null;
        $this->editStatus = '';
        $this->editNote = '';

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Status barang berhasil diupdate.');
    }

    public function render()
    {
        $items = Item::with(['box', 'customer'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('resi_number', 'like', "%{$this->search}%")
                      ->orWhere('name', 'like', "%{$this->search}%");
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(15);

        $statuses = Item::getValidStatuses();

        return view('livewire.admin.item-status-manager.index', compact('items', 'statuses'));
    }
}
