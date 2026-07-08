<?php

namespace App\Livewire\Admin;

use App\Models\User;
use App\Services\AuditLogService;
use App\Services\NotificationService;
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
