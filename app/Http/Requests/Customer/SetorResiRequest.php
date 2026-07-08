<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PRD §11.3: Form Setor Resi
 * PRD §12.5: Upload File validation
 * PRD §13.2: Error Messages
 */
class SetorResiRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'box_id' => ['required', 'exists:boxes,id'],
            'name' => ['required', 'string', 'min:2', 'max:255'],
            'quantity' => ['required', 'integer', 'min:1', 'max:9999'],
            'price_yuan' => ['required', 'numeric', 'min:0.01', 'max:999999'],
            'resi_number' => ['required', 'string', 'min:3', 'max:100'],
            'proof_co' => ['required', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'is_sensitive' => ['nullable', 'boolean'],
            'sensitive_type' => ['required_if:is_sensitive,1', 'nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'box_id.required' => 'Pilih box terlebih dahulu',
            'box_id.exists' => 'Box tidak ditemukan',
            'name.required' => 'Nama barang wajib diisi',
            'name.min' => 'Nama barang minimal 2 karakter',
            'quantity.required' => 'Jumlah wajib diisi',
            'quantity.integer' => 'Jumlah harus berupa angka',
            'quantity.min' => 'Jumlah minimal 1',
            'price_yuan.required' => 'Harga wajib diisi',
            'price_yuan.numeric' => 'Harga harus berupa angka',
            'price_yuan.min' => 'Harga minimal 0.01',
            'resi_number.required' => 'Nomor resi wajib diisi',
            'resi_number.min' => 'Nomor resi minimal 3 karakter',
            'proof_co.required' => 'Foto bukti barang wajib diupload',
            'proof_co.mimes' => 'Format foto harus jpg, png, atau webp',
            'proof_co.max' => 'Ukuran foto maksimal 5MB',
            'sensitive_type.required_if' => 'Pilih jenis sensitive item',
        ];
    }
}
