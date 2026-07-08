<?php

namespace App\Livewire\Owner;

use App\Models\ActivityLog;
use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Manage Admin — Ting Warehouse')]
class ManageAdminIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = '';
    #[Url]
    public int $perPage = 15;

    public ?int $selectedId = null;
    public bool $showDetail = false;

    // ─── Confirmation Dialog ─────────────────────────────────────
    public bool $showConfirm = false;
    public string $confirmAction = '';
    public string $confirmMessage = '';

    public function updatedSearch(): void { $this->resetPage(); }
    public function updatedFilterStatus(): void { $this->resetPage(); }

    public function selectAdmin(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    public function getSelectedAdminProperty(): ?User
    {
        if (!$this->selectedId) return null;
        return User::find($this->selectedId);
    }

    public function confirmActivate(int $id): void
    {
        $admin = User::findOrFail($id);
        $this->selectedId = $id;
        $this->confirmAction = 'activate';
        $this->confirmMessage = "Aktifkan akun admin \"{$admin->name}\"? Admin akan dapat mengakses seluruh fitur operasional.";
        $this->showConfirm = true;
    }

    public function confirmDeactivate(int $id): void
    {
        $admin = User::findOrFail($id);
        $this->selectedId = $id;
        $this->confirmAction = 'deactivate';
        $this->confirmMessage = "Nonaktifkan akun admin \"{$admin->name}\"? Admin tidak akan dapat mengakses sistem sampai diaktifkan kembali.";
        $this->showConfirm = true;
    }

    public function cancelConfirm(): void
    {
        $this->showConfirm = false;
        $this->confirmAction = '';
        $this->confirmMessage = '';
    }

    public function executeConfirm(AuditLogService $auditService): void
    {
        $admin = User::findOrFail($this->selectedId);

        if ($this->confirmAction === 'activate') {
            $this->activateAdmin($admin, $auditService);
        } elseif ($this->confirmAction === 'deactivate') {
            $this->deactivateAdmin($admin, $auditService);
        }

        $this->showConfirm = false;
        $this->confirmAction = '';
        $this->confirmMessage = '';
    }

    private function activateAdmin(User $admin, AuditLogService $auditService): void
    {
        if ($admin->status === User::STATUS_ACTIVE) {
            $this->dispatch('toast', type: 'info', title: 'Info', message: "{$admin->name} sudah aktif.");
            return;
        }

        $oldStatus = $admin->status;
        $admin->status = User::STATUS_ACTIVE;
        $admin->save();

        $auditService->logCustom($admin, 'activated', "Admin {$admin->name} diaktifkan oleh Owner", [
            'status' => $oldStatus,
        ], [
            'status' => User::STATUS_ACTIVE,
        ]);

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Akun admin \"{$admin->name}\" berhasil diaktifkan.");
    }

    private function deactivateAdmin(User $admin, AuditLogService $auditService): void
    {
        if ($admin->status === User::STATUS_INACTIVE) {
            $this->dispatch('toast', type: 'info', title: 'Info', message: "{$admin->name} sudah nonaktif.");
            return;
        }

        $oldStatus = $admin->status;
        $admin->status = User::STATUS_INACTIVE;
        $admin->save();

        $auditService->logCustom($admin, 'deactivated', "Admin {$admin->name} dinonaktifkan oleh Owner", [
            'status' => $oldStatus,
        ], [
            'status' => User::STATUS_INACTIVE,
        ]);

        $this->dispatch('toast', type: 'warning', title: 'Berhasil', message: "Akun admin \"{$admin->name}\" dinonaktifkan.");
    }

    public function getAdminActivitiesProperty()
    {
        if (!$this->selectedId) return collect();

        return ActivityLog::where('user_id', $this->selectedId)
            ->with('user')
            ->latest()
            ->limit(20)
            ->get();
    }

    public function render()
    {
        $query = User::whereIn('role', ['admin', 'owner'])
            ->where('id', '!=', auth()->id());

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $admins = $query->latest()->paginate($this->perPage);

        return view('livewire.owner.manage-admin.index', [
            'admins' => $admins,
            'selectedAdmin' => $this->selected_admin,
            'adminActivities' => $this->selectedId ? ActivityLog::where('user_id', $this->selectedId)->with('user')->latest()->limit(20)->get() : collect(),
        ]);
    }
}
