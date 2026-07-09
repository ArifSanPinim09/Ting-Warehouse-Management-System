<?php

use App\Http\Requests\RegisterRequest;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $ktp_number = '';
    public string $address = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     *
     * PRD §4.1: Validasi -> Simpan(PENDING) -> Notif admin -> Aktivasi -> Login
     * PRD §20.5: Register rate limit 3x per hour
     * User TIDAK langsung login setelah register, harus tunggu aktivasi admin.
     */
    public function register(): void
    {
        // PRD §20.5: Rate limiting for registration
        $this->ensureIsNotRateLimited();

        // Hit rate limiter BEFORE processing (counts all attempts, successful or not)
        RateLimiter::hit($this->throttleKey(), 3600); // 1 hour window

        $validated = $this->validate((new RegisterRequest())->rules(), (new RegisterRequest())->messages());

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = User::STATUS_PENDING;
        $validated['role'] = 'customer';

        event(new Registered($user = User::create($validated)));

        // Notify admins about new registration (PRD §4.1)
        app(NotificationService::class)->customerRegister($user);

        // Tidak login otomatis — customer harus tunggu aktivasi admin (PRD §4.1)
        $this->redirect(route('login', absolute: false), navigate: true);
    }

    /**
     * Ensure the registration request is not rate limited.
     *
     * PRD §20.5: 3 registrations per hour per IP.
     */
    protected function ensureIsNotRateLimited(): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey(), 3)) {
            return;
        }

        $seconds = RateLimiter::availableIn($this->throttleKey());

        throw ValidationException::withMessages([
            'email' => 'Terlalu banyak percobaan registrasi. Coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
        ]);
    }

    /**
     * Get the registration rate limiting throttle key.
     */
    protected function throttleKey(): string
    {
        return Str::transliterate('register|' . request()->ip());
    }
}; ?>

<div>
    <form wire:submit="register">
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Nama')" />
            <x-text-input wire:model="name" id="name" class="block mt-1 w-full" type="text" name="name" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input wire:model="email" id="email" class="block mt-1 w-full" type="email" name="email" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Phone -->
        <div class="mt-4">
            <x-input-label for="phone" :value="__('No Telepon')" />
            <x-text-input wire:model="phone" id="phone" class="block mt-1 w-full" type="text" name="phone" required autocomplete="tel" />
            <x-input-error :messages="$errors->get('phone')" class="mt-2" />
        </div>

        <!-- KTP Number -->
        <div class="mt-4">
            <x-input-label for="ktp_number" :value="__('No KTP')" />
            <x-text-input wire:model="ktp_number" id="ktp_number" class="block mt-1 w-full" type="text" name="ktp_number" required maxlength="16" />
            <x-input-error :messages="$errors->get('ktp_number')" class="mt-2" />
        </div>

        <!-- Address -->
        <div class="mt-4">
            <x-input-label for="address" :value="__('Alamat')" />
            <textarea wire:model="address" id="address" class="block mt-1 w-full border-gray-300 focus:border-accent focus:ring-accent/40 rounded-md shadow-card" name="address" rows="3" required></textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input wire:model="password" id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
            <x-text-input wire:model="password_confirmation" id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-body text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-accent/40" href="{{ route('login') }}" wire:navigate>
                {{ __('Sudah punya akun?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Daftar') }}
            </x-primary-button>
        </div>
    </form>
</div>
