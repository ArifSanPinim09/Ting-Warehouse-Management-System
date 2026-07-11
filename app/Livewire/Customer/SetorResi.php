<?php

namespace App\Livewire\Customer;

use App\Http\Requests\Customer\SetorResiRequest;
use App\Models\Box;
use App\Models\Item;
use App\Models\WhChinaData;
use App\Services\NotificationService;
use App\Services\RecapMatchingService;
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
    // ─── Revisi Client: Add On & Catatan ────────────────────
    public string $addOn = '0';
    public string $notes = '';
    // ─── UI State ───────────────────────────────────────────────
    public bool $showSuccess = false;
    public bool $submitting = false;

    // ─── WH China Match Indicator ──────────────────────────────
    public ?array $whMatchInfo = null;

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
            'addOn' => ['nullable', 'numeric', 'min:0', 'max:999999'],
            'notes' => ['nullable', 'string', 'max:500'],
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

    // ─── Real-time WH China Match Check ────────────────────────
    public function updatedResiNumber(): void
    {
        $this->whMatchInfo = null;

        if (strlen($this->resiNumber) < 3) {
            return;
        }

        $wh = \App\Models\WhChinaData::where('resi_number', $this->resiNumber)
            ->whereNull('item_id')
            ->first();

        if ($wh) {
            $this->whMatchInfo = [
                'berat' => $wh->berat,
                'ukuran' => $wh->ukuran_box,
                'foto' => $wh->foto_barang ? \Illuminate\Support\Facades\Storage::url($wh->foto_barang) : null,
                'tanggal' => $wh->created_at->format('d M Y'),
            ];
        }
    }

    public function submit(NotificationService $notifService): void
    {
        $this->validate();
        $this->submitting = true;

        // Verify box is OPEN — Revisi §8.1: "Box sudah ditutup. Tidak bisa menambah barang."
        $box = Box::find($this->boxId);
        if (!$box || $box->status !== Box::STATUS_OPEN) {
            $this->addError('boxId', 'Box sudah ditutup. Tidak bisa menambah barang.');
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
            'add_on' => $this->addOn ?: 0,
            'notes' => $this->notes ?: null,
        ]);

        // Update last_setor_date on box (Revisi §2.3)
        $box->last_setor_date = now();
        $box->save();

        // Auto-match: if WH China data already exists for this resi, link it now
        $whData = WhChinaData::where('resi_number', $this->resiNumber)
            ->whereNull('item_id')
            ->first();
        if ($whData) {
            app(RecapMatchingService::class)->matchByResi($whData);
        }

        // Trigger notification to admin
        $notifService->customerRegister(auth()->user());

        // Reset form
        $this->reset(['name', 'quantity', 'priceYuan', 'resiNumber', 'proofCo', 'isSensitive', 'sensitiveType', 'addOn', 'notes']);
        $this->showSuccess = true;
        $this->submitting = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Barang berhasil didaftarkan.');
    }

    public function render()
    {
        // PRD §4.3: Sharing box bisa dipakai semua customer (customer_id = NULL)
        // Direct box hanya untuk customer tertentu (customer_id = auth user)
        $userId = auth()->id();
        $boxes = Box::where('status', Box::STATUS_OPEN)
            ->where(function ($query) use ($userId) {
                // Box sharing (customer_id NULL) → semua customer bisa lihat
                $query->whereNull('customer_id')
                    // Box direct → hanya milik customer ini
                    ->orWhere('customer_id', $userId);
            })
            ->orderByDesc('last_setor_date')
            ->get();

        $sensitiveTypes = ['Elektronik', 'Baterai', 'Cairan', 'Kosmetik', 'Makanan', 'Obat-obatan', 'Magnet', 'Garment', 'Lainnya'];

        return view('livewire.customer.setor-resi.index', compact('boxes', 'sensitiveTypes'))
            ->layout('layouts.app');
    }
}
