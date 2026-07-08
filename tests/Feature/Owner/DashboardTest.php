<?php

namespace Tests\Feature\Owner;

use App\Models\Box;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private function createOwner(): User
    {
        return User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
    }

    private function createAdmin(): User
    {
        return User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
    }

    private function createCustomer(): User
    {
        return User::factory()->create(['role' => 'customer', 'status' => User::STATUS_ACTIVE]);
    }

    public function test_owner_can_access_dashboard(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        $response = $this->get('/owner/dashboard');

        $response->assertOk();
    }

    public function test_admin_cannot_access_owner_dashboard(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $response = $this->get('/owner/dashboard');

        $response->assertForbidden();
    }

    public function test_customer_cannot_access_owner_dashboard(): void
    {
        $customer = $this->createCustomer();

        $this->actingAs($customer);

        $response = $this->get('/owner/dashboard');

        $response->assertForbidden();
    }

    public function test_guest_cannot_access_owner_dashboard(): void
    {
        $response = $this->get('/owner/dashboard');

        $response->assertRedirect('/login');
    }

    public function test_dashboard_shows_revenue_stats(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'status' => Invoice::STATUS_VERIFIED,
            'grand_total' => 1500000,
        ]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\Dashboard::class)
            ->assertSee('Revenue bulan ini')
            ->assertSee('Outstanding')
            ->assertSee('Customer')
            ->assertSee('Pengiriman aktif');
    }

    public function test_dashboard_shows_empty_state_when_no_data(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\Dashboard::class)
            ->assertSee('Rp 0')
            ->assertSee('Tidak ada notifikasi')
            ->assertSee('Belum ada aktivitas')
            ->assertSee('Belum ada invoice');
    }

    public function test_dashboard_shows_notifications(): void
    {
        $owner = $this->createOwner();

        Notification::create([
            'id' => \Illuminate\Support\Str::uuid(),
            'type' => 'App\\Models\\Notification',
            'notifiable_type' => User::class,
            'notifiable_id' => $owner->id,
            'data' => ['title' => 'Test Notif', 'message' => 'Test message'],
        ]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\Dashboard::class)
            ->assertSee('Test Notif')
            ->assertSee('Test message');
    }

    public function test_dashboard_shows_recent_activities(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        \App\Models\ActivityLog::create([
            'user_id' => $owner->id,
            'subject_type' => User::class,
            'subject_id' => $owner->id,
            'event' => 'created',
        ]);

        Livewire::test(\App\Livewire\Owner\Dashboard::class)
            ->assertSee('Aktivitas Terbaru');
    }

    public function test_dashboard_shows_top_customers(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'status' => Invoice::STATUS_VERIFIED,
            'grand_total' => 5000000,
        ]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\Dashboard::class)
            ->assertSee('Top Customer')
            ->assertSee($customer->name);
    }

    public function test_dashboard_shows_recent_invoices(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();
        $box = Box::factory()->create(['customer_id' => $customer->id]);

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'invoice_number' => 'INV-TEST-001',
        ]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\Dashboard::class)
            ->assertSee('Invoice Terbaru')
            ->assertSee('INV-TEST-001');
    }

    public function test_dashboard_displays_all_quick_menu_links(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\Dashboard::class)
            ->assertSee(route('owner.finance', absolute: false))
            ->assertSee(route('owner.audit-log', absolute: false))
            ->assertSee(route('owner.manage-admin', absolute: false))
            ->assertSee(route('admin.settings', absolute: false))
            ->assertSee(route('admin.boxes', absolute: false))
            ->assertSee(route('admin.invoices', absolute: false));
    }
}
