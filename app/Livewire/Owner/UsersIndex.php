<?php

namespace App\Livewire\Owner;

use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Owner Manage Users — §7.4, §3.3
 *
 * CRUD user, ganti role, activate/deactivate.
 */
#[Layout('layouts.admin')]
#[Title('Manage Users — Ting Warehouse')]
class UsersIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    #[Url]
    public string $filterRole = '';
    #[Url]
    public string $filterStatus = '';

    // ─── Detail & Actions ───────────────────────────────────────
    public ?int $selectedId = null;
    public bool $showDetail = false;
    public bool $showRoleModal = false;
    public string $newRole = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterRole(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function selectUser(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    public function openRoleModal(): void
    {
        $user = User::findOrFail($this->selectedId);
        $this->newRole = $user->role;
        $this->showRoleModal = true;
    }

    public function closeRoleModal(): void
    {
        $this->showRoleModal = false;
        $this->newRole = '';
    }

    public function updateRole(AuditLogService $auditService): void
    {
        $this->validate([
            'newRole' => ['required', 'in:admin,customer'],
        ]);

        $user = User::findOrFail($this->selectedId);

        if ($user->isOwner()) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Tidak bisa mengubah role owner.');
            return;
        }

        $oldRole = $user->role;
        $user->update(['role' => $this->newRole]);

        $auditService->logCustom($user, 'role_changed', "Role diubah: {$oldRole} → {$this->newRole}");

        $this->closeRoleModal();

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Role {$user->name} diubah ke {$this->newRole}.");
    }

    public function toggleStatus(NotificationService $notifService, AuditLogService $auditService): void
    {
        $user = User::findOrFail($this->selectedId);

        if ($user->isOwner()) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Tidak bisa mengubah status owner.');
            return;
        }

        $oldStatus = $user->status;
        $newStatus = $user->status === User::STATUS_ACTIVE ? User::STATUS_INACTIVE : User::STATUS_ACTIVE;
        $user->update(['status' => $newStatus]);

        $auditService->logCustom($user, 'status_changed', "Status diubah: {$oldStatus} → {$newStatus}");

        if ($newStatus === User::STATUS_ACTIVE) {
            $notifService->accountActivated($user);
        }

        $label = $newStatus === User::STATUS_ACTIVE ? 'diaktifkan' : 'dinonaktifkan';
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "{$user->name} berhasil {$label}.");
    }

    public function getSelectedUserProperty(): ?User
    {
        if (!$this->selectedId) return null;
        return User::find($this->selectedId);
    }

    public function render()
    {
        $query = User::query();

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterRole) {
            $query->where('role', $this->filterRole);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $users = $query->latest()->paginate(15);

        return view('livewire.owner.users.index', [
            'users' => $users,
            'selectedUser' => $this->selected_user,
        ]);
    }
}
