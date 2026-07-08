<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class LoginStatusTest extends TestCase
{
    use RefreshDatabase;

    /**
     * PRD §13.1: "Akun belum aktif. Hubungi admin."
     */
    public function test_login_with_pending_account_shows_error(): void
    {
        $user = User::factory()->create([
            'status' => User::STATUS_PENDING,
            'password' => bcrypt('password'),
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertHasErrors();
        $this->assertGuest();
    }

    /**
     * PRD §13.1: "Akun telah dinonaktifkan."
     */
    public function test_login_with_inactive_account_shows_error(): void
    {
        $user = User::factory()->create([
            'status' => User::STATUS_INACTIVE,
            'password' => bcrypt('password'),
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertHasErrors();
        $this->assertGuest();
    }

    /**
     * PRD §4.1: 5x gagal kunci 15 menit
     * PRD §13.1: "Akun terkunci. Coba lagi dalam X menit."
     */
    public function test_login_with_locked_account_shows_error(): void
    {
        $user = User::factory()->create([
            'status' => User::STATUS_ACTIVE,
            'password' => bcrypt('password'),
        ]);

        // Fail 5 times to trigger lockout
        for ($i = 0; $i < 5; $i++) {
            Volt::test('pages.auth.login')
                ->set('form.email', $user->email)
                ->set('form.password', 'wrong-password')
                ->call('login');
        }

        // 6th attempt should be locked out
        $component = Volt::test('pages.auth.login')
            ->set('form.email', $user->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertHasErrors();
        $this->assertGuest();
    }

    /**
     * PRD §7.5: Owner redirects to /owner/dashboard
     */
    public function test_owner_login_redirects_to_owner_dashboard(): void
    {
        $owner = User::factory()->create([
            'role' => 'owner',
            'status' => User::STATUS_ACTIVE,
            'password' => bcrypt('password'),
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $owner->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertRedirect('/owner/dashboard');
    }

    /**
     * PRD §7.5: Admin redirects to /admin/dashboard
     */
    public function test_admin_login_redirects_to_admin_dashboard(): void
    {
        $admin = User::factory()->create([
            'role' => 'admin',
            'status' => User::STATUS_ACTIVE,
            'password' => bcrypt('password'),
        ]);

        $component = Volt::test('pages.auth.login')
            ->set('form.email', $admin->email)
            ->set('form.password', 'password');

        $component->call('login');

        $component->assertRedirect('/admin/dashboard');
    }
}
