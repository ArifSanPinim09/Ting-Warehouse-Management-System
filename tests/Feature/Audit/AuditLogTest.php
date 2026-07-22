<?php

namespace Tests\Feature\Audit;

use App\Livewire\Admin\ManageBox;
use App\Livewire\Admin\VerificationIndex;
use App\Livewire\Owner\ManageAdminIndex;
use App\Models\ActivityLog;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AuditLogTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Box status change creates audit log entry
     */
    public function test_box_status_change_logged(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create([
            'customer_id' => $customer->id,
            'status' => Box::STATUS_OPEN,
        ]);

        $this->actingAs($admin);

        Livewire::test(ManageBox::class)
            ->call('selectBox', $box->id)
            ->call('confirmStatusChange', Box::STATUS_SEND_TO_CARGO)
            ->set('statusNote', 'Box sudah dikirim ke cargo')
            ->call('updateStatus');

        $log = ActivityLog::where('subject_type', Box::class)
            ->where('subject_id', $box->id)
            ->where('event', 'updated')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($admin->id, $log->user_id);
        $this->assertArrayHasKey('status', $log->old_values);
        $this->assertArrayHasKey('status', $log->new_values);
    }

    /**
     * Invoice generation creates audit log entry
     */
    public function test_invoice_generation_logged(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create([
            'customer_id' => $customer->id,
            'status' => Box::STATUS_ARRIVED_INA,
            'type' => 'sharing',
            'method' => 'air',
        ]);

        $this->seedDefaultRates();
        $this->actingAs($admin);

        Livewire::test(\App\Livewire\Admin\GenerateInvoice::class)
            ->set('selectedBoxId', $box->id)
            ->set('weight', 50)
            ->set('length', 30)
            ->set('width', 20)
            ->set('height', 20)
            ->set('addOn', 0)
            ->call('generateInvoice');

        $log = ActivityLog::where('subject_type', Invoice::class)
            ->where('event', 'generated')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($admin->id, $log->user_id);
    }

    /**
     * Payment verification creates audit log entry
     */
    public function test_payment_verification_logged(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_ACTIVE]);
        $customer = User::factory()->create(['role' => 'customer']);
        $box = Box::factory()->create(['customer_id' => $customer->id]);
        $invoice = Invoice::factory()->create([
            'customer_id' => $customer->id,
            'box_id' => $box->id,
            'status' => Invoice::STATUS_WAITING_VERIFICATION,
            'payment_method' => 'transfer',
            'payment_proof' => 'test.jpg',
        ]);

        $this->actingAs($admin);

        Livewire::test(VerificationIndex::class)
            ->call('selectInvoice', $invoice->id)
            ->call('verifyPayment');

        $log = ActivityLog::where('subject_type', Invoice::class)
            ->where('subject_id', $invoice->id)
            ->where('event', 'payment_verified')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($admin->id, $log->user_id);
    }

    /**
     * Admin activation creates audit log entry
     */
    public function test_admin_activation_logged(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'status' => User::STATUS_ACTIVE]);
        $admin = User::factory()->create(['role' => 'admin', 'status' => User::STATUS_INACTIVE]);

        $this->actingAs($owner);

        Livewire::test(ManageAdminIndex::class)
            ->call('confirmActivate', $admin->id)
            ->call('executeConfirm');

        $log = ActivityLog::where('subject_type', User::class)
            ->where('subject_id', $admin->id)
            ->where('event', 'activated')
            ->first();

        $this->assertNotNull($log);
        $this->assertEquals($owner->id, $log->user_id);
    }

    private function seedDefaultRates(): void
    {
        $settings = [
            ['key' => 'kurs_yuan_idr', 'value' => '2460', 'group' => 'currency'],
            ['key' => 'rate_sharing_air_berat', 'value' => '255', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_air_volume', 'value' => '230', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_berat', 'value' => '70', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sea_volume', 'value' => '83', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_air_berat', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_air_volume', 'value' => '315', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_berat', 'value' => '95', 'group' => 'rate_sharing'],
            ['key' => 'rate_sharing_sensitive_sea_volume', 'value' => '95', 'group' => 'rate_sharing'],
            ['key' => 'rate_direct_air_berat', 'value' => '230', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_air_volume', 'value' => '160', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_berat', 'value' => '70', 'group' => 'rate_direct'],
            ['key' => 'rate_direct_sea_volume', 'value' => '90', 'group' => 'rate_direct'],
            ['key' => 'fee_packing_150', 'value' => '5000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_1000', 'value' => '6500', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_2000', 'value' => '8000', 'group' => 'fee_packing'],
            ['key' => 'fee_packing_extra_per_kg', 'value' => '1500', 'group' => 'fee_packing'],
        ];

        foreach ($settings as $setting) {
            \App\Models\Setting::create($setting);
        }
    }
}
