<?php

namespace App\Livewire\Admin;

use App\Models\Checkout;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Admin Manage Checkouts — §4.6, §7.3, §8.9
 *
 * List checkout requests, process packing photo + tracking number.
 */
#[Layout('layouts.admin')]
#[Title('Checkout Requests — Ting Warehouse')]
class ManageCheckout extends Component
{
    use WithFileUploads, WithPagination;

    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = '';

    // ─── Detail & Actions ───────────────────────────────────────
    public ?int $selectedId = null;
    public bool $showDetail = false;
    public bool $showProcessModal = false;
    public $packingPhoto = null;
    public string $trackingNumber = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function selectCheckout(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    public function openProcessModal(): void
    {
        $this->reset(['packingPhoto', 'trackingNumber']);
        $this->resetValidation();
        $this->showProcessModal = true;
    }

    public function closeProcessModal(): void
    {
        $this->showProcessModal = false;
        $this->reset(['packingPhoto', 'trackingNumber']);
        $this->resetValidation();
    }

    public function processCheckout(NotificationService $notifService, AuditLogService $auditService): void
    {
        $this->validate([
            'trackingNumber' => ['required', 'string', 'min:3', 'max:100'],
            'packingPhoto' => ['nullable', 'image', 'max:5120'],
        ], [
            'trackingNumber.required' => 'Nomor tracking wajib diisi.',
            'trackingNumber.min' => 'Nomor tracking minimal 3 karakter.',
            'packingPhoto.image' => 'File harus berupa gambar.',
            'packingPhoto.max' => 'Ukuran foto maksimal 5MB.',
        ]);

        $checkout = Checkout::with(['invoice', 'customer'])->findOrFail($this->selectedId);

        if ($checkout->status !== Checkout::STATUS_REQUEST) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Checkout sudah diproses.');
            return;
        }

        $photoPath = null;
        if ($this->packingPhoto) {
            $photoPath = $this->packingPhoto->store('packing-photos', 'public');
        }

        $checkout->update([
            'packing_photo' => $photoPath,
            'tracking_number' => $this->trackingNumber,
            'status' => Checkout::STATUS_SENT,
        ]);

        $auditService->logCustom($checkout, 'checkout_processed', "Checkout #{$checkout->id} diproses, tracking: {$this->trackingNumber}");
        $notifService->checkoutProcessed($checkout);

        $this->closeProcessModal();
        $this->showDetail = false;
        $this->selectedId = null;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Checkout berhasil diproses.');
    }

    public function getSelectedCheckoutProperty(): ?Checkout
    {
        if (!$this->selectedId) return null;
        return Checkout::with(['invoice', 'invoice.box', 'customer'])->find($this->selectedId);
    }

    public function render()
    {
        $query = Checkout::with(['invoice', 'invoice.box', 'customer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tracking_number', 'like', "%{$this->search}%")
                  ->orWhere('recipient_name', 'like', "%{$this->search}%")
                  ->orWhereHas('invoice', function ($iq) {
                      $iq->where('invoice_number', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $checkouts = $query->latest()->paginate(15);

        return view('livewire.admin.checkouts.index', [
            'checkouts' => $checkouts,
            'selectedCheckout' => $this->selected_checkout,
        ]);
    }
}
