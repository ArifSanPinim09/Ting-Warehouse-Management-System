<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWhChinaDataRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // authorization handled by route middleware (role:admin,owner)
    }

    public function rules(): array
    {
        return [
            'resi_number' => ['required', 'string', 'max:100'],
            'berat' => ['required', 'numeric', 'min:0.01'],
            'ukuran_box' => ['required', 'string', 'max:100'],
            'biaya_jasa' => ['nullable', 'numeric', 'min:0'],
            'foto_barang' => ['nullable', 'image', 'max:5120'], // 5MB
        ];
    }
}
