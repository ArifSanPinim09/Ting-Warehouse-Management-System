<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RegisterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * Rules from PRD §4.1, §12.1-12.4, §12.7, §12.8.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:3', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['required', 'numeric', 'digits_between:10,15'],
            'ktp_number' => ['required', 'numeric', 'digits:16', 'unique:users,ktp_number'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }

    /**
     * Get custom error messages for validation errors.
     *
     * Messages PERSIS sesuai PRD §12.1-12.4, §12.7, §12.8.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            // §12.7 Nama
            'name.required' => 'Nama wajib diisi',
            'name.min' => 'Nama minimal 3 karakter',
            'name.max' => 'Nama maksimal 255 karakter',

            // §12.1 Email
            'email.required' => 'Email wajib diisi',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email terlalu panjang',
            'email.unique' => 'Email sudah terdaftar',

            // §12.3 Nomor Telepon
            'phone.required' => 'No telepon wajib diisi',
            'phone.numeric' => 'No telepon harus angka',
            'phone.digits_between' => 'No telepon minimal 10 digit',

            // §12.4 No KTP
            'ktp_number.required' => 'No KTP wajib diisi',
            'ktp_number.numeric' => 'No KTP harus angka',
            'ktp_number.digits' => 'No KTP harus 16 digit',
            'ktp_number.unique' => 'No KTP sudah terdaftar',

            // §12.8 Alamat
            'address.required' => 'Alamat wajib diisi',
            'address.min' => 'Alamat minimal 10 karakter',
            'address.max' => 'Alamat maksimal 500 karakter',

            // §12.2 Password
            'password.required' => 'Password wajib diisi',
            'password.min' => 'Password minimal 8 karakter',
            'password.confirmed' => 'Konfirmasi password tidak cocok',
        ];
    }
}
