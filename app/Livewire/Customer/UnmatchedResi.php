<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use App\Models\Item;
use App\Models\WhChinaData;
use App\Services\RecapMatchingService;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

/**
 * Resi Belum Dikenali — Customer melihat WH China data yang belum matched.
 *
 * Customer bisa klaim resi dengan mengisi data barang (nama, qty, harga, foto).
 * Sistem lalu buat Item + auto-match ke WhChinaData.
 */
#[Layout('layouts.app')]
#[Title('Resi Belum Dikenali — Ting Warehouse')]
class UnmatchedResi extends Component
{
    use WithFileUploads;

    // ─── Claim Form State ──────────────────────────────────────
    public ?int $selectedWhId = null;
    public ?int $boxId = null;
    public string $itemName = '';
    public int $quantity = 1;
    public string $priceYuan = '';
    public $proofCo = null;
    public bool $isSensitive = false;
    public ?string $sensitiveType = null;

    public bool $showClaimForm = false;
    public bool $submitting = false;

    protected function rules(): array
    {
        return [
            'selectedWhId' => ['required', 'exists:wh_china_data,id'],
            'boxId' => ['required', 'exists:boxes,id'],
            'itemName' => ['required', 'string', 'min:2', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'priceYuan' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'proofCo' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'isSensitive' => ['nullable', 'boolean'],
            'sensitiveType' => ['required_if:isSensitive,true', 'nullable', 'string', 'max:50'],
        ];
    }

    protected function messages(): array
    {
        return [
            'selectedWhId.required' => 'Pilih resi terlebih dahulu',
            'boxId.required' => 'Pilih box terlebih dahulu',
            'itemName.required' => 'Nama barang wajib diisi',
            'itemName.min' => 'Nama barang minimal 2 karakter',
            'quantity.required' => 'Jumlah wajib diisi',
            'priceYuan.required' => 'Harga wajib diisi',
            'priceYuan.numeric' => 'Harga harus berupa angka',
            'proofCo.required' => 'Foto bukti barang wajib diupload',
            'proofCo.mimes' => 'Format foto harus jpg, png, atau webp',
            'proofCo.max' => 'Ukuran foto maksimal 5MB',
            'sensitiveType.required_if' => 'Pilih jenis sensitive item',
        ];
    }

    public function selectResi(int $whId): void
    {
        $wh = WhChinaData::find($whId);

        if (!$wh || $wh->isMatched()) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Resi ini sudah diklaim.');
            return;
        }

        $this->selectedWhId = $whId;
        $this->reset(['boxId', 'itemName', 'quantity', 'priceYuan', 'proofCo', 'isSensitive', 'sensitiveType']);
        $this->showClaimForm = true;
    }

    public function cancelClaim(): void
    {
        $this->reset(['selectedWhId', 'boxId', 'itemName', 'quantity', 'priceYuan', 'proofCo', 'isSensitive', 'sensitiveType', 'showClaimForm']);
    }

    public function submitClaim(RecapMatchingService $matching): void
    {
        $this->validate();
        $this->submitting = true;

        $wh = WhChinaData::find($this->selectedWhId);

        if (!$wh || $wh->isMatched()) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Resi ini sudah diklaim oleh orang lain.');
            $this->cancelClaim();
            $this->submitting = false;
            return;
        }

        // Verify box is OPEN
        $box = Box::find($this->boxId);
        if (!$box || $box->status !== Box::STATUS_OPEN) {
            $this->addError('boxId', 'Box sudah ditutup. Pilih box lain.');
            $this->submitting = false;
            return;
        }

        // Sprint 5B: Admin China DONE — locked box tidak bisa di-claim
        if ($box->is_locked) {
            $this->addError('boxId', 'Batch sudah di-DONE oleh Admin China. Tidak bisa klaim barang.');
            $this->submitting = false;
            return;
        }

        // Upload proof photo (nullable since REV-03.2)
        $proofPath = $this->proofCo ? $this->proofCo->store('proof-co', 'public') : null;

        // Create the Item for this customer
        $item = Item::create([
            'box_id' => $this->boxId,
            'customer_id' => auth()->id(),
            'name' => $this->itemName,
            'quantity' => $this->quantity,
            'price_yuan' => $this->priceYuan,
            'resi_number' => $wh->resi_number,
            'proof_co' => $proofPath,
            'is_sensitive' => $this->isSensitive,
            'sensitive_type' => $this->isSensitive ? $this->sensitiveType : null,
        ]);

        // Update last_setor_date on box
        $box->last_setor_date = now();
        $box->save();

        // Auto-match: link WH China data to this item
        $matching->matchByResi($wh);

        $this->cancelClaim();

        $this->dispatch('toast',
            type: 'success',
            title: 'Berhasil',
            message: "Resi {$wh->resi_number} berhasil diklaim dan terhubung ke data WH China.",
        );

        $this->submitting = false;
    }

    public function render()
    {
        $unmatchedData = WhChinaData::whereNull('item_id')
            ->with('admin')
            ->latest()
            ->paginate(20);

        // PRD §4.3: Sharing box (customer_id NULL) + direct box milik customer
        $openBoxes = Box::where('status', Box::STATUS_OPEN)
            ->where(function ($query) {
                $query->whereNull('customer_id')
                    ->orWhere('customer_id', auth()->id());
            })
            ->orderByDesc('last_setor_date')
            ->get();

        $sensitiveTypes = ['Elektronik', 'Baterai', 'Cairan', 'Kosmetik', 'Makanan', 'Obat-obatan', 'Magnet', 'Lainnya'];

        return view('livewire.customer.unmatched-resi.index', [
            'unmatchedData' => $unmatchedData,
            'openBoxes' => $openBoxes,
            'sensitiveTypes' => $sensitiveTypes,
        ]);
    }
}
