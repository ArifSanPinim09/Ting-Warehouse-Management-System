<?php

namespace App\Livewire\WhChina;

use App\Models\ShippingMaterialFee;
use App\Models\Box;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wh-china')]
#[Title('Shipping & Material Fees — Ting Warehouse')]
class ShippingMaterialFees extends Component
{
    use WithPagination;

    // ─── Form Fields ─────────────────────────────────────────────
    public ?int $editId = null;
    public string $category = '';
    public string $name = '';
    public string $biayaYuan = '';
    public string $status = 'UNPAID';
    public ?int $boxId = null;
    public string $notes = '';

    public function render()
    {
        $fees = ShippingMaterialFee::with(['box', 'inputBy'])
            ->latest()
            ->paginate(15);

        $boxes = Box::whereIn('status', [Box::STATUS_OPEN, Box::STATUS_CLOSED, Box::STATUS_REQUEST_TO_SEND])
            ->orderByDesc('batch_name')
            ->get();

        $totalYuan = ShippingMaterialFee::sum('biaya_yuan');
        $totalRupiah = $totalYuan * (float) \App\Models\Setting::getValue('kurs_yuan_idr', 2460);

        return view('livewire.wh-china.shipping-material-fees.index', compact('fees', 'boxes', 'totalYuan', 'totalRupiah'));
    }

    public function save(AuditLogService $auditService): void
    {
        $this->validate([
            'category' => ['required', 'string', 'in:shipping,material,operational,other'],
            'name' => ['required', 'string', 'max:255'],
            'biayaYuan' => ['required', 'numeric', 'min:0', 'max:9999999'],
            'status' => ['required', 'in:PAID,UNPAID'],
            'boxId' => ['nullable', 'exists:boxes,id'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $data = [
            'category' => $this->category,
            'name' => $this->name,
            'biaya_yuan' => $this->biayaYuan,
            'status' => $this->status,
            'box_id' => $this->boxId,
            'notes' => $this->notes ?: null,
            'input_by' => auth()->id(),
        ];

        if ($this->editId) {
            $fee = ShippingMaterialFee::findOrFail($this->editId);
            $oldValues = $fee->only(['category', 'name', 'biaya_yuan', 'status', 'box_id', 'notes']);
            $fee->update($data);
            $auditService->logCustom($fee, 'updated', "Shipping/Material fee updated: {$this->name}", $oldValues, $data);
            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Fee berhasil diupdate.');
        } else {
            $fee = ShippingMaterialFee::create($data);
            $auditService->logCustom($fee, 'created', "Shipping/Material fee created: {$this->name}", [], $data);
            $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Fee berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $fee = ShippingMaterialFee::findOrFail($id);
        $this->editId = $fee->id;
        $this->category = $fee->category;
        $this->name = $fee->name;
        $this->biayaYuan = (string) $fee->biaya_yuan;
        $this->status = $fee->status;
        $this->boxId = $fee->box_id;
        $this->notes = $fee->notes ?? '';
    }

    public function delete(int $id, AuditLogService $auditService): void
    {
        $fee = ShippingMaterialFee::findOrFail($id);
        $auditService->logCustom($fee, 'deleted', "Shipping/Material fee deleted: {$fee->name}", $fee->toArray(), []);
        $fee->delete();
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Fee berhasil dihapus.');
    }

    public function toggleStatus(int $id): void
    {
        $fee = ShippingMaterialFee::findOrFail($id);
        $fee->update(['status' => $fee->status === 'PAID' ? 'UNPAID' : 'PAID']);
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Status berhasil diubah.');
    }

    public function resetForm(): void
    {
        $this->editId = null;
        $this->category = '';
        $this->name = '';
        $this->biayaYuan = '';
        $this->status = 'UNPAID';
        $this->boxId = null;
        $this->notes = '';
    }
}
