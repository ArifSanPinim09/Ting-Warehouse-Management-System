<?php

namespace App\Livewire\Customer;

use App\Services\FeeCalculationService;
use Livewire\Component;

/**
 * Kalkulator Biaya — §4.8
 *
 * WAJIB menggunakan FeeCalculationService dari Fase 3.
 */
class Kalkulator extends Component
{
    public string $method = 'air';
    public string $type = 'sharing';
    public string $weight = '';
    public string $length = '';
    public string $width = '';
    public string $height = '';
    public bool $isSensitive = false;
    public bool $isGarment = false; // Flow Website: Garment option

    public ?array $result = null;
    public bool $calculated = false;

    public function calculate(FeeCalculationService $feeService): void
    {
        $this->validate([
            'weight' => ['required', 'numeric', 'min:0.1', 'max:99999'],
            'length' => ['required', 'numeric', 'min:1', 'max:999'],
            'width' => ['required', 'numeric', 'min:1', 'max:999'],
            'height' => ['required', 'numeric', 'min:1', 'max:999'],
        ], [
            'weight.required' => 'Masukkan berat barang',
            'weight.numeric' => 'Berat harus berupa angka',
            'weight.min' => 'Berat minimal 0.1 kg',
            'length.required' => 'Masukkan panjang',
            'width.required' => 'Masukkan lebar',
            'height.required' => 'Masukkan tinggi',
        ]);

        $this->result = $feeService->calculate(
            type: $this->type,
            method: $this->method,
            weight: (float) $this->weight,
            length: (float) $this->length,
            width: (float) $this->width,
            height: (float) $this->height,
            isSensitive: $this->isSensitive,
            isGarment: $this->isGarment, // Flow Website: pass garment flag
        );

        $this->calculated = true;
    }

    public function resetForm(): void
    {
        $this->reset(['weight', 'length', 'width', 'height', 'isSensitive', 'isGarment', 'result', 'calculated']);
    }

    public function render()
    {
        return view('livewire.customer.kalkulator.index')
            ->layout('layouts.app');
    }
}
