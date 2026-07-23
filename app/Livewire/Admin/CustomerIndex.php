<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Info Customer — Ting Warehouse')]
class CustomerIndex extends Component
{
    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = '';

    public ?int $selectedId = null;
    public bool $showDetail = false;

    // ─── Edit Modal ────────────────────────────────────────────
    public bool $showEditModal = false;
    public string $editName = '';
    public string $editEmail = '';
    public string $editPhone = '';
    public string $editKtpNumber = '';
    public string $editAddress = '';
    public string $editLineId = '';
    public string $editCustomerCode = '';
    public string $editStatus = '';
    public string $editCustomRateAir = '';
    public string $editCustomRateSea = '';

    // ─── Delete Confirmation ───────────────────────────────────
    public bool $showDeleteConfirm = false;

    // ─── Password Reset ────────────────────────────────────────
    public bool $showResetPasswordConfirm = false;
    public string $newPassword = '';

    public function openResetPasswordConfirm(): void
    {
        $this->newPassword = '';
        $this->showResetPasswordConfirm = true;
    }

    public function closeResetPasswordConfirm(): void
    {
        $this->showResetPasswordConfirm = false;
        $this->newPassword = '';
    }

    public function resetPassword(AuditLogService $auditService): void
    {
        $this->validate([
            'newPassword' => 'required|string|min:8|max:50',
        ], [
            'newPassword.required' => 'Password baru wajib diisi.',
            'newPassword.min' => 'Password minimal 8 karakter.',
        ]);

        $customer = User::findOrFail($this->selectedId);
        $customer->password = bcrypt($this->newPassword);
        $customer->save();

        $auditService->logCustom($customer, 'password_reset', "Password customer {$customer->name} direset oleh admin");

        $this->showResetPasswordConfirm = false;
        $this->newPassword = '';

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Password {$customer->name} berhasil direset.");
    }

    public function selectCustomer(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    public function activateCustomer(NotificationService $notifService, AuditLogService $auditService): void
    {
        $customer = User::findOrFail($this->selectedId);

        if ($customer->status !== User::STATUS_PENDING) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Customer tidak dalam status menunggu aktivasi.');
            return;
        }

        $customer->status = User::STATUS_ACTIVE;
        $customer->save();

        $auditService->logCustom($customer, 'activated', "Customer {$customer->name} diaktivasi");
        $notifService->accountActivated($customer);

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Akun {$customer->name} berhasil diaktivasi.");
    }

    public function deactivateCustomer(NotificationService $notifService, AuditLogService $auditService): void
    {
        $customer = User::findOrFail($this->selectedId);

        if ($customer->status !== User::STATUS_ACTIVE) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Customer tidak aktif.');
            return;
        }

        $customer->status = User::STATUS_INACTIVE;
        $customer->save();

        $auditService->logCustom($customer, 'deactivated', "Customer {$customer->name} dinonaktifkan");

