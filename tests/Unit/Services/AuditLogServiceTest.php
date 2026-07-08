<?php

namespace Tests\Unit\Services;

use App\Models\ActivityLog;
use App\Models\Box;
use App\Models\Invoice;
use App\Models\Setting;
use App\Models\User;
use App\Services\AuditLogService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

/**
 * Unit tests for AuditLogService.
 *
 * Tests that the service correctly computes diffs, logs events,
 * and skips logging when nothing changed.
 */
class AuditLogServiceTest extends TestCase
{
    use RefreshDatabase;

    private AuditLogService $service;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = new AuditLogService();
    }

    /**
     * Test basic log creation with diff.
     */
    public function test_log_creates_activity_log_with_diff(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create(['status' => Box::STATUS_OPEN]);

        $this->service->log(
            event: 'updated',
            subject: $box,
            old: ['status' => Box::STATUS_OPEN],
            new: ['status' => Box::STATUS_SENT_TO_CARGO],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertEquals($user->id, $log->user_id);
        $this->assertEquals(Box::class, $log->subject_type);
        $this->assertEquals($box->id, $log->subject_id);
        $this->assertEquals('updated', $log->event);
        $this->assertEquals(['status' => Box::STATUS_OPEN], $log->old_values);
        $this->assertEquals(['status' => Box::STATUS_SENT_TO_CARGO], $log->new_values);
    }

    /**
     * Test log skips when no diff (nothing changed).
     */
    public function test_log_skips_when_no_diff(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create(['status' => Box::STATUS_OPEN]);

        $result = $this->service->log(
            event: 'updated',
            subject: $box,
            old: ['status' => Box::STATUS_OPEN],
            new: ['status' => Box::STATUS_OPEN],
        );

        $this->assertNull($result);
        $this->assertEquals(0, ActivityLog::count());
    }

    /**
     * Test log filters out timestamp and password fields from diff.
     */
    public function test_log_filters_excluded_fields(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create();

        $this->service->log(
            event: 'updated',
            subject: $box,
            old: [
                'status'     => Box::STATUS_OPEN,
                'updated_at' => '2024-01-01 00:00:00',
                'created_at' => '2024-01-01 00:00:00',
                'password'   => 'old-hash',
            ],
            new: [
                'status'     => Box::STATUS_SENT_TO_CARGO,
                'updated_at' => '2024-01-02 00:00:00',
                'created_at' => '2024-01-01 00:00:00',
                'password'   => 'new-hash',
            ],
        );

        $log = ActivityLog::first();

        $this->assertArrayNotHasKey('updated_at', $log->old_values);
        $this->assertArrayNotHasKey('created_at', $log->old_values);
        $this->assertArrayNotHasKey('password', $log->old_values);
        $this->assertArrayNotHasKey('updated_at', $log->new_values);
        $this->assertArrayHasKey('status', $log->old_values);
        $this->assertArrayHasKey('status', $log->new_values);
    }

    /**
     * Test log for "created" event (no diff needed).
     */
    public function test_log_for_created_event(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $setting = Setting::factory()->create(['key' => 'test_key', 'value' => '100', 'group' => 'test']);

        $this->service->log(
            event: 'created',
            subject: $setting,
            old: [],
            new: ['key' => 'test_key', 'value' => '100', 'group' => 'test'],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertEquals('created', $log->event);
        $this->assertNull($log->old_values);
        $this->assertArrayHasKey('key', $log->new_values);
    }

    /**
     * Test logCustom with description.
     */
    public function test_log_custom_with_description(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $invoice = Invoice::factory()->create();

        // Clear any observer-created logs first
        ActivityLog::truncate();

        $description = "Invoice {$invoice->invoice_number} generated";

        $this->service->logCustom(
            subject: $invoice,
            event: 'generated',
            description: $description,
            new: ['invoice_number' => $invoice->invoice_number, 'grand_total' => 500000],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertEquals('generated', $log->event);
        $this->assertEquals($description, $log->new_values['_description']);
        $this->assertEquals($invoice->invoice_number, $log->new_values['invoice_number']);
    }

    /**
     * Test log with null user (system action).
     */
    public function test_log_with_null_user(): void
    {
        // Don't login anyone — Auth::id() returns null
        Auth::logout();

        $box = Box::factory()->create();

        $this->service->log(
            event: 'created',
            subject: $box,
            new: ['status' => Box::STATUS_OPEN],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertNull($log->user_id);
    }

    /**
     * Test multiple fields changed in a single update.
     */
    public function test_log_multiple_fields_changed(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $setting = Setting::factory()->create();

        // Clear any observer-created logs
        ActivityLog::truncate();

        $this->service->log(
            event: 'updated',
            subject: $setting,
            old: ['key' => 'rate_sharing_air_berat', 'value' => '255'],
            new: ['key' => 'rate_sharing_air_berat', 'value' => '300'],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertEquals('255', $log->old_values['value']);
        $this->assertEquals('300', $log->new_values['value']);
        // Key didn't change, should be excluded from diff
        $this->assertArrayNotHasKey('key', $log->old_values);
        $this->assertArrayNotHasKey('key', $log->new_values);
    }

    /**
     * Test ActivityLog model scopes.
     */
    public function test_activity_log_scopes(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create();
        $invoice = Invoice::factory()->create();

        // Clear observer-created logs
        ActivityLog::truncate();

        // Create different events
        $this->service->log(event: 'created', subject: $box, new: ['status' => 'OPEN']);
        $this->service->log(event: 'updated', subject: $box, old: ['status' => 'OPEN'], new: ['status' => 'SENT_TO_CARGO']);
        $this->service->log(event: 'generated', subject: $invoice, new: ['invoice_number' => 'INV-001']);

        // Test ForEvent scope
        $updatedLogs = ActivityLog::forEvent('updated')->get();
        $this->assertEquals(1, $updatedLogs->count());

        // Test ForSubjectType scope
        $boxLogs = ActivityLog::forSubjectType(Box::class)->get();
        $this->assertEquals(2, $boxLogs->count());

        $invoiceLogs = ActivityLog::forSubjectType(Invoice::class)->get();
        $this->assertEquals(1, $invoiceLogs->count());
    }

    /**
     * Test SettingFactory creates a setting.
     */
    public function test_setting_factory(): void
    {
        $setting = Setting::factory()->create();

        $this->assertNotNull($setting);
        $this->assertNotNull($setting->key);
    }

    /**
     * Test log for "deleted" event.
     */
    public function test_log_for_deleted_event(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create();

        // Clear observer-created logs
        ActivityLog::truncate();

        $this->service->log(
            event: 'deleted',
            subject: $box,
            old: ['status' => Box::STATUS_OPEN, 'tracking_number' => 'TRK-001'],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertEquals('deleted', $log->event);
        $this->assertEquals(Box::class, $log->subject_type);
        $this->assertEquals(['status' => Box::STATUS_OPEN, 'tracking_number' => 'TRK-001'], $log->old_values);
        $this->assertNull($log->new_values);
    }

    /**
     * Test logCustom without description (empty description).
     */
    public function test_log_custom_without_description(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create();
        ActivityLog::truncate();

        $this->service->logCustom(
            subject: $box,
            event: 'exported',
            description: '',
            new: ['format' => 'csv'],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertEquals('exported', $log->event);
        $this->assertArrayNotHasKey('_description', $log->new_values);
        $this->assertEquals('csv', $log->new_values['format']);
    }

    /**
     * Test log with both old and new as empty arrays (created event).
     */
    public function test_log_created_with_empty_new(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create();
        ActivityLog::truncate();

        $this->service->log(
            event: 'created',
            subject: $box,
            old: [],
            new: [],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        $this->assertEquals('created', $log->event);
        $this->assertNull($log->old_values);
        $this->assertNull($log->new_values);
    }

    /**
     * Test computeDiff with multiple changed fields.
     */
    public function test_log_multiple_changed_fields(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create();
        ActivityLog::truncate();

        $this->service->log(
            event: 'updated',
            subject: $box,
            old: ['status' => Box::STATUS_OPEN, 'tracking_number' => 'TRK-OLD', 'notes' => 'lama'],
            new: ['status' => Box::STATUS_SENT_TO_CARGO, 'tracking_number' => 'TRK-NEW', 'notes' => 'lama'],
        );

        $log = ActivityLog::first();

        $this->assertNotNull($log);
        // status and tracking_number changed
        $this->assertEquals(Box::STATUS_OPEN, $log->old_values['status']);
        $this->assertEquals(Box::STATUS_SENT_TO_CARGO, $log->new_values['status']);
        $this->assertEquals('TRK-OLD', $log->old_values['tracking_number']);
        $this->assertEquals('TRK-NEW', $log->new_values['tracking_number']);
        // notes didn't change, should NOT appear
        $this->assertArrayNotHasKey('notes', $log->old_values);
        $this->assertArrayNotHasKey('notes', $log->new_values);
    }

    /**
     * Test ActivityLog model has correct relationship.
     */
    public function test_activity_log_subject_morph(): void
    {
        $user = User::factory()->create();
        Auth::login($user);

        $box = Box::factory()->create();
        ActivityLog::truncate();

        $this->service->log(
            event: 'created',
            subject: $box,
            new: ['status' => Box::STATUS_OPEN],
        );

        $log = ActivityLog::first();

        $this->assertEquals(Box::class, $log->subject_type);
        $this->assertEquals($box->id, $log->subject_id);
    }
}
