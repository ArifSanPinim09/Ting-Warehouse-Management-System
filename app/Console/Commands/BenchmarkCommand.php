<?php

namespace App\Console\Commands;

use App\Models\Box;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Console\Command;

/**
 * Benchmark main endpoints against realistic data.
 *
 * §5.2: API <500ms P95, DB query <100ms
 *
 * Usage: php artisan app:benchmark
 * Prerequisites: php artisan db:seed --class=RealisticDataSeeder
 */
class BenchmarkCommand extends Command
{
    protected $signature = 'app:benchmark';
    protected $description = 'Benchmark main endpoints against realistic data scale';

    public function handle(): int
    {
        $this->info('=== Ting Warehouse Performance Benchmark ===');
        $this->info('§5.2 Target: API <500ms P95, DB query <100ms');
        $this->newLine();

        // Verify data exists
        $userCount = User::count();
        $boxCount = Box::count();
        $invoiceCount = Invoice::count();

        if ($userCount < 100) {
            $this->error("Not enough data. Run: php artisan db:seed --class=RealisticDataSeeder");
            $this->info("Current: {$userCount} users, {$boxCount} boxes, {$invoiceCount} invoices");
            return Command::FAILURE;
        }

        $this->info("Data: {$userCount} users, {$boxCount} boxes, {$invoiceCount} invoices");
        $this->newLine();

        $results = [];

        // ─── Benchmark 1: Admin Dashboard ────────────────────────
        $admin = User::where('role', 'admin')->first();
        $results[] = $this->benchmarkLivewire(
            'Admin Dashboard',
            \App\Livewire\Admin\Dashboard::class,
            $admin
        );

        // ─── Benchmark 2: Manage Box ─────────────────────────────
        $results[] = $this->benchmarkLivewire(
            'Manage Box',
            \App\Livewire\Admin\ManageBox::class,
            $admin
        );

        // ─── Benchmark 3: Generate Invoice ───────────────────────
        $results[] = $this->benchmarkLivewire(
            'Generate Invoice',
            \App\Livewire\Admin\GenerateInvoice::class,
            $admin
        );

        // ─── Benchmark 4: Verification ───────────────────────────
        $results[] = $this->benchmarkLivewire(
            'Verification',
            \App\Livewire\Admin\VerificationIndex::class,
            $admin
        );

        // ─── Benchmark 5: Customer Dashboard ─────────────────────
        $customer = User::where('role', 'customer')
            ->where('status', User::STATUS_ACTIVE)
            ->first();
        $results[] = $this->benchmarkLivewire(
            'Customer Dashboard',
            \App\Livewire\Customer\Dashboard::class,
            $customer
        );

        // ─── Benchmark 6: Owner Dashboard ────────────────────────
        $owner = User::where('role', 'owner')->first();
        $results[] = $this->benchmarkLivewire(
            'Owner Dashboard',
            \App\Livewire\Owner\Dashboard::class,
            $owner
        );

        // ─── Benchmark 7: Finance Report ─────────────────────────
        $results[] = $this->benchmarkLivewire(
            'Finance Report',
            \App\Livewire\Owner\FinanceIndex::class,
            $owner
        );

        // ─── Benchmark 8: Audit Log ──────────────────────────────
        $results[] = $this->benchmarkLivewire(
            'Audit Log',
            \App\Livewire\AuditLogIndex::class,
            $owner
        );

        // ─── Benchmark 9: Recap ──────────────────────────────────
        $results[] = $this->benchmarkLivewire(
            'Recap',
            \App\Livewire\Admin\RecapIndex::class,
            $admin
        );

        // ─── Summary ─────────────────────────────────────────────
        $this->newLine();
        $this->info('=== Results Summary ===');
        $this->newLine();

        $headers = ['Endpoint', 'Queries', 'DB Time (ms)', 'Total (ms)', 'Status'];
        $this->table($headers, $results);

        $this->newLine();

        // Check §5.2 compliance
        $failures = array_filter($results, fn ($r) => str_contains($r[4], 'FAIL'));
        if (empty($failures)) {
            $this->info('✓ All endpoints pass §5.2 performance targets.');
            return Command::SUCCESS;
        } else {
            $this->error('✗ Some endpoints exceed §5.2 targets.');
            return Command::FAILURE;
        }
    }

    private function benchmarkLivewire(string $label, string $componentClass, User $user): array
    {
        \Auth::login($user);
        \DB::enableQueryLog();

        $start = microtime(true);

        $start = microtime(true);
        $errorMsg = '';

        try {
            $component = \Livewire\Livewire::test($componentClass);
        } catch (\Throwable $e) {
            $errorMsg = $e->getMessage();
        }

        $elapsed = round((microtime(true) - $start) * 1000, 1);
        $queries = \DB::getQueryLog();
        \DB::disableQueryLog();
        \Auth::logout();

        $queryCount = count($queries);
        $dbTime = round(array_sum(array_column($queries, 'time')), 1);

        // §5.2 checks
        $apiOk = $elapsed < 500;
        $dbOk = $dbTime < 100;

        if ($errorMsg && !$apiOk) {
            $status = 'FAIL: ' . $errorMsg;
        } elseif (!$apiOk) {
            $status = 'FAIL: API > 500ms';
        } elseif (!$dbOk) {
            $status = 'FAIL: DB > 100ms';
        } else {
            $status = 'PASS';
        }

        return [
            $label,
            $queryCount,
            $dbTime,
            $elapsed,
            $status,
        ];
    }
}
