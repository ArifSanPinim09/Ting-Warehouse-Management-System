<?php

namespace Tests\Feature\Owner;

use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class FinanceTest extends TestCase
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

    public function test_owner_can_access_finance(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        $response = $this->get('/owner/finance');

        $response->assertOk();
    }

    public function test_admin_cannot_access_finance(): void
    {
        $admin = $this->createAdmin();

        $this->actingAs($admin);

        $response = $this->get('/owner/finance');

        $response->assertForbidden();
    }

    public function test_customer_cannot_access_finance(): void
    {
        $customer = $this->createCustomer();

        $this->actingAs($customer);

        $response = $this->get('/owner/finance');

        $response->assertForbidden();
    }

    public function test_finance_shows_summary_cards(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\FinanceIndex::class)
            ->assertSee('Total Revenue')
            ->assertSee('Outstanding')
            ->assertSee('Profit')
            ->assertSee('Total Invoice');
    }

    public function test_finance_shows_empty_state(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\FinanceIndex::class)
            ->assertSee('Tidak ada data ditemukan');
    }

    public function test_finance_shows_invoice_list(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-FIN-001',
            'grand_total' => 2500000,
        ]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\FinanceIndex::class)
            ->assertSee('INV-FIN-001')
            ->assertSee($customer->name)
            ->assertSee('2.500.000');
    }

    public function test_finance_search_filter(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-SEARCH-001',
        ]);

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-OTHER-002',
        ]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\FinanceIndex::class)
            ->set('search', 'SEARCH')
            ->assertSee('INV-SEARCH-001')
            ->assertDontSee('INV-OTHER-002');
    }

    public function test_finance_status_filter(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-VERIFIED-001',
            'status' => Invoice::STATUS_VERIFIED,
        ]);

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-PENDING-001',
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ]);

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\FinanceIndex::class)
            ->set('filterStatus', 'verified')
            ->assertSee('INV-VERIFIED-001')
            ->assertDontSee('INV-PENDING-001');
    }

    public function test_finance_reset_filters(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\FinanceIndex::class)
            ->set('search', 'test')
            ->set('filterStatus', 'verified')
            ->call('resetFilters')
            ->assertSet('search', '')
            ->assertSet('filterStatus', '');
    }

    public function test_finance_export_buttons_visible(): void
    {
        $owner = $this->createOwner();

        $this->actingAs($owner);

        Livewire::test(\App\Livewire\Owner\FinanceIndex::class)
            ->assertSee('CSV')
            ->assertSee('Excel');
    }

    public function test_export_finance_csv_as_owner(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-EXPORT-001',
        ]);

        $this->actingAs($owner);

        $response = $this->get('/owner/finance/export?type=csv');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
    }

    public function test_export_finance_excel_as_owner(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
            'invoice_number' => 'INV-EXCEL-001',
        ]);

        $this->actingAs($owner);

        $response = $this->get('/owner/finance/export?type=excel');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/vnd.ms-excel; charset=UTF-8');
    }

    public function test_export_finance_creates_audit_log(): void
    {
        $owner = $this->createOwner();
        $customer = $this->createCustomer();

        Invoice::factory()->create([
            'customer_id' => $customer->id,
        ]);

        $this->actingAs($owner);

        $this->get('/owner/finance/export?type=csv');

        $this->assertDatabaseHas('activity_logs', [
            'user_id' => $owner->id,
            'event' => 'export_finance',
        ]);
    }
}
