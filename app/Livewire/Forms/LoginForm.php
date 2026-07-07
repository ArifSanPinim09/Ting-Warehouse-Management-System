<?php

namespace App\Livewire\Forms;

use App\Models\User;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Validate;
use Livewire\Form;

class LoginForm extends Form
{
    #[Validate('required|string|email')]
    public string $email = '';

    #[Validate('required|string')]
    public string $password = '';

    #[Validate('boolean')]
    public bool $remember = false;

    /**
     * Attempt to authenticate the request's credentials.
     *
     * PRD §4.1: User harus aktif, session 120 menit, 5x gagal kunci 15 menit
     * PRD §13.1: Error messages PERSIS sesuai teks
     *
     * @throws ValidationException
     */
    public function authenticate(): void
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only(['email', 'password']), $this->remember)) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'form.email' => 'Email atau password salah',
            ]);
        }

        $user = Auth::user();

        // PRD §13.1: "Akun belum aktif. Hubungi admin."
        if ($user->status === User::STATUS_PENDING) {
            Auth::logout();

            throw ValidationException::withMessages([
                'form.email' => 'Akun belum aktif. Hubungi admin.',
            ]);
        }

        // PRD §13.1: "Akun telah dinonaktifkan."
        if ($user->status === User::STATUS_INACTIVE) {
            Auth::logout();

            throw ValidationException::withMessages([
                'form.email' => 'Akun telah dinonaktifkan.',
            ]);
        }

        RateLimiter::clear($this->throttleKey());
    }

    /**
     * Ensure the authentication request is not rate limited.
     *
     * PRD §4.1: 5x gagal kunci 15 menit
     * PRD §13.1: "Akun terkunci. Coba lagi dalam X menit."
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 5)) {
            return;
        }

        event(new Lockout(request()));

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'form.email' => 'Akun terkunci. Coba lagi dalam '.ceil($seconds / 60).' menit.',
        ]);
    }

    /**
     * Get the authentication rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate(Str::lower($this->email).'|'.request()->ip());
    }
}
