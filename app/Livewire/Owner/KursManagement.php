<?php

namespace App\Livewire\Owner;

use App\Models\KursHistory;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.admin')]
#[Title('Kurs Management — Ting Warehouse')]
class KursManagement extends Component
{
    use WithPagination;

    public string $kursValue = '';
    public string $effectiveDate = '';
    public bool $showForm = false;

    public function render()
    {
        $history = KursHistory::with('inputBy')
            ->latest('effective_date')
            ->paginate(15);

        $currentKurs = KursHistory::getLatest();

        return view('livewire.owner.kurs-management.index', compact('history', 'currentKurs'));
    }

    public function openForm(): void
    {
        $this->kursValue = '';
        $this->effectiveDate = now()->format('Y-m-d');
        $this->showForm = true;
    }

    public function closeForm(): void
    {
        $this->showForm = false;
        $this->resetValidation();
    }

    public function save(AuditLogService $auditService): void
    {
        $this->validate([
            'kursValue' => ['required', 'numeric', 'min:1', 'max:99999'],
            'effectiveDate' => ['required', 'date'],
        ]);

        $kurs = KursHistory::create([
            'kurs_value' => $this->kursValue,
            'effective_date' => $this->effectiveDate,
            'input_by' => auth()->id(),
        ]);

        $auditService->logCustom($kurs, 'kurs_updated', "Kurs Yuan→IDR diupdate: Rp " . number_format($this->kursValue, 0, ',', '.') . " (efektif {$this->effectiveDate})");

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Kurs berhasil diupdate.');

        $this->closeForm();
    }
}
