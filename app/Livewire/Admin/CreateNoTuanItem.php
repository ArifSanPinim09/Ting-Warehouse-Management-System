<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Models\Item;
use App\Services\AuditLogService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Create No Tuan Item — Admin input barang langsung sebagai No Tuan.
 *
 * Flow sesuai client (10 Juli 2026):
 * 1. Barang tiba di warehouse tanpa ada yang setor resi
 * 2. Admin input data barang langsung
 * 3. Barang tampil di halaman customer /no-tuan
 * 4. Customer bisa klaim
 *
 * Bedanya dengan setor resi:
 * - Tidak ada resi_number (tidak ada yang setor)
 * - Tidak ada customer_id (belum tahu punya siapa)
 * - Tidak ada price_yuan (belum tahu harga)
 * - Tidak ada proof_co (bukan dari customer)
 * - Langsung status = 'no_tuan'
 */
#[Layout('layouts.admin')]
#[Title('Input Barang No Tuan — Ting Warehouse')]
class CreateNoTuanItem extends Component
{
    use WithFileUploads;

    // ─── Form Fields ────────────────────────────────────────────
    public string $name = '';
    public int $quantity = 1;
    public ?int $boxId = null;
    public string $description = '';
    public $photo = null;
    public string $notes = '';

    // ─── UI State ───────────────────────────────────────────────
    public bool $submitting = false;

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'boxId' => ['required', 'exists:boxes,id'],
            'description' => ['nullable', 'string', 'max:1000'],
            'photo' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'name.required' => 'Nama barang wajib diisi',
            'name.min' => 'Nama barang minimal 2 karakter',
            'quantity.required' => 'Jumlah wajib diisi',
            'quantity.min' => 'Jumlah minimal 1',
            'boxId.required' => 'Pilih box tempat barang ini berada',
            'boxId.exists' => 'Box tidak ditemukan',
            'photo.mimes' => 'Format foto harus jpg atau png',
            'photo.max' => 'Ukuran foto maksimal 5MB',
        ];
    }

    public function submit(AuditLogService $auditService): void
    {
        $this->validate();
        $this->submitting = true;

        $photoPath = null;
        if ($this->photo) {
            $photoPath = $this->photo->store('no-tuan-items', 'public');
        }

        $item = Item::create([
            'box_id' => $this->boxId,
            'customer_id' => null, // No Tuan — tidak ada customer
            'name' => $this->name,
            'quantity' => $this->quantity,
            'price_yuan' => null, // Belum tahu harga
            'resi_number' => null, // Tidak ada yang setor resi
            'proof_co' => $photoPath, // Foto barang dari warehouse
            'is_sensitive' => false,
            'arrived_china' => true, // Barang sudah di warehouse
            'status' => Item::STATUS_NO_TUAN, // Langsung No Tuan
        ]);

        $auditService->logCustom($item, 'no_tuan_created', "Admin input barang No Tuan: '{$item->name}' (qty: {$item->quantity})");

        $this->reset(['name', 'quantity', 'description', 'photo', 'notes']);
        $this->submitting = false;

        $this->dispatch('toast',
            type: 'success',
            title: 'Berhasil',
            message: "Barang '{$item->name}' berhasil ditambahkan sebagai No Tuan. Barang sekarang tampil di halaman customer.",
        );
    }

    public function render()
    {
        // Ambil box yang masih OPEN (bisa setor barang)
        $boxes = Box::whereIn('status', [Box::STATUS_OPEN, Box::STATUS_CLOSED])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.admin.no-tuan.create', [
            'boxes' => $boxes,
        ]);
    }
}