        $this->dispatch('toast', type: 'warning', title: 'Berhasil', message: "Akun {$customer->name} dinonaktifkan.");
    }

    // ─── Edit Customer ─────────────────────────────────────────

    public function openEditModal(): void
    {
        $customer = User::findOrFail($this->selectedId);
        $this->editName = $customer->name;
        $this->editEmail = $customer->email;
        $this->editPhone = $customer->phone ?? '';
        $this->editKtpNumber = $customer->ktp_number ?? '';
        $this->editAddress = $customer->address ?? '';
        $this->editLineId = $customer->line_id ?? '';
        $this->editCustomerCode = $customer->customer_code ?? '';
        $this->editStatus = $customer->status;
        $this->editCustomRateAir = $customer->custom_rate_air !== null ? (string) $customer->custom_rate_air : '';
        $this->editCustomRateSea = $customer->custom_rate_sea !== null ? (string) $customer->custom_rate_sea : '';
        $this->showEditModal = true;
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
    }

    public function saveCustomer(AuditLogService $auditService): void
    {
        $customer = User::findOrFail($this->selectedId);

        $this->validate([
            'editName' => 'required|string|max:255',
            'editEmail' => 'required|email|max:255|unique:users,email,' . $customer->id,
            'editPhone' => 'nullable|string|max:20',
            'editKtpNumber' => 'nullable|string|max:30',
            'editAddress' => 'nullable|string|max:500',
            'editLineId' => 'nullable|string|max:50',
            'editCustomerCode' => 'nullable|string|max:10|unique:users,customer_code,' . $customer->id,
            'editStatus' => 'required|in:pending,active,inactive',
            'editCustomRateAir' => 'nullable|numeric|min:0',
            'editCustomRateSea' => 'nullable|numeric|min:0',
        ], [
            'editName.required' => 'Nama wajib diisi',
            'editEmail.required' => 'Email wajib diisi',
            'editEmail.email' => 'Format email tidak valid',
            'editEmail.unique' => 'Email sudah digunakan',
            'editStatus.required' => 'Status wajib dipilih',
        ]);

        $oldValues = [
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'status' => $customer->status,
        ];

        $customer->update([
            'name' => $this->editName,
            'email' => $this->editEmail,
            'phone' => $this->editPhone ?: null,
            'ktp_number' => $this->editKtpNumber ?: null,
            'address' => $this->editAddress ?: null,
            'line_id' => $this->editLineId ?: null,
            'customer_code' => $this->editCustomerCode ?: null,
            'status' => $this->editStatus,
            'custom_rate_air' => $this->editCustomRateAir !== '' ? (float) $this->editCustomRateAir : null,
            'custom_rate_sea' => $this->editCustomRateSea !== '' ? (float) $this->editCustomRateSea : null,
        ]);

        $newValues = [
            'name' => $customer->name,
            'email' => $customer->email,
            'phone' => $customer->phone,
            'status' => $customer->status,
        ];

        $auditService->log('updated', $customer, $oldValues, $newValues);

        $this->showEditModal = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Data customer {$customer->name} berhasil diupdate.");
    }

    // ─── Delete Customer ───────────────────────────────────────

    public function openDeleteConfirm(): void
    {
        $this->showDeleteConfirm = true;
    }

    public function closeDeleteConfirm(): void
    {
        $this->showDeleteConfirm = false;
    }

    public function deleteCustomer(AuditLogService $auditService): void
    {
        $customer = User::findOrFail($this->selectedId);

        // Safety: check for active data
        $activeBoxes = $customer->boxes()->whereIn('status', ['OPEN', 'SENT_TO_CARGO', 'OTW_INA', 'UP_INVOICE'])->count();
        $activeInvoices = $customer->invoices()->whereIn('status', ['waiting_payment', 'waiting_verification'])->count();

        // Sprint 5B: Juga cek barang yang masih di China (box belum sampai INA)
        $itemsInChina = $customer->items()
            ->whereHas('box', function ($q) {
                $q->whereNotIn('status', ['ARRIVED_INA', 'REDLINE', 'STEVEDORING', 'CHECKED_BY_WH', 'INVOICE', 'DONE']);
            })
            ->count();

        if ($activeBoxes > 0 || $activeInvoices > 0 || $itemsInChina > 0) {
            $this->dispatch('toast',
                type: 'error',
                title: 'Tidak Bisa Dihapus',
                message: "Customer memiliki {$activeBoxes} box aktif, {$activeInvoices} invoice aktif, dan {$itemsInChina} barang masih di China. Selesaikan dulu sebelum menghapus.",
            );
            $this->showDeleteConfirm = false;
            return;
        }

        $name = $customer->name;
        $auditService->logCustom($customer, 'deleted', "Customer {$name} dihapus oleh admin");

        $customer->delete();

        $this->showDeleteConfirm = false;
        $this->showDetail = false;
        $this->selectedId = null;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Customer {$name} berhasil dihapus.");
    }

    public function getSelectedCustomerProperty(): ?User
    {
        if (!$this->selectedId) return null;
        return User::with(['boxes', 'invoices', 'checkouts', 'complains'])->find($this->selectedId);
    }

    public function render()
    {
        $query = User::where('role', 'customer')
            ->withCount(['boxes', 'invoices', 'checkouts', 'complains']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%")
                  ->orWhere('phone', 'like', "%{$this->search}%");
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $customers = $query->latest()->paginate(15);

        return view('livewire.admin.customers.index', [
            'customers' => $customers,
            'selectedCustomer' => $this->selected_customer,
        ]);
    }
}
