<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PRD §11.6: Form Komplain
 * PRD §13.5: Error Messages
 */
class KomplainRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => ['required', 'string'],
            'resolution' => ['required', 'string', 'in:refund,replacement'],
            'invoice_number' => ['nullable', 'string', 'max:50'],
            'resi_number' => ['nullable', 'string', 'max:100'],
            'description' => ['required', 'string', 'min:10', 'max:2000'],
            'video_url' => ['nullable', 'file', 'mimes:mp4,mov', 'max:51200'],
            'photo_url' => ['nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'type.required' => 'Pilih jenis komplain',
            'resolution.required' => 'Pilih opsi resolusi',
            'resolution.in' => 'Opsi resolusi tidak valid',
            'description.required' => 'Jelaskan masalah Anda',
            'description.min' => 'Deskripsi minimal 10 karakter',
            'description.max' => 'Deskripsi maksimal 2000 karakter',
            'video_url.mimes' => 'Format video harus mp4 atau mov',
            'video_url.max' => 'Ukuran video maksimal 50MB',
            'photo_url.mimes' => 'Format foto harus jpg atau png',
            'photo_url.max' => 'Ukuran foto maksimal 5MB',
        ];
    }
}
