<?php

namespace App\Livewire\WhChina;

use App\Models\KursHistory;
use App\Models\WhChinaData;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.wh-china')]
#[Title('Dashboard — WH China')]
class Dashboard extends Component
{
    use WithFileUploads, WithPagination;

    // ─── Form State ─────────────────────────────────────────────
    public bool $showModal = false;
    public string $resiNumber = '';
    public string $hurufBox = '';
    public string $berat = '';
    public string $panjang = '';
    public string $lebar = '';
    public string $tinggi = '';
    public string $serviceFeeYuan = '';
    public $fotoArrivedChina = []; // multi-upload
    public ?int $editingId = null;

    // ─── Kurs ───────────────────────────────────────────────────
    public float $kursYuan = 0;

    // ─── Search ─────────────────────────────────────────────────
    public string $search = '';

    public function mount(): void
    {
        $this->loadKurs();
    }

    private function loadKurs(): void
    {
        $latest = KursHistory::getLatest();
        $this->kursYuan = $latest ? (float) $latest->kurs_value : 0;
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Computed: IDR equivalent of entered Yuan amount.
     */
    public function getServiceFeeIdrProperty(): ?float
    {
        if ($this->serviceFeeYuan === '' || $this->kursYuan <= 0) {
            return null;
        }
        return (float) $this->serviceFeeYuan * $this->kursYuan;
    }

    /**
     * Open modal for new input.
     */
    public function openModal(): void
    {
        $this->resetForm();
        $this->showModal = true;
    }

    /**
     * Close modal.
     */
    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    /**
     * Submit (create or update) WH China data.
     */
    public function submitData(): void
    {
        $this->validate([
            'resiNumber' => 'required|string|max:100',
            'hurufBox' => 'required|string|max:10',
            'berat' => 'nullable|numeric|min:0.01',
            'panjang' => 'nullable|numeric|min:0.01',
            'lebar' => 'nullable|numeric|min:0.01',
            'tinggi' => 'nullable|numeric|min:0.01',
            'serviceFeeYuan' => 'required|numeric|min:0',
            'fotoArrivedChina.*' => 'nullable|image|max:5120',
        ], [
            'resiNumber.required' => 'Resi number is required.',
            'hurufBox.required' => 'Box letter is required.',
            'serviceFeeYuan.required' => 'Service fee is required.',
            'serviceFeeYuan.numeric' => 'Service fee must be a number.',
            'berat.numeric' => 'Weight must be a number.',
            'panjang.numeric' => 'Length must be a number.',
            'lebar.numeric' => 'Width must be a number.',
            'tinggi.numeric' => 'Height must be a number.',
        ]);

        // Calculate volume if dimensions provided
        $volume = null;
        if ($this->panjang && $this->lebar && $this->tinggi) {
            $volume = ($this->panjang * $this->lebar * $this->tinggi) / 6000;
        }

        // Convert Yuan → IDR
        $biayaJasaIdr = (float) $this->serviceFeeYuan * $this->kursYuan;

        // Handle photo uploads
        $fotoPaths = [];
        if (!empty($this->fotoArrivedChina)) {
            foreach ($this->fotoArrivedChina as $foto) {
                if ($foto) {
                    $fotoPaths[] = $foto->store('wh-china', 'public');
                }
            }
        }

        if ($this->editingId) {
            // UPDATE
            $whData = WhChinaData::findOrFail($this->editingId);

            // Delete old photos if new ones uploaded
            if (!empty($fotoPaths) && $whData->foto_arrived_china) {
                Storage::disk('public')->delete($whData->foto_arrived_china);
            }

            $whData->update([
                'resi_number' => $this->resiNumber,
                'huruf_box' => $this->hurufBox,
                'berat' => $this->berat !== '' ? (float) $this->berat : null,
                'panjang' => $this->panjang !== '' ? (float) $this->panjang : null,
                'lebar' => $this->lebar !== '' ? (float) $this->lebar : null,
                'tinggi' => $this->tinggi !== '' ? (float) $this->tinggi : null,
                'volume' => $volume ?? $whData->volume,
                'biaya_jasa' => $biayaJasaIdr,
                'foto_arrived_china' => !empty($fotoPaths) ? $fotoPaths[0] : $whData->foto_arrived_china,
            ]);
        } else {
            // CREATE
            WhChinaData::create([
                'resi_number' => $this->resiNumber,
                'huruf_box' => $this->hurufBox,
                'berat' => $this->berat !== '' ? (float) $this->berat : null,
                'panjang' => $this->panjang !== '' ? (float) $this->panjang : null,
                'lebar' => $this->lebar !== '' ? (float) $this->lebar : null,
                'tinggi' => $this->tinggi !== '' ? (float) $this->tinggi : null,
                'volume' => $volume,
                'biaya_jasa' => $biayaJasaIdr,
                'foto_arrived_china' => $fotoPaths[0] ?? null,
                'input_by' => auth()->id(),
            ]);
        }

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('toast', type: 'success', title: 'Success', message: 'Data saved successfully.');
    }

    /**
     * Load data into form for editing.
     */
    public function editData(int $id): void
    {
        $whData = WhChinaData::findOrFail($id);
        $this->editingId = $whData->id;
        $this->resiNumber = $whData->resi_number;
        $this->hurufBox = $whData->huruf_box ?? '';
        $this->berat = $whData->berat !== null ? (string) $whData->berat : '';
        $this->panjang = $whData->panjang !== null ? (string) $whData->panjang : '';
        $this->lebar = $whData->lebar !== null ? (string) $whData->lebar : '';
        $this->tinggi = $whData->tinggi !== null ? (string) $whData->tinggi : '';

        // Convert IDR back to Yuan for display
        $this->serviceFeeYuan = ($whData->biaya_jasa !== null && $this->kursYuan > 0)
            ? (string) round($whData->biaya_jasa / $this->kursYuan, 2)
            : '';
        $this->fotoArrivedChina = [];

        // Open modal for editing
        $this->showModal = true;
        $this->dispatch('scrollToTop');
    }

    /**
     * Delete a WH China data record.
     */
    public function deleteData(int $id): void
    {
        $whData = WhChinaData::findOrFail($id);

        // Clean up photos
        if ($whData->foto_arrived_china) {
            Storage::disk('public')->delete($whData->foto_arrived_china);
        }

        $whData->delete();
        $this->dispatch('toast', type: 'success', title: 'Deleted', message: 'Record deleted successfully.');
    }

    /**
     * Cancel editing and reset form.
     */
    public function cancelEdit(): void
    {
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->resiNumber = '';
        $this->hurufBox = '';
        $this->berat = '';
        $this->panjang = '';
        $this->lebar = '';
        $this->tinggi = '';
        $this->serviceFeeYuan = '';
        $this->fotoArrivedChina = [];
    }

    public function render()
    {
        $query = WhChinaData::query()
            ->when($this->search, function ($q) {
                $q->where('resi_number', 'like', "%{$this->search}%")
                  ->orWhere('huruf_box', 'like', "%{$this->search}%");
            })
            ->latest();

        $records = $query->paginate(20);

        return view('livewire.wh-china.dashboard.index', compact('records'));
    }
}
