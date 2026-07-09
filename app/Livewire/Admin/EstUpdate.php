<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Services\AuditLogService;
use App\Services\NotificationService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Est Update — Ting Warehouse')]
class EstUpdate extends Component
{
    #[Url]
    public string $search = '';

    public ?int $selectedBoxId = null;
    public string $etd = '';
    public string $eta = '';
    public string $estNote = '';
    public bool $showForm = false;

    public function selectBox(int $id): void
    {
        $box = Box::find($id);
        if (!$box) return;

        $this->selectedBoxId = $id;
        $this->etd = $box->etd?->format('Y-m-d') ?? '';
        $this->eta = $box->eta?->format('Y-m-d') ?? '';
        $this->estNote = '';
        $this->showForm = true;
    }

    public function cancelForm(): void
    {
        $this->showForm = false;
        $this->selectedBoxId = null;
        $this->etd = '';
        $this->eta = '';
        $this->estNote = '';
    }

    public function saveEstimate(NotificationService $notifService, AuditLogService $auditService): void
    {
        $this->validate([
            'etd' => 'nullable|date',
            'eta' => 'nullable|date|after_or_equal:etd',
            'estNote' => 'nullable|string|max:500',
        ]);

        $box = Box::with('customer')->findOrFail($this->selectedBoxId);
        $oldEtd = $box->etd?->format('Y-m-d');
        $oldEta = $box->eta?->format('Y-m-d');

        $box->etd = $this->etd ?: null;
        $box->eta = $this->eta ?: null;
        $box->save();

        $auditService->logCustom(
            $box,
            'estimate_updated',
            "Estimasi diperbarui" . ($this->estNote ? ": {$this->estNote}" : ''),
            ['etd' => $oldEtd, 'eta' => $oldEta],
            ['etd' => $this->etd, 'eta' => $this->eta, 'note' => $this->estNote],
        );

        // Notify customer
        if ($box->customer) {
            $message = "Estimasi pengiriman box {$box->tracking_number} telah diperbarui." .
                ($this->etd ? " ETD: " . \Carbon\Carbon::parse($this->etd)->format('d M Y') : '') .
                ($this->eta ? " ETA: " . \Carbon\Carbon::parse($this->eta)->format('d M Y') : '');

            \App\Models\Notification::create([
                'id' => \Illuminate\Support\Str::uuid()->toString(),
                'notifiable_type' => \App\Models\User::class,
                'notifiable_id' => $box->customer_id,
                'user_id' => $box->customer_id, // Revisi §3.3
                'type' => 'box_status_changed',
                'data' => [
                    'title' => 'Estimasi Diperbarui',
                    'message' => $message,
                    'box_id' => $box->id,
                    'link' => route('dashboard'),
                ],
                'title' => 'Estimasi Diperbarui', // Revisi §3.3
                'message' => $message, // Revisi §3.3
                'is_read' => false, // Revisi §3.3
            ]);
        }

        $this->cancelForm();
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Estimasi berhasil diupdate.');
    }

    public function render()
    {
        $query = Box::with('customer')
            ->whereIn('status', [Box::STATUS_OPEN, Box::STATUS_SENT_TO_CARGO, Box::STATUS_OTW_INA, Box::STATUS_UP_INVOICE]);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('tracking_number', 'like', "%{$this->search}%")
                  ->orWhere('batch_name', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  });
            });
        }

        $boxes = $query->latest()->paginate(15);

        return view('livewire.admin.est-update.index', [
            'boxes' => $boxes,
        ]);
    }
}
