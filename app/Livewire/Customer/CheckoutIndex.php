<?php

namespace App\Livewire\Customer;

use App\Models\Checkout;
use App\Models\Invoice;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Checkout — §4.6, §8.9, §11.5
 *
 * Request pengiriman barang.
 */
class CheckoutIndex extends Component
{
    use WithPagination;

    public string $filterStatus = '';

    // Form state
    public bool $showForm = false;
    public ?int $invoiceId = null;
    public string $addressType = 'personal';
    public string $recipientName = '';
    public string $recipientPhone = '';
    public string $address = '';
    public string $senderName = '';
    public string $senderPhone = '';
    public bool $confirmation = false;
    public bool $submitting = false;

    // Sprint 3: Ekspedisi + ongkir
    public ?int $ekspedisiId = null;
    public string $ongkir = '0';

    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function openForm(): void
    {
        $this->showForm = true;
        $this->resetValidation();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['invoiceId', 'addressType', 'recipientName', 'recipientPhone', 'address', 'senderName', 'senderPhone', 'confirmation', 'ekspedisiId', 'ongkir']);
        $this->resetValidation();
    }

    public function submit(): void
    {
        $this->validate([
            'invoiceId' => ['required', 'exists:invoices,id'],
            'addressType' => ['required', 'in:personal,dropship'],
            'recipientName' => ['required', 'string', 'min:3', 'max:255'],
            'recipientPhone' => ['required', 'string', 'min:10', 'max:15'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'senderName' => [$this->addressType === 'dropship' ? 'required' : 'nullable', 'string', 'max:255'],
            'senderPhone' => [$this->addressType === 'dropship' ? 'required' : 'nullable', 'string', 'max:20'],
            'confirmation' => ['required', 'accepted'],
            // Sprint 3: Ekspedisi
            'ekspedisiId' => ['nullable', 'exists:ekspedisis,id'],
            'ongkir' => ['nullable', 'numeric', 'min:0', 'max:9999999'],
        ], [
            'invoiceId.required' => 'Pilih invoice terlebih dahulu',
            'invoiceId.exists' => 'Invoice tidak ditemukan',
            'addressType.required' => 'Pilih tipe alamat',
            'recipientName.required' => 'Nama penerima wajib diisi',
            'recipientName.min' => 'Nama penerima minimal 3 karakter',
            'recipientPhone.required' => 'Nomor telepon wajib diisi',
            'recipientPhone.min' => 'Nomor telepon minimal 10 digit',
            'address.required' => 'Alamat wajib diisi',
            'address.min' => 'Alamat minimal 10 karakter',
            'senderName.required' => 'Nama pengirim wajib diisi untuk dropship',
            'senderPhone.required' => 'No telp pengirim wajib diisi untuk dropship',
            'confirmation.accepted' => 'Centang konfirmasi terlebih dahulu',
        ]);

        $this->submitting = true;

        $invoice = Invoice::where('id', $this->invoiceId)
            ->where('customer_id', auth()->id())
            ->where('status', Invoice::STATUS_VERIFIED)
            ->first();

        if (!$invoice) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Invoice belum terverifikasi.');
            $this->submitting = false;
            return;
        }

        // Check if checkout already exists for this invoice
        $exists = Checkout::where('invoice_id', $this->invoiceId)
            ->where('customer_id', auth()->id())
            ->exists();

        if ($exists) {
            $this->dispatch('toast', type: 'error', title: 'Gagal', message: 'Checkout sudah diajukan untuk invoice ini.');
            $this->submitting = false;
            return;
        }

        Checkout::create([
            'invoice_id' => $this->invoiceId,
            'customer_id' => auth()->id(),
            'address_type' => $this->addressType,
            'recipient_name' => $this->recipientName,
            'recipient_phone' => $this->recipientPhone,
            'address' => $this->address,
            'sender_name' => $this->addressType === 'dropship' ? $this->senderName : null,
            'sender_phone' => $this->addressType === 'dropship' ? $this->senderPhone : null,
            'status' => Checkout::STATUS_REQUEST,
            // Sprint 3: Ekspedisi + ongkir
            'ekspedisi_id' => $this->ekspedisiId,
            'ongkir' => $this->ongkir ?: 0,
        ]);

        $this->closeForm();
        $this->submitting = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Request checkout berhasil dikirim.');
    }

    public function render()
    {
        $checkouts = Checkout::where('customer_id', auth()->id())
            ->with(['invoice', 'invoice.box', 'ekspedisi'])
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        // Verified invoices for checkout form
        $verifiedInvoices = Invoice::where('customer_id', auth()->id())
            ->where('status', Invoice::STATUS_VERIFIED)
            ->whereDoesntHave('checkouts', function ($q) {
                $q->where('customer_id', auth()->id());
            })
            ->with('box')
            ->get();

        // Sprint 3: Active ekspedisi list
        $ekspedisis = \App\Models\Ekspedisi::getActive();

        return view('livewire.customer.checkout.index', compact('checkouts', 'verifiedInvoices', 'ekspedisis'))
            ->layout('layouts.app');
    }
}
