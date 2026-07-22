<?php

namespace App\Livewire\WhChina;

use App\Models\Box;
use App\Models\CargoDestination;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

#[Layout('layouts.wh-china')]
#[Title('Request to Send — Ting Warehouse')]
class RequestToSend extends Component
{
    use WithFileUploads;

    // ─── Modal State ─────────────────────────────────────────────
    public bool $showSendModal = false;
    public ?int $selectedBoxId = null;
    public ?Box $selectedBox = null;

    // ─── Form Fields ─────────────────────────────────────────────
    public string $cargoResi = '';
    public $cargoPhoto = null;
    public string $berat = '';
    public string $ukuran = '';
    public string $cargoDestination = '';

    public function render()
    {
        $airBoxes = Box::with(['customer', 'items'])
            ->where('method', 'air')
            ->where('status', Box::STATUS_REQUEST_TO_SEND)
            ->orderByDesc('updated_at')
            ->get();

        $seaBoxes = Box::with(['customer', 'items'])
            ->where('method', 'sea')
            ->where('status', Box::STATUS_REQUEST_TO_SEND)
            ->orderByDesc('updated_at')
            ->get();

        $destinations = CargoDestination::getActive();

        return view('livewire.wh-china.request-to-send.index', compact('airBoxes', 'seaBoxes', 'destinations'));
    }

    public function openSendModal(int $boxId): void
    {
        $this->selectedBoxId = $boxId;
        $this->selectedBox = Box::with('customer', 'items')->find($boxId);

        if (!$this->selectedBox) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Box tidak ditemukan.');
            return;
        }

        $this->resetForm();
        $this->showSendModal = true;
    }

    public function closeSendModal(): void
    {
        $this->showSendModal = false;
        $this->resetForm();
        $this->selectedBoxId = null;
        $this->selectedBox = null;
    }

    public function sendToCargo(AuditLogService $auditService): void
    {
        $this->validate([
            'cargoResi' => ['required', 'string', 'max:100'],
            'cargoPhoto' => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'berat' => ['required', 'numeric', 'min:0.01', 'max:99999'],
            'ukuran' => ['required', 'string', 'max:100'],
            'cargoDestination' => ['required', 'string', 'max:10'],
        ]);

        if (!$this->selectedBox) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Box tidak ditemukan.');
            return;
        }

        $photoPath = null;
        if ($this->cargoPhoto) {
            $photoPath = $this->cargoPhoto->store('cargo-photos', 'public');
        }

        $oldStatus = $this->selectedBox->status;

        $this->selectedBox->update([
            'status' => Box::STATUS_SEND_TO_CARGO,
            'cargo_tracking_number' => $this->cargoResi,
            'cargo_destination' => $this->cargoDestination,
            'cargo_photo' => $photoPath,
        ]);

        // Update wh_china_data for items in this box
        foreach ($this->selectedBox->items as $item) {
            if ($item->resi_number) {
                \App\Models\WhChinaData::where('resi_number', $item->resi_number)
                    ->whereNull('matched_at')
                    ->update([
                        'berat' => (float) $this->berat,
                        'ukuran_box' => $this->ukuran,
                    ]);
            }
        }

        $auditService->logCustom(
            $this->selectedBox,
            'status_changed',
            "Box {$this->selectedBox->display_name} dikirim ke cargo ({$this->cargoDestination}) — resi: {$this->cargoResi}",
            ['status' => $oldStatus],
            ['status' => Box::STATUS_SEND_TO_CARGO, 'cargo_resi' => $this->cargoResi, 'destination' => $this->cargoDestination]
        );

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Box {$this->selectedBox->display_name} dikirim ke cargo {$this->cargoDestination}.");

        $this->closeSendModal();
    }

    private function resetForm(): void
    {
        $this->cargoResi = '';
        $this->cargoPhoto = null;
        $this->berat = '';
        $this->ukuran = '';
        $this->cargoDestination = '';
    }
}
