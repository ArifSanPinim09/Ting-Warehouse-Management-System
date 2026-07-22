<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response
            ->assertOk()
            ->assertSeeVolt('pages.auth.register');
    }

    public function test_new_users_can_register(): void
    {
        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('phone', '081234567890')
            ->set('ktp_number', '1234567890123456')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('tncAccepted', true);

        $component->call('register');

        $component->assertRedirect(route('login', absolute: false));

        // User is NOT authenticated after registration — must wait for admin activation (PRD §4.1)
        $this->assertGuest();

        // But the user record exists with PENDING status
        $this->assertDatabaseHas('users', [
            'email' => 'test@example.com',
            'status' => 'pending',
            'role' => 'customer',
        ]);
    }

    /**
     * PRD §12.1: "Email sudah terdaftar"
     */
    public function test_register_with_duplicate_email_shows_error(): void
    {
        User::factory()->create(['email' => 'taken@example.com']);

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'taken@example.com')
            ->set('phone', '081234567890')
            ->set('ktp_number', '1234567890123456')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('tncAccepted', true);

        $component->call('register');

        $component->assertHasErrors(['email']);
    }

    /**
     * PRD §12.4: "No KTP sudah terdaftar"
     */
    public function test_register_with_duplicate_ktp_shows_error(): void
    {
        User::factory()->create(['ktp_number' => '1234567890123456']);

        $component = Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'new@example.com')
            ->set('phone', '081234567890')
            ->set('ktp_number', '1234567890123456')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('tncAccepted', true);

        $component->call('register');

        $component->assertHasErrors(['ktp_number']);
    }

    /**
     * PRD §4.1: User created as PENDING, not ACTIVE
     */
    public function test_register_creates_user_with_pending_status(): void
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('phone', '081234567890')
            ->set('ktp_number', '1234567890123456')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->set('tncAccepted', true)
            ->call('register');

        $user = User::where('email', 'test@example.com')->first();
        $this->assertNotNull($user);
        $this->assertEquals(User::STATUS_PENDING, $user->status);
        $this->assertEquals('customer', $user->role);
    }

    /**
     * PRD §4.1: Customer must NOT be auto-logged-in after registration
     */
    public function test_register_does_not_auto_login(): void
    {
        Volt::test('pages.auth.register')
            ->set('name', 'Test User')
            ->set('email', 'test@example.com')
            ->set('phone', '081234567890')
            ->set('ktp_number', '1234567890123456')
            ->set('address', 'Jl. Test No. 123, Jakarta')
            ->set('password', 'password')
            ->set('password_confirmation', 'password')
            ->call('register');

        $this->assertGuest();
    }
}
