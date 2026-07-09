<?php

namespace Tests\Unit\Models;

use App\Models\KursHistory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KursHistoryModelTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    // ─── Relationship Tests ─────────────────────────────────────────

    public function test_belongs_to_user_via_input_by(): void
    {
        $kurs = KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $this->assertInstanceOf(User::class, $kurs->inputBy);
        $this->assertEquals($this->admin->id, $kurs->inputBy->id);
    }

    public function test_user_has_many_kurs_history(): void
    {
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);
        KursHistory::create([
            'kurs_value' => 2700.00,
            'effective_date' => '2026-09-15',
            'input_by' => $this->admin->id,
        ]);

        $this->assertCount(2, $this->admin->kursHistory);
    }

    // ─── Fillable & Cast Tests ──────────────────────────────────────

    public function test_fillable_attributes(): void
    {
        $kurs = KursHistory::create([
            'kurs_value' => 2660.50,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('kurs_history', [
            'id' => $kurs->id,
            'input_by' => $this->admin->id,
        ]);
        $this->assertEqualsWithDelta(2660.50, (float) $kurs->kurs_value, 0.01);
        $this->assertEquals('2026-09-01', $kurs->effective_date->format('Y-m-d'));
    }

    public function test_kurs_value_is_decimal_cast(): void
    {
        $kurs = KursHistory::create([
            'kurs_value' => 2660.75,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $this->assertIsString($kurs->kurs_value);
        $this->assertEquals('2660.75', $kurs->kurs_value);
    }

    public function test_effective_date_is_date_cast(): void
    {
        $kurs = KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $kurs->effective_date);
    }

    public function test_created_at_is_datetime_cast(): void
    {
        $kurs = KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        // With UPDATED_AT = null, Eloquent auto-sets created_at
        $this->assertNotNull($kurs->created_at);
        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $kurs->created_at);
    }

    public function test_no_updated_at_column(): void
    {
        $this->assertFalse(
            \Illuminate\Support\Facades\Schema::hasColumn('kurs_history', 'updated_at'),
            'kurs_history should not have updated_at — history records are immutable'
        );
    }

    // ─── Unique Constraint Tests ────────────────────────────────────

    public function test_unique_constraint_on_kurs_value_and_effective_date(): void
    {
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $this->expectException(\Illuminate\Database\QueryException::class);

        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);
    }

    public function test_same_kurs_value_different_date_is_allowed(): void
    {
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $kurs2 = KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-15',
            'input_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('kurs_history', ['id' => $kurs2->id]);
        $this->assertEquals(2, KursHistory::count());
    }

    public function test_same_date_different_kurs_value_is_allowed(): void
    {
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $kurs2 = KursHistory::create([
            'kurs_value' => 2700.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $this->assertDatabaseHas('kurs_history', ['id' => $kurs2->id]);
        $this->assertEquals(2, KursHistory::count());
    }

    // ─── Cascade Delete Tests ───────────────────────────────────────

    public function test_cascade_delete_when_user_deleted(): void
    {
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
        ]);

        $this->admin->delete();

        $this->assertEquals(0, KursHistory::count());
    }

    // ─── getLatest() Tests ──────────────────────────────────────────

    public function test_get_latest_returns_most_recent_by_effective_date(): void
    {
        KursHistory::create([
            'kurs_value' => 2500.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 08:00:00',
        ]);
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-15',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-15 08:00:00',
        ]);
        KursHistory::create([
            'kurs_value' => 2600.00,
            'effective_date' => '2026-09-10',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-10 08:00:00',
        ]);

        $latest = KursHistory::getLatest();

        $this->assertNotNull($latest);
        $this->assertEquals(2660.00, (float) $latest->kurs_value);
        $this->assertEquals('2026-09-15', $latest->effective_date->format('Y-m-d'));
    }

    public function test_get_latest_returns_null_when_empty(): void
    {
        $this->assertNull(KursHistory::getLatest());
    }

    public function test_get_latest_tiebreaks_by_created_at(): void
    {
        // Same effective_date, different created_at
        $first = KursHistory::create([
            'kurs_value' => 2500.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 10:00:00',
        ]);
        $second = KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 14:00:00',
        ]);

        $latest = KursHistory::getLatest();

        $this->assertNotNull($latest);
        $this->assertEquals($second->id, $latest->id);
    }

    // ─── getKursOnDate() Tests ──────────────────────────────────────

    public function test_get_kurs_on_date_returns_matching_record(): void
    {
        KursHistory::create([
            'kurs_value' => 2500.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 08:00:00',
        ]);
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-15',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-15 08:00:00',
        ]);

        // Query for Sep 10 → should get Sep 1 record (closest before)
        $kurs = KursHistory::getKursOnDate('2026-09-10');

        $this->assertNotNull($kurs);
        $this->assertEquals(2500.00, (float) $kurs->kurs_value);
    }

    public function test_get_kurs_on_date_returns_exact_date_match(): void
    {
        KursHistory::create([
            'kurs_value' => 2500.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 08:00:00',
        ]);
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-15',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-15 08:00:00',
        ]);

        $kurs = KursHistory::getKursOnDate('2026-09-15');

        $this->assertNotNull($kurs);
        $this->assertEquals(2660.00, (float) $kurs->kurs_value);
    }

    public function test_get_kurs_on_date_returns_latest_before_query_date(): void
    {
        KursHistory::create([
            'kurs_value' => 2500.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 08:00:00',
        ]);
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-15',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-15 08:00:00',
        ]);
        KursHistory::create([
            'kurs_value' => 2700.00,
            'effective_date' => '2026-09-20',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-20 08:00:00',
        ]);

        // Query for Sep 18 → should get Sep 15 record
        $kurs = KursHistory::getKursOnDate('2026-09-18');

        $this->assertNotNull($kurs);
        $this->assertEquals(2660.00, (float) $kurs->kurs_value);
    }

    public function test_get_kurs_on_date_returns_null_when_no_records_before_date(): void
    {
        KursHistory::create([
            'kurs_value' => 2660.00,
            'effective_date' => '2026-09-15',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-15 08:00:00',
        ]);

        // Query for Sep 1 → no kurs before that date
        $kurs = KursHistory::getKursOnDate('2026-09-01');

        $this->assertNull($kurs);
    }

    public function test_get_kurs_on_date_returns_null_when_empty(): void
    {
        $kurs = KursHistory::getKursOnDate('2026-09-01');

        $this->assertNull($kurs);
    }

    public function test_get_kurs_on_date_with_same_date_multiple_entries(): void
    {
        // Same effective_date, different created_at — should return the later one
        KursHistory::create([
            'kurs_value' => 2500.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 08:00:00',
        ]);
        KursHistory::create([
            'kurs_value' => 2600.00,
            'effective_date' => '2026-09-01',
            'input_by' => $this->admin->id,
            'created_at' => '2026-09-01 16:00:00',
        ]);

        $kurs = KursHistory::getKursOnDate('2026-09-01');

        $this->assertNotNull($kurs);
        $this->assertEquals(2600.00, (float) $kurs->kurs_value);
    }
}
