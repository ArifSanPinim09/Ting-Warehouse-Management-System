<?php

namespace App\Http\Requests\Customer;

use Illuminate\Foundation\Http\FormRequest;

/**
 * PRD §11.5: Form Checkout
 * PRD §13.4: Error Messages
 */
class CheckoutRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'invoice_id' => ['required', 'exists:invoices,id'],
            'address_type' => ['required', 'string', 'in:personal,dropship'],
            'recipient_name' => ['required', 'string', 'min:3', 'max:255'],
            'recipient_phone' => ['required', 'string', 'min:10', 'max:15'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'confirmation' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'invoice_id.required' => 'Pilih invoice terlebih dahulu',
            'invoice_id.exists' => 'Invoice tidak ditemukan',
            'address_type.required' => 'Pilih tipe alamat',
            'address_type.in' => 'Tipe alamat tidak valid',
            'recipient_name.required' => 'Nama penerima wajib diisi',
            'recipient_name.min' => 'Nama penerima minimal 3 karakter',
            'recipient_phone.required' => 'Nomor telepon wajib diisi',
            'recipient_phone.min' => 'Nomor telepon minimal 10 digit',
            'recipient_phone.max' => 'Nomor telepon maksimal 15 digit',
            'address.required' => 'Alamat wajib diisi',
            'address.min' => 'Alamat minimal 10 karakter',
            'confirmation.required' => 'Centang konfirmasi terlebih dahulu',
            'confirmation.accepted' => 'Centang konfirmasi terlebih dahulu',
        ];
    }
}
