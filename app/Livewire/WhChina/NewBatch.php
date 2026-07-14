<?php

namespace App\Livewire\WhChina;

use App\Models\Box;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.wh-china')]
#[Title('New Batch — WH China')]
class NewBatch extends Component
{
    use WithPagination;

    // ─── Form State ─────────────────────────────────────────────
    public string $batchName = '';
    public string $hurufBox = '';
    public string $openDate = '';
    public string $closeDate = '';
    public string $method = 'air';
    public string $type = 'sharing';

    public function createBatch(): void
    {
        $this->validate([
            'batchName' => 'required|string|max:100',
            'hurufBox' => 'nullable|string|max:10',
            'openDate' => 'required|date',
            'closeDate' => 'nullable|date|after_or_equal:openDate',
            'method' => 'required|in:air,sea',
            'type' => 'required|in:sharing,direct',
        ], [
            'batchName.required' => 'Batch name is required.',
            'openDate.required' => 'Open date is required.',
            'closeDate.after_or_equal' => 'Close date must be on or after open date.',
            'method.required' => 'Method is required.',
            'type.required' => 'Type is required.',
        ]);

        Box::create([
            'batch_name' => $this->batchName,
            'huruf_box' => $this->hurufBox ?: null,
            'open_date' => $this->openDate,
            'close_date' => $this->closeDate ?: null,
            'method' => $this->method,
            'type' => $this->type,
            'status' => Box::STATUS_OPEN,
        ]);

        $this->reset(['batchName', 'hurufBox', 'openDate', 'closeDate']);
        $this->dispatch('toast', type: 'success', title: 'Batch Created', message: 'New batch created successfully.');
    }

    public function render()
    {
        $batches = Box::whereNotNull('batch_name')
            ->latest()
            ->paginate(20);

        return view('livewire.wh-china.new-batch.index', compact('batches'));
    }
}
