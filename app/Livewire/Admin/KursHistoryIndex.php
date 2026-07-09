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
 * Duplicate (kurs_value, effective_date) ditolak sesuai §8.1.
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

    public function toggleForm(): void
    {
        $this->showForm = ! $this->showForm;
        if (! $this->showForm) {
            $this->resetForm();
        }
    }

    public function saveKurs(AuditLogService $auditService): void
    {
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
        $exists = KursHistory::where('kurs_value', $this->kurs_value)
            ->whereDate('effective_date', $this->effective_date)
            ->exists();

        if ($exists) {
            $this->dispatch('toast',
                type: 'error',
                title: 'Error',
                message: 'Kurs untuk tanggal ini sudah ada.',
            );
            return;
        }

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
