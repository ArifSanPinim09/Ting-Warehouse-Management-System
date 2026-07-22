<?php

namespace App\Livewire\WhChina;

use App\Models\GoodsWeightFee;
use App\Models\Box;
use App\Models\Setting;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wh-china')]
#[Title('Goods Weight Fees — Ting Warehouse')]
class GoodsWeightFees extends Component
{
    use WithPagination;

    // ─── Form Fields ─────────────────────────────────────────────
    public ?int $editId = null;
    public ?int $boxId = null;
    public string $hurufBox = '';
    public string $beratKg = '';
    public string $biayaYuan = '';
    public string $status = 'UNPAID';
    public string $notes = '';

    public function render()
    {
        $fees = GoodsWeightFee::with(['box', 'inputBy'])
            ->latest()
            ->paginate(15);

        $boxes = Box::whereIn('status', [
            Box::STATUS_SEND_TO_CARGO,
            Box::STATUS_ARRIVED_AT_CARGO,
            Box::STATUS_WAITING_FOR_DEPARTURE,
            Box::STATUS_DEPARTURE,
            Box::STATUS_ARRIVED_INA,
        ])
            ->orderByDesc('batch_name')
            ->get();

        $kurs = (float) Setting::getValue('kurs_yuan_idr', 2460);

        $totalBerat = GoodsWeightFee::sum('berat_kg');
        $totalYuan = GoodsWeightFee::sum('biaya_yuan');
        $totalRupiah = $totalYuan * $kurs;

        return view('livewire.wh-china.goods-weight-fees.index', compact('fees', 'boxes', 'kurs', 'totalBerat', 'totalYuan', 'totalRupiah'));
    }

    public function updatedBoxId($value): void
    {
        if ($value) {
            $box = Box::find($value);
            $this->hurufBox = $box?->huruf_box ?? '';
        } else {
            $this->hurufBox = '';
        }
    }

    public function save(AuditLogService $auditService): void
    {
        $this->validate([
            'boxId' => ['nullable', 'exists:boxes,id'],
            'hurufBox' => ['nullable', 'string', 'max:10'],
            'beratKg' => ['required', 'numeric', 'min:0.01', 'max:99999'],
            'biayaYuan' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'status' => ['required', 'in:PAID,UNPAID'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $data = [
            'box_id' => $this->boxId,
            'huruf_box' => $this->hurufBox ?: null,
            'berat_kg' => $this->beratKg,
            'biaya_yuan' => $this->biayaYuan,
            'status' => $this->status,
            'notes' => $this->notes ?: null,
            'input_by' => auth()->id(),
        ];

        if ($this->editId) {
            $fee = GoodsWeightFee::findOrFail($this->editId);
            $oldValues = $fee->only(['box_id', 'huruf_box', 'berat_kg', 'biaya_yuan', 'status', 'notes']);
            $fee->update($data);
            $auditService->logCustom($fee, 'updated', "Goods weight fee updated", $oldValues, $data);
            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Fee berhasil diupdate.');
        } else {
            $fee = GoodsWeightFee::create($data);
            $auditService->logCustom($fee, 'created', "Goods weight fee created", [], $data);
            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Fee berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $fee = GoodsWeightFee::findOrFail($id);
        $this->editId = $fee->id;
        $this->boxId = $fee->box_id;
        $this->hurufBox = $fee->huruf_box ?? '';
        $this->beratKg = (string) $fee->berat_kg;
        $this->biayaYuan = (string) $fee->biaya_yuan;
        $this->status = $fee->status;
        $this->notes = $fee->notes ?? '';
    }

    public function delete(int $id, AuditLogService $auditService): void
    {
        $fee = GoodsWeightFee::findOrFail($id);
        $auditService->logCustom($fee, 'deleted', "Goods weight fee deleted", $fee->toArray(), []);
        $fee->delete();
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Fee berhasil dihapus.');
    }

    public function toggleStatus(int $id): void
    {
        $fee = GoodsWeightFee::findOrFail($id);
        $fee->update(['status' => $fee->status === 'PAID' ? 'UNPAID' : 'PAID']);
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Status berhasil diubah.');
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->boxId = null;
        $this->hurufBox = '';
        $this->beratKg = '';
        $this->biayaYuan = '';
        $this->status = 'UNPAID';
        $this->notes = '';
    }
}
