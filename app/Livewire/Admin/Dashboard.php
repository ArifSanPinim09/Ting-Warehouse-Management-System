<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Dashboard Admin — Ting Warehouse')]
class Dashboard extends Component
{
    // ─── Stat Properties ────────────────────────────────────────
    public int $sharingOpen = 0;
    public int $sharingClosed = 0;
    public int $directOpen = 0;
    public int $directClosed = 0;
    public int $handcarryOpen = 0;
    public int $handcarryClosed = 0;
    public int $customerActive = 0;
    public int $customerPending = 0;
    public int $totalCustomers = 0;
    public int $pendingVerifications = 0;
    public int $pendingCheckouts = 0;
    public int $pendingComplaints = 0;

    // ─── Collection Properties ──────────────────────────────────
    public $notifications;
    public $recentActivities;
    public $deadlinePayments;
    public $deadlineStorages;
    public $longStorageCustomers;

    public function mount(): void
    {
        $this->loadStats();
        $this->loadNotifications();
        $this->loadActivities();
        $this->loadDeadlines();
    }

    public function loadStats(): void
    {
        // Box stats by type & status
        $boxStats = Box::selectRaw("
            type,
            SUM(CASE WHEN status != 'DONE' THEN 1 ELSE 0 END) as open_count,
            SUM(CASE WHEN status = 'DONE' THEN 1 ELSE 0 END) as closed_count
        ")->groupBy('type')->get()->keyBy('type');

        $this->sharingOpen = (int) ($boxStats['sharing']?->open_count ?? 0);
        $this->sharingClosed = (int) ($boxStats['sharing']?->closed_count ?? 0);
        $this->directOpen = (int) ($boxStats['direct']?->open_count ?? 0);
        $this->directClosed = (int) ($boxStats['direct']?->closed_count ?? 0);
        $this->handcarryOpen = (int) ($boxStats['handcarry']?->open_count ?? 0);
        $this->handcarryClosed = (int) ($boxStats['handcarry']?->closed_count ?? 0);

        // Customer stats
        $this->customerActive = User::where('role', 'customer')->where('status', User::STATUS_ACTIVE)->count();
        $this->customerPending = User::where('role', 'customer')->where('status', User::STATUS_PENDING)->count();
        $this->totalCustomers = User::where('role', 'customer')->count();

        // Pending action counts
        $this->pendingVerifications = Invoice::where('status', Invoice::STATUS_WAITING_VERIFICATION)->count();
        $this->pendingCheckouts = Checkout::where('status', Checkout::STATUS_REQUEST)->count();
        $this->pendingComplaints = Complain::whereIn('status', [Complain::STATUS_OPEN, Complain::STATUS_IN_REVIEW])->count();
    }

    public function loadNotifications(): void
    {
        $this->notifications = Notification::where('notifiable_type', User::class)
            ->where('notifiable_id', auth()->id())
            ->latest()
            ->limit(8)
            ->get();
    }

    public function loadActivities(): void
    {
        $this->recentActivities = \App\Models\ActivityLog::with('user')
            ->latest()
            ->limit(10)
            ->get();
    }

    public function loadDeadlines(): void
    {
        // Invoices waiting payment (deadline tracking) — nimbun kelamaan + belum bayar
        $this->deadlinePayments = Invoice::with(['customer', 'box'])
            ->where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->where('payment_deadline', '<', now())
            ->orderBy('payment_deadline')
            ->limit(10)
            ->get();

        // Boxes that have been at warehouse for too long (storage deadline)
        $this->deadlineStorages = Box::with('customer')
            ->whereIn('status', [Box::STATUS_OPEN, Box::STATUS_OTW_INA])
            ->latest()
            ->limit(10)
            ->get();

        // Sprint 5B: Customers nimbun kelamaan — items di box OPEN > 14 hari
        $this->longStorageCustomers = \App\Models\User::where('role', 'customer')
            ->where('status', 'active')
            ->whereHas('items.box', function ($q) {
                $q->where('status', Box::STATUS_OPEN)
                  ->where('updated_at', '<', now()->subDays(14));
            })
            ->withCount(['items' => function ($q) {
                $q->whereHas('box', function ($bq) {
                    $bq->where('status', Box::STATUS_OPEN)
                      ->where('updated_at', '<', now()->subDays(14));
                });
            }])
            ->having('items_count', '>', 0)
            ->orderByDesc('items_count')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.admin.dashboard.index');
    }
}
