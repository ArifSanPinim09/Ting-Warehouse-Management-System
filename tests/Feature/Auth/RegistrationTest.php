<?php

namespace Tests\Feature\Auth;

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
            ->set('password_confirmation', 'password');

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
}
