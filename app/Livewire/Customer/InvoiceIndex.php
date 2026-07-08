<?php

namespace App\Livewire\Customer;

use App\Models\Invoice;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Invoice & Pembayaran — §4.5, §8.8, §11.4
 *
 * Lihat dan bayar invoice.
 * Trigger NotificationService on payment.
 */
class InvoiceIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';
    public string $filterStatus = '';

    // Pay modal state
    public ?int $payingInvoiceId = null;
    public string $paymentMethod = '';
    public $paymentProof = null;
    public bool $submitting = false;

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function openPayModal(int $invoiceId): void
    {
        $this->payingInvoiceId = $invoiceId;
        $this->paymentMethod = '';
        $this->paymentProof = null;
        $this->resetValidation();
    }

    public function closePayModal(): void
    {
        $this->payingInvoiceId = null;
        $this->reset(['paymentMethod', 'paymentProof']);
        $this->resetValidation();
    }

    public function submitPayment(NotificationService $notifService): void
    {
        $this->validate([
            'paymentMethod' => ['required', 'in:transfer,qris'],
            'paymentProof' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'paymentMethod.required' => 'Pilih metode pembayaran',
            'paymentMethod.in' => 'Metode pembayaran tidak valid',
            'paymentProof.required' => 'Upload bukti transfer',
            'paymentProof.mimes' => 'Format foto harus jpg atau png',
            'paymentProof.max' => 'Ukuran foto maksimal 5MB',
        ]);

        $this->submitting = true;

        $invoice = Invoice::where('id', $this->payingInvoiceId)
            ->where('customer_id', auth()->id())
            ->firstOrFail();

        if ($invoice->status !== Invoice::STATUS_WAITING_PAYMENT) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Invoice sudah dibayar.');
            $this->submitting = false;
            return;
        }

        // Upload payment proof
        $proofPath = $this->paymentProof->store('payment-proof', 'public');

        $invoice->update([
            'payment_method' => $this->paymentMethod,
            'payment_proof' => $proofPath,
            'status' => Invoice::STATUS_WAITING_VERIFICATION,
        ]);

        // Notify admin
        $notifService->paymentReceived($invoice);

        $this->closePayModal();
        $this->submitting = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Bukti transfer berhasil dikirim. Menunggu verifikasi admin.');
    }

    public function render()
    {
        $invoices = Invoice::where('customer_id', auth()->id())
            ->with('box')
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', "%{$this->search}%")
                      ->orWhereHas('box', function ($q2) {
                          $q2->where('tracking_number', 'like', "%{$this->search}%");
                      });
                });
            })
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.customer.invoice.index', compact('invoices'))
            ->layout('layouts.app');
    }
}
