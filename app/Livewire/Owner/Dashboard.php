<?php

namespace App\Livewire\Owner;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use App\Models\ActivityLog;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard Owner — Ting Warehouse')]
class Dashboard extends Component
{
    // ─── Revenue Stats ────────────────────────────────────────────
    public float $revenueThisMonth = 0;
    public float $revenueLastMonth = 0;
    public float $revenueTotal = 0;
    public float $outstanding = 0;
    public float $revenueGrowth = 0;

    // ─── Customer Stats ──────────────────────────────────────────
    public int $totalCustomers = 0;
    public int $activeCustomers = 0;
    public int $newCustomersThisMonth = 0;

    // ─── Box / Shipping Stats ────────────────────────────────────
    public int $totalBoxes = 0;
    public int $activeBoxes = 0;
    public int $completedBoxes = 0;
    public int $boxesThisMonth = 0;

    // ─── Invoice Stats ───────────────────────────────────────────
    public int $totalInvoices = 0;
    public int $pendingPayments = 0;
    public int $pendingVerifications = 0;
    public int $verifiedInvoices = 0;

    // ─── Complaint Stats ─────────────────────────────────────────
    public int $openComplaints = 0;
    public int $totalComplaints = 0;

    // ─── Checkout Stats ──────────────────────────────────────────
    public int $pendingCheckouts = 0;

    // ─── Collections ─────────────────────────────────────────────
    public $notifications;
    public $recentActivities;
    public $revenueByMonth;
    public $topCustomers;
    public $recentInvoices;

    public function mount(): void
    {
        $this->loadStats();
        $this->loadCollections();
    }

    /**
     * Consolidated stats query — reduces 11 separate queries to 4.
     */
    private function loadStats(): void
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // ── Invoice stats (1 query with conditional aggregation) ──
        $invoiceStats = Invoice::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'waiting_payment' THEN 1 ELSE 0 END) as pending_payment,
            SUM(CASE WHEN status = 'waiting_verification' THEN 1 ELSE 0 END) as pending_verification,
            SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified,
            SUM(CASE WHEN status = 'verified' THEN grand_total ELSE 0 END) as revenue_total,
            SUM(CASE WHEN status = 'verified' AND updated_at >= ? THEN grand_total ELSE 0 END) as revenue_this_month,
            SUM(CASE WHEN status = 'verified' AND updated_at >= ? AND updated_at <= ? THEN grand_total ELSE 0 END) as revenue_last_month,
            SUM(CASE WHEN status IN ('waiting_payment', 'waiting_verification') THEN grand_total ELSE 0 END) as outstanding
        ", [$startOfMonth, $startOfLastMonth, $endOfLastMonth])->first();

        $this->totalInvoices = (int) $invoiceStats->total;
        $this->pendingPayments = (int) $invoiceStats->pending_payment;
        $this->pendingVerifications = (int) $invoiceStats->pending_verification;
        $this->verifiedInvoices = (int) $invoiceStats->verified;
        $this->revenueTotal = (float) $invoiceStats->revenue_total;
        $this->revenueThisMonth = (float) $invoiceStats->revenue_this_month;
        $this->revenueLastMonth = (float) $invoiceStats->revenue_last_month;
        $this->outstanding = (float) $invoiceStats->outstanding;

        $this->revenueGrowth = $this->revenueLastMonth > 0
            ? round((($this->revenueThisMonth - $this->revenueLastMonth) / $this->revenueLastMonth) * 100, 1)
            : ($this->revenueThisMonth > 0 ? 100 : 0);

        // ── User stats (1 query with conditional aggregation) ─────
        $userStats = User::where('role', 'customer')->selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as new_this_month
        ", [$startOfMonth])->first();

        $this->totalCustomers = (int) $userStats->total;
        $this->activeCustomers = (int) $userStats->active;
        $this->newCustomersThisMonth = (int) $userStats->new_this_month;

        // ── Box stats (1 query with conditional aggregation) ──────
        $boxStats = Box::selectRaw("
            COUNT(*) as total,
            SUM(CASE WHEN status = 'DONE' THEN 1 ELSE 0 END) as completed,
            SUM(CASE WHEN status IN ('OPEN', 'SENT_TO_CARGO', 'OTW_INA', 'UP_INVOICE') THEN 1 ELSE 0 END) as active,
            SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END) as this_month
        ", [$startOfMonth])->first();

        $this->totalBoxes = (int) $boxStats->total;
        $this->completedBoxes = (int) $boxStats->completed;
        $this->activeBoxes = (int) $boxStats->active;
        $this->boxesThisMonth = (int) $boxStats->this_month;

        // ── Complaint + Checkout stats (1 query) ──────────────────
        $this->openComplaints = Complain::whereIn('status', [
            Complain::STATUS_OPEN, Complain::STATUS_IN_REVIEW,
        ])->count();
        $this->totalComplaints = Complain::count();
        $this->pendingCheckouts = Checkout::where('status', Checkout::STATUS_REQUEST)->count();
    }

    /**
     * Load all collection data — 5 queries with eager loading.
     */
    private function loadCollections(): void
    {
        $this->notifications = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', auth()->id())
            ->latest()
            ->limit(8)
            ->get();

        $this->recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();

        // Revenue chart — single query, group in PHP
        $invoices = Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->where('updated_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['grand_total', 'updated_at']);

        $grouped = $invoices->groupBy(fn ($inv) => $inv->updated_at->format('Y-m'))
            ->mapWithKeys(fn ($group, $key) => [$key => (float) $group->sum('grand_total')]);

        $filled = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $filled[$key] = $grouped[$key] ?? 0;
        }
        $this->revenueByMonth = $filled;

        $this->topCustomers = Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->selectRaw('customer_id, SUM(grand_total) as total_spent, COUNT(*) as invoice_count')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->with('customer:id,name,email')
            ->get();

        $this->recentInvoices = Invoice::with(['customer:id,name', 'box:id,tracking_number,batch_name'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.owner.dashboard.index');
    }
}
