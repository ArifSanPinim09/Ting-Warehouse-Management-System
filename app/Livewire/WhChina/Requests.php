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
        $requests = \App\Models\Item::whereNotNull('request_type')
            ->where('request_type', '!=', '[]')
            ->where('request_type', '!=', '')
            ->with(['box', 'customer'])
            ->latest()
            ->get();

        return view('livewire.wh-china.requests.index', compact('requests'));
    }
}
