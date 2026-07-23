<?php

namespace App\Livewire\Admin;

use App\Models\KursHistory;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Pengaturan Kurs History — Revisi §2.2, §4.1, §7.2
 *
 * Admin/Owner bisa input kurs + tanggal effective.
 * ONE kurs per date only — duplicate dates ditolak sesuai §8.1.
 */
#[Layout('layouts.admin')]
#[Title('History Kurs — Ting Warehouse')]
class KursHistoryIndex extends Component
{
    use WithPagination;

    // ─── Form Fields (§7.2) ─────────────────────────────────────
    public string $kurs_value = '';
    public string $effective_date = '';

    // ─── UI State ───────────────────────────────────────────────
    public bool $showForm = false;
    public ?int $editingId = null;

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->resetForm();
        }
    }

    /**
     * Load kurs data into form for editing.
     */
    public function editKurs(int $id): void
    {
        // Only owner can edit kurs (Flow Website: hanya Owner bisa ubah rate)
        if (auth()->user()->role !== 'owner') {
            $this->dispatch('toast', type: 'error', title: 'Akses Ditolak', message: 'Hanya Owner yang dapat mengubah kurs.');
            return;
        }

        $kurs = KursHistory::findOrFail($id);
        $this->editingId = $kurs->id;
        $this->kurs_value = (string) $kurs->kurs_value;
        $this->effective_date = $kurs->effective_date->format('Y-m-d');
        $this->showForm = true;
    }

    public function saveKurs(AuditLogService $auditService): void
    {
        // Only owner can save kurs (Flow Website: hanya Owner bisa ubah rate)
        if (auth()->user()->role !== 'owner') {
            $this->dispatch('toast', type: 'error', title: 'Akses Ditolak', message: 'Hanya Owner yang dapat menyimpan kurs.');
            return;
        }

        $this->validate([
            'kurs_value' => 'required|numeric|min:1|max:99999',
            'effective_date' => 'required|date|before_or_equal:today',
        ], [
            'kurs_value.required' => 'Masukkan nilai kurs',
            'kurs_value.numeric' => 'Kurs harus berupa angka',
            'kurs_value.min' => 'Kurs minimal 1',
            'effective_date.required' => 'Masukkan tanggal berlaku',
            'effective_date.before_or_equal' => 'Tanggal tidak boleh di masa depan',
        ]);

        // Check duplicate — §8.1: "Kurs untuk tanggal ini sudah ada."
        // ONE kurs per date only (except when editing same record)
        $existingKurs = KursHistory::whereDate('effective_date', $this->effective_date)
            ->when($this->editingId, fn ($q) => $q->where('id', '!=', $this->editingId))
            ->first();

        if ($existingKurs) {
            $formattedKurs = 'Rp ' . number_format($existingKurs->kurs_value, 0, ',', '.');
            $this->addError('effective_date', "Kurs untuk tanggal ini sudah ada ({$formattedKurs}). Klik tombol Edit untuk mengubahnya.");
            return;
        }

        if ($this->editingId) {
            // Update existing
            $kurs = KursHistory::findOrFail($this->editingId);
            $oldValues = ['kurs_value' => $kurs->kurs_value, 'effective_date' => $kurs->effective_date->format('Y-m-d')];
            $kurs->update([
                'kurs_value' => (float) $this->kurs_value,
                'effective_date' => $this->effective_date,
            ]);
            $auditService->logCustom(
                $kurs,
                'kurs_updated',
                "Kurs diupdate: Rp {$this->kurs_value} untuk tanggal {$this->effective_date}",
            );
        } else {
            // Create new
            $kurs = KursHistory::create([
                'kurs_value' => (float) $this->kurs_value,
                'effective_date' => $this->effective_date,
                'input_by' => auth()->id(),
            ]);
            $auditService->logCustom(
                $kurs,
                'kurs_created',
                "Kurs baru: Rp {$this->kurs_value} untuk tanggal {$this->effective_date}",
            );
        }

        $this->resetForm();
        $this->showForm = false;

        $this->dispatch('toast',
            type: 'success',
            title: 'Berhasil',
            message: 'Kurs berhasil diupdate.',
        );
    }

    private function resetForm(): void
    {
        $this->kurs_value = '';
        $this->effective_date = '';
        $this->editingId = null;
    }

    public function render()
    {
        $history = KursHistory::with('inputBy')
            ->orderByDesc('effective_date')
            ->orderByDesc('created_at')
            ->paginate(20);

        $latestKurs = KursHistory::getLatest();

        return view('livewire.admin.kurs-history.index', [
            'history' => $history,
            'latestKurs' => $latestKurs,
        ]);
    }
}
