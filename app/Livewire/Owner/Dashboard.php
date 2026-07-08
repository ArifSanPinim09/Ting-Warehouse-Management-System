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
        $this->loadRevenueStats();
        $this->loadCustomerStats();
        $this->loadBoxStats();
        $this->loadInvoiceStats();
        $this->loadComplaintStats();
        $this->loadCheckoutStats();
        $this->loadNotifications();
        $this->loadActivities();
        $this->loadCharts();
        $this->loadTopCustomers();
        $this->loadRecentInvoices();
    }

    private function loadRevenueStats(): void
    {
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        $this->revenueThisMonth = (float) Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->where('updated_at', '>=', $startOfMonth)
            ->sum('grand_total');

        $this->revenueLastMonth = (float) Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->whereBetween('updated_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('grand_total');

        $this->revenueTotal = (float) Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->sum('grand_total');

        $this->outstanding = (float) Invoice::whereIn('status', [
            Invoice::STATUS_WAITING_PAYMENT,
            Invoice::STATUS_WAITING_VERIFICATION,
        ])->sum('grand_total');

        $this->revenueGrowth = $this->revenueLastMonth > 0
            ? round((($this->revenueThisMonth - $this->revenueLastMonth) / $this->revenueLastMonth) * 100, 1)
            : ($this->revenueThisMonth > 0 ? 100 : 0);
    }

    private function loadCustomerStats(): void
    {
        $this->totalCustomers = User::where('role', 'customer')->count();
        $this->activeCustomers = User::where('role', 'customer')
            ->where('status', User::STATUS_ACTIVE)
            ->count();
        $this->newCustomersThisMonth = User::where('role', 'customer')
            ->where('created_at', '>=', now()->startOfMonth())
            ->count();
    }

    private function loadBoxStats(): void
    {
        $this->totalBoxes = Box::count();
        $this->activeBoxes = Box::whereIn('status', [
            Box::STATUS_OPEN,
            Box::STATUS_SENT_TO_CARGO,
            Box::STATUS_OTW_INA,
            Box::STATUS_UP_INVOICE,
        ])->count();
        $this->completedBoxes = Box::where('status', Box::STATUS_DONE)->count();
        $this->boxesThisMonth = Box::where('created_at', '>=', now()->startOfMonth())->count();
    }

    private function loadInvoiceStats(): void
    {
        $this->totalInvoices = Invoice::count();
        $this->pendingPayments = Invoice::where('status', Invoice::STATUS_WAITING_PAYMENT)->count();
        $this->pendingVerifications = Invoice::where('status', Invoice::STATUS_WAITING_VERIFICATION)->count();
        $this->verifiedInvoices = Invoice::where('status', Invoice::STATUS_VERIFIED)->count();
    }

    private function loadComplaintStats(): void
    {
        $this->openComplaints = Complain::whereIn('status', [
            Complain::STATUS_OPEN,
            Complain::STATUS_IN_REVIEW,
        ])->count();
        $this->totalComplaints = Complain::count();
    }

    private function loadCheckoutStats(): void
    {
        $this->pendingCheckouts = Checkout::where('status', Checkout::STATUS_REQUEST)->count();
    }

    private function loadNotifications(): void
    {
        $this->notifications = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', auth()->id())
            ->latest()
            ->limit(8)
            ->get();
    }

    private function loadActivities(): void
    {
        $this->recentActivities = ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();
    }

    private function loadCharts(): void
    {
        // Revenue by month for last 6 months (DB-agnostic: group in PHP)
        $invoices = Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->where('updated_at', '>=', now()->subMonths(5)->startOfMonth())
            ->get(['grand_total', 'updated_at']);

        $grouped = $invoices->groupBy(function ($inv) {
            return $inv->updated_at->format('Y-m');
        })->mapWithKeys(function ($group, $key) {
            return [$key => (float) $group->sum('grand_total')];
        });

        // Fill missing months with 0
        $filled = collect();
        for ($i = 5; $i >= 0; $i--) {
            $key = now()->subMonths($i)->format('Y-m');
            $filled[$key] = $grouped[$key] ?? 0;
        }
        $this->revenueByMonth = $filled;
    }

    private function loadTopCustomers(): void
    {
        $this->topCustomers = Invoice::where('status', Invoice::STATUS_VERIFIED)
            ->selectRaw('customer_id, SUM(grand_total) as total_spent, COUNT(*) as invoice_count')
            ->groupBy('customer_id')
            ->orderByDesc('total_spent')
            ->limit(5)
            ->with('customer:id,name,email')
            ->get();
    }

    private function loadRecentInvoices(): void
    {
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
