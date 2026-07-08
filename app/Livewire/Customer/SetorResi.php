<?php

namespace App\Livewire\Customer;

use App\Http\Requests\Customer\SetorResiRequest;
use App\Models\Box;
use App\Models\Item;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Setor Resi — §4.4, §8.7, §11.3
 *
 * Input barang baru.
 * Trigger NotificationService on success.
 */
class SetorResi extends Component
{
    use WithFileUploads;

    public ?int $boxId = null;
    public string $name = '';
    public int $quantity = 1;
    public string $priceYuan = '';
    public string $resiNumber = '';
    public $proofCo = null;
    public bool $isSensitive = false;
    public ?string $sensitiveType = null;

    public bool $showSuccess = false;
    public bool $submitting = false;

    protected function rules(): array
    {
        return [
            'boxId' => ['required', 'exists:boxes,id'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'priceYuan' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'resiNumber' => ['required', 'string', 'min:3', 'max:100'],
            'proofCo' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'isSensitive' => ['nullable', 'boolean'],
            'sensitiveType' => ['required_if:isSensitive,true', 'nullable', 'string', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'boxId.required' => 'Pilih box terlebih dahulu',
            'boxId.exists' => 'Box tidak ditemukan',
            'name.required' => 'Nama barang wajib diisi',
            'name.min' => 'Nama barang minimal 2 karakter',
            'quantity.required' => 'Jumlah wajib diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
            'priceYuan.required' => 'Harga wajib diisi',
            'priceYuan.numeric' => 'Harga harus berupa angka',
            'priceYuan.min' => 'Harga minimal 0.01',
            'resiNumber.required' => 'Nomor resi wajib diisi',
            'resiNumber.min' => 'Nomor resi minimal 3 karakter',
            'proofCo.required' => 'Foto bukti barang wajib diupload',
            'proofCo.mimes' => 'Format foto harus jpg, png, atau webp',
            'proofCo.max' => 'Ukuran foto maksimal 5MB',
            'sensitiveType.required_if' => 'Pilih jenis sensitive item',
        ];
    }

    public function submit(NotificationService $notifService): void
    {
        $this->validate();
        $this->submitting = true;

        // Verify box is OPEN
        $box = Box::find($this->boxId);
        if (!$box || $box->status !== Box::STATUS_OPEN) {
            $this->addError('boxId', 'Box sudah ditutup, tidak bisa menambah barang');
            $this->submitting = false;
            return;
        }

        // Check duplicate resi in same box
        $exists = Item::where('box_id', $this->boxId)
            ->where('resi_number', $this->resiNumber)
            ->exists();

        if ($exists) {
            $this->addError('resiNumber', "Nomor resi sudah terdaftar di box ini");
            $this->submitting = false;
            return;
        }

        // Upload proof photo with hashed filename
        $proofPath = $this->proofCo->store('proof-co', 'public');

        Item::create([
            'box_id' => $this->boxId,
            'customer_id' => auth()->id(),
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price_yuan' => $this->priceYuan,
            'resi_number' => $this->resiNumber,
            'proof_co' => $proofPath,
            'is_sensitive' => $this->isSensitive,
            'sensitive_type' => $this->isSensitive ? $this->sensitiveType : null,
        ]);

        // Trigger notification to admin
        $notifService->customerRegister(auth()->user());

        // Reset form
        $this->reset(['name', 'quantity', 'priceYuan', 'resiNumber', 'proofCo', 'isSensitive', 'sensitiveType']);
        $this->showSuccess = true;
        $this->submitting = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Barang berhasil didaftarkan.');
    }

    public function render()
    {
        $boxes = Box::where('customer_id', auth()->id())
            ->where('status', Box::STATUS_OPEN)
            ->get();

        $sensitiveTypes = ['Elektronik', 'Baterai', 'Cairan', 'Kosmetik', 'Makanan', 'Obat-obatan', 'Magnet', 'Lainnya'];

        return view('livewire.customer.setor-resi.index', compact('boxes', 'sensitiveTypes'))
            ->layout('layouts.app');
    }
}
