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
    public ?string $customer_code = null;
    public string $password = '';
    public string $password_confirmation = '';
    public bool $tncAccepted = false; // Sprint 3: TnC checkbox
    public bool $showTnc = false; // Sprint 3: TnC modal

    /**
     * Handle an incoming registration request.
     *
     * PRD §4.1: Validasi -> Simpan(PENDING) -> Notif admin -> Aktivasi -> Login
     * PRD §20.5: Register rate limit 3x per hour
     * Sprint 3: TnC must be accepted
     * User TIDAK langsung login setelah register, harus tunggu aktivasi admin.
     */
    public function register(): void
    {
        // PRD §20.5: Rate limiting for registration (3x per hour per IP)
        $this->ensureIsNotRateLimited();

        // Hit rate limiter BEFORE processing (counts all attempts, successful or not)
        RateLimiter::hit($this->throttleKey(), 3600); // 1 hour window

        $validated = $this->validate((new RegisterRequest())->rules(), (new RegisterRequest())->messages());

        // Sprint 3: TnC validation
        if (!$this->tncAccepted) {
            throw ValidationException::withMessages([
                'tncAccepted' => 'Anda harus menyetujui Syarat & Ketentuan untuk mendaftar.',
            ]);
        }

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = User::STATUS_PENDING;
        $validated['role'] = 'customer';
        $validated['tnc_accepted'] = true;
        $validated['tnc_accepted_at'] = now();

        // Auto-generate unique customer_code (Nama ID) from name initials
        // Flow Website: Nama ID customer harus unik & tidak bisa diganti setelah register
        $validated['customer_code'] = $this->generateUniqueCustomerCode($validated['name']);

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

    /**
     * Generate a unique 3-letter customer_code from customer name.
     * Takes first letters of up to 3 words, uppercase. Appends number if collision.
     * Example: "Rina Wijaya" → "RIW", "Budi" → "BUD", "Budi" again → "BUD2"
     */
    protected function generateUniqueCustomerCode(string $name): string
    {
        $words = explode(' ', trim($name));
        $code = '';
        foreach (array_slice($words, 0, 3) as $word) {
            $code .= strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $word), 0, 1));
        }
        $code = str_pad($code ?: 'XXX', 3, 'X');

        $base = $code;
        $suffix = 1;
        while (\App\Models\User::where('customer_code', $code)->exists()) {
            $suffix++;
            $code = $base . $suffix;
        }

        return $code;
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

        {{-- Sprint 3: TnC Checkbox --}}
        <div class="mt-4">
            <label class="flex items-start gap-2">
                <input wire:model="tncAccepted" type="checkbox" name="tnc_accepted" required class="mt-1 rounded border-gray-300 text-primary focus:ring-primary">
                <span class="text-[13px] text-gray-600">
                    Saya menyetujui
                    <button type="button" wire:click="$set('showTnc', true)" class="text-primary underline hover:text-primary/80">Syarat & Ketentuan</button>
                    Ting Warehouse
                </span>
            </label>
            @error('tncAccepted') <p class="text-[12px] text-red-500 mt-1">{{ $message }}</p> @enderror
        </div>

        {{-- TnC Modal --}}
        @if($showTnc ?? false)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-black/30" wire:click="$set('showTnc', false)"></div>
                <div class="relative bg-white rounded-[12px] shadow-xl w-full max-w-lg max-h-[80vh] overflow-y-auto z-10">
                    <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between sticky top-0 bg-white">
                        <h3 class="text-[15px] font-semibold text-gray-900">Syarat & Ketentuan</h3>
                        <button wire:click="$set('showTnc', false)" class="text-gray-400 hover:text-gray-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                    <div class="p-6 space-y-3 text-[13px] text-gray-600 leading-relaxed">
                        <p><strong>1. Pendaftaran</strong><br>Dengan mendaftar, Anda setuju untuk memberikan informasi yang benar dan akurat.</p>
                        <p><strong>2. Resi & Barang</strong><br>Customer wajib menyetorkan resi dari supplier China. Barang yang tidak diklaim dalam 1 minggu akan dikenakan denda.</p>
                        <p><strong>3. Pembayaran</strong><br>Tagihan harus dibayar dalam 5 hari. Keterlambatan dikenakan denda Rp 5.000/hari. Setelah 1 minggu, barang akan di-hold.</p>
                        <p><strong>4. Lelang</strong><br>Barang yang tidak diklaim setelah 15 hari (12 hari + 3 hari grace) akan dilelang.</p>
                        <p><strong>5. Blacklist</strong><br>Customer yang kabur/tidak membayar akan di-blacklist dan tidak dapat menggunakan layanan lagi.</p>
                        <p><strong>6. Sensitive & Garment</strong><br>Barang sensitive dan garment dikenakan rate khusus. Wajib declare saat setor resi.</p>
                    </div>
                    <div class="px-6 py-4 border-t border-gray-100 sticky bottom-0 bg-white">
                        <button wire:click="$set('showTnc', false)" class="w-full bg-primary text-white text-[14px] font-medium rounded-[8px] py-2 hover:bg-primary/90">Saya Mengerti</button>
                    </div>
                </div>
            </div>
        @endif

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
