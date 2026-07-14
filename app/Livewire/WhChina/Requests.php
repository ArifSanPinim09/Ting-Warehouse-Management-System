<?php

namespace App\Livewire\WhChina;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.wh-china')]
#[Title('Requests — WH China')]
class Requests extends Component
{
    public function render()
    {
        // REV-03.8: Show customer requests (placeholder until REV-04.3 adds request_type column)
        // Currently returns empty — will be populated when customer request feature is implemented.
        $requests = collect();

        return view('livewire.wh-china.requests.index', compact('requests'));
    }
}
