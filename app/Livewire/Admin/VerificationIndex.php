<?php

namespace App\Livewire\Admin;

use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Verifikasi Pembayaran — Ting Warehouse')]
class VerificationIndex extends Component
{
    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = 'waiting_verification';

    // ─── Detail & Actions ───────────────────────────────────────
    public ?int $selectedId = null;
    public bool $showDetail = false;
    public bool $showRejectModal = false;
    public string $rejectReason = '';

    public function selectInvoice(int $id): void
    {
        $this->selectedId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedId = null;
    }

    public function verifyPayment(NotificationService $notifService, AuditLogService $auditService): void
    {
        $invoice = Invoice::with('customer')->findOrFail($this->selectedId);

        if ($invoice->status !== Invoice::STATUS_WAITING_VERIFICATION) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Invoice tidak dalam status menunggu verifikasi.');
            return;
        }

        $invoice->status = Invoice::STATUS_VERIFIED;
        $invoice->save();

        // Revisi §2.4.4: denda tagged → paid saat invoice verified
        DendaClaim::where('invoice_id', $invoice->id)
            ->where('status', DendaClaim::STATUS_TAGGED)
            ->update(['status' => DendaClaim::STATUS_PAID]);

        $auditService->logCustom($invoice, 'payment_verified', "Pembayaran invoice {$invoice->invoice_number} diverifikasi");
        $notifService->paymentVerified($invoice);

        $this->showDetail = false;
        $this->selectedId = null;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Pembayaran berhasil diverifikasi.');
    }

    public function openRejectModal(): void
    {
        $this->rejectReason = '';
        $this->showRejectModal = true;
    }

    public function rejectPayment(NotificationService $notifService, AuditLogService $auditService): void
    {
        $this->validate([
            'rejectReason' => 'required|string|min:5|max:500',
        ]);

        $invoice = Invoice::with('customer')->findOrFail($this->selectedId);

        $oldStatus = $invoice->status;
        $invoice->status = Invoice::STATUS_WAITING_PAYMENT;
        $invoice->payment_proof = null;
        $invoice->payment_method = null;
        $invoice->save();

        $auditService->logCustom($invoice, 'payment_rejected', "Pembayaran ditolak: {$this->rejectReason}", ['status' => $oldStatus], ['status' => Invoice::STATUS_WAITING_PAYMENT]);
        $notifService->paymentRejected($invoice, $this->rejectReason);

        $this->showRejectModal = false;
        $this->showDetail = false;
        $this->selectedId = null;
        $this->rejectReason = '';

        $this->dispatch('toast', type: 'warning', title: 'Ditolak', message: 'Bukti transfer ditolak. Customer akan mendapat notifikasi.');
    }

    public function getSelectedInvoiceProperty(): ?Invoice
    {
        if (!$this->selectedId) return null;
        return Invoice::with(['box', 'customer'])->find($this->selectedId);
    }

    public function render()
    {
        $query = Invoice::with(['box', 'customer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $invoices = $query->latest()->paginate(15);

        return view('livewire.admin.verification.index', [
            'invoices' => $invoices,
            'selectedInvoice' => $this->selected_invoice,
        ]);
    }
}
