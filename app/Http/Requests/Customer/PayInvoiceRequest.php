<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PRD §11.4: Form Bayar Invoice
 * PRD §13.3: Error Messages
 */
class PayInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_method' => ['required', 'string', 'in:transfer,qris'],
            'payment_proof' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.required' => 'Pilih metode pembayaran',
            'payment_method.in' => 'Metode pembayaran tidak valid',
            'payment_proof.required' => 'Upload bukti transfer',
            'payment_proof.mimes' => 'Format foto harus jpg atau png',
            'payment_proof.max' => 'Ukuran foto maksimal 5MB',
        ];
    }
}
