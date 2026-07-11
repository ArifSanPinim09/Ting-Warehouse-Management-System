<?php

namespace App\Livewire\Customer;

use App\Models\Complain;
use App\Services\NotificationService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

/**
 * Komplain — §4.7, §8.10, §11.6
 *
 * Ajukan dan track komplain.
 * Trigger NotificationService on submit.
 */
class KomplainIndex extends Component
{
    use WithPagination, WithFileUploads;

    public string $filterStatus = '';

    // Form state
    public bool $showForm = false;
    public string $type = '';
    public string $resolution = '';
    public string $invoiceNumber = '';
    public string $resiNumber = '';
    public string $description = '';
    public $videoFile = null;
    public $photoFile = null;
    public bool $submitting = false;

    public function updatingFilterStatus(): void { $this->resetPage(); }

    public function openForm(): void
    {
        $this->showForm = true;
        $this->resetValidation();
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->reset(['type', 'resolution', 'invoiceNumber', 'resiNumber', 'description', 'videoFile', 'photoFile']);
        $this->resetValidation();
    }

    public function submit(NotificationService $notifService): void
    {
        $this->validate([
            'type' => ['required', 'string'],
            'resolution' => ['required', 'in:refund,replacement'],
            'invoiceNumber' => ['nullable', 'string', 'max:50'],
            'resiNumber' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'videoFile' => ['nullable', 'file', 'mimes:mp4,mov', 'max:51200'],
            'photoFile' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ], [
            'type.required' => 'Pilih jenis komplain',
            'resolution.required' => 'Pilih opsi resolusi',
            'description.required' => 'Jelaskan masalah Anda',
            'description.min' => 'Deskripsi minimal 10 karakter',
            'videoFile.mimes' => 'Format video harus mp4 atau mov',
            'videoFile.max' => 'Ukuran video maksimal 50MB',
            'photoFile.mimes' => 'Format foto harus jpg atau png',
            'photoFile.max' => 'Ukuran foto maksimal 5MB',
        ]);

        $this->submitting = true;

        $videoPath = null;
        $photoPath = null;

        try {
            if ($this->videoFile) {
                $videoPath = $this->videoFile->store('complaints/video', 'public');
            }
            if ($this->photoFile) {
                $photoPath = $this->photoFile->store('complaints/photo', 'public');
            }
        } catch (\Exception $e) {
            $this->submitting = false;
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Gagal upload file: ' . $e->getMessage());
            return;
        }

        $complaint = Complain::create([
            'customer_id' => auth()->id(),
            'box_id' => null,
            'type' => $this->type,
            'resolution' => $this->resolution,
            'invoice_number' => $this->invoiceNumber ?: null,
            'resi_number' => $this->resiNumber ?: null,
            'description' => $this->description,
            'video_url' => $videoPath,
            'photo_url' => $photoPath,
            'status' => Complain::STATUS_OPEN,
        ]);

        // Notify admin
        $notifService->newComplaint($complaint);

        $this->closeForm();
        $this->submitting = false;

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Komplain berhasil diajukan.');
    }

    public function render()
    {
        $complaints = Complain::where('customer_id', auth()->id())
            ->when($this->filterStatus, function ($query) {
                $query->where('status', $this->filterStatus);
            })
            ->latest()
            ->paginate(10);

        $complaintTypes = [
            'Kurang Barang Ekspedisi',
            'Tidak Arrived China',
            'Tidak Arrived Indonesia',
            'Kurang Barang China',
            'Kurang Barang Indonesia',
            'Barang Rusak',
            'Barang Salah',
            'Lainnya',
        ];

        return view('livewire.customer.komplain.index', compact('complaints', 'complaintTypes'))
            ->layout('layouts.app');
    }
}
