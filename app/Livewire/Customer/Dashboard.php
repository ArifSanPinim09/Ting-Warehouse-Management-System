<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\WhChinaData;
use App\Services\FeeCalculationService;
use Livewire\Component;

/**
 * Customer Dashboard — §4.2, §8.4
 *
 * Komponen: Rate Card, Invoice Unpaid Card, Goods Card, Receipt Card,
 * Status Box Table, Notifikasi, Shortcuts, Unmatched WH Alert
 */
class Dashboard extends Component
{
    // ─── Detail Modal State ─────────────────────────────────────
    public ?int $detailBoxId = null;
    public bool $showDetail = false;

    public function openBoxDetail(int $boxId): void
    {
        $this->detailBoxId = $boxId;
        $this->showDetail = true;
    }

    public function closeBoxDetail(): void
    {
        $this->showDetail = false;
        $this->detailBoxId = null;
    }

    public function render()
    {
        $user = auth()->user();
        $now = now();
        $startOfMonth = $now->copy()->startOfMonth();

        // Single query for all invoice stats
        $invoiceStats = Invoice::where('customer_id', $user->id)
            ->selectRaw("
                SUM(CASE WHEN status = 'waiting_payment' THEN grand_total ELSE 0 END) as unpaid_total,
                SUM(CASE WHEN status = 'waiting_payment' THEN 1 ELSE 0 END) as unpaid_count
            ")->first();

        $unpaidInvoices = (float) ($invoiceStats->unpaid_total ?? 0);
        $unpaidCount = (int) ($invoiceStats->unpaid_count ?? 0);

        // REV-01.3: Hanya tampilkan sharing box dimana customer punya item
        // + direct box milik customer ini
        $activeBoxes = Box::where(function ($query) use ($user) {
                // (1) Sharing box: customer_id NULL + customer punya barang
                $query->where(function ($q) use ($user) {
                    $q->whereNull('customer_id')
                        ->whereHas('items', function ($items) use ($user) {
                            $items->where('customer_id', $user->id);
                        });
                })
                // (2) Sharing box milik customer ini
                ->orWhere('customer_id', $user->id);
            })
            ->whereIn('status', [Box::STATUS_OPEN, Box::STATUS_SENT_TO_CARGO, Box::STATUS_OTW_INA])
            ->count();

        // Single query for item stats this month
        $itemStats = Item::where('customer_id', $user->id)
            ->where('created_at', '>=', $startOfMonth)
            ->selectRaw("
                COUNT(*) as goods,
                SUM(CASE WHEN resi_number IS NOT NULL THEN 1 ELSE 0 END) as receipts
            ")->first();

        $goodsThisMonth = (int) ($itemStats->goods ?? 0);
        $receiptsThisMonth = (int) ($itemStats->receipts ?? 0);

        // Batch load settings (1 query instead of 3)
        $settings = Setting::whereIn('key', ['rate_sharing_air_berat', 'rate_sharing_sea_berat'])
            ->pluck('value', 'key');

        $rateAir = (float) ($settings['rate_sharing_air_berat'] ?? 255);
        $rateSea = (float) ($settings['rate_sharing_sea_berat'] ?? 70);

        // Kurs from history table (Revisi §2.2) — always use today's kurs
        $feeService = app(FeeCalculationService::class);
        $kursYuan = $feeService->getKursToday();

        // REV-01.3: Status Box - hanya sharing box yang ada barang customer + direct box milik customer
        $boxes = Box::where(function ($query) use ($user) {
                // (1) Sharing box: customer_id NULL + customer punya barang
                $query->where(function ($q) use ($user) {
                    $q->whereNull('customer_id')
                        ->whereHas('items', function ($items) use ($user) {
                            $items->where('customer_id', $user->id);
                        });
                })
                // (2) Sharing box milik customer ini
                ->orWhere('customer_id', $user->id);
            })
            ->withCount('items')
            ->with(['items' => function ($query) {
                $query->with('whChinaData');
            }])
            ->latest()
            ->paginate(15);

        // REV-01.3: Detail box - hanya sharing box yang ada barang customer + direct box milik customer
        $detailBox = null;
        if ($this->detailBoxId) {
            $detailBox = Box::where('id', $this->detailBoxId)
                ->where(function ($query) use ($user) {
                    // (1) Sharing box: customer_id NULL + customer punya barang
                    $query->where(function ($q) use ($user) {
                        $q->whereNull('customer_id')
                            ->whereHas('items', function ($items) use ($user) {
                                $items->where('customer_id', $user->id);
                            });
                    })
                    // (2) Sharing box milik customer ini / direct box milik customer
                    ->orWhere('customer_id', $user->id);
                })
                ->with(['items' => function ($query) {
                    $query->with('whChinaData');
                }])
                ->first();
        }

        // Recent notifications
        $notifications = Notification::where('notifiable_type', 'App\\Models\\User')
            ->where('notifiable_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

        // Unmatched WH China data count (for alert banner)
        $unmatchedWhCount = WhChinaData::whereNull('item_id')->count();

        // REV-01.2: Info Open Box Global — list box yang sedang OPEN
        $openBoxes = Box::where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->whereNull('customer_id')
                        ->whereHas('items', function ($items) use ($user) {
                            $items->where('customer_id', $user->id);
                        });
                })
                ->orWhere('customer_id', $user->id);
            })
            ->where('status', Box::STATUS_OPEN)
            ->pluck('batch_name', 'id');

        // REV-05.3: Redline boxes ticker
        $redlineBoxes = Box::where(function ($query) use ($user) {
                $query->where(function ($q) use ($user) {
                    $q->whereNull('customer_id')
                        ->whereHas('items', function ($items) use ($user) {
                            $items->where('customer_id', $user->id);
                        });
                })
                ->orWhere('customer_id', $user->id);
            })
            ->where('is_redline', true)
            ->get(['id', 'batch_name', 'huruf_box', 'redline_note']);

        return view('livewire.customer.dashboard.index', compact(
            'activeBoxes',
            'unpaidInvoices',
            'unpaidCount',
            'goodsThisMonth',
            'receiptsThisMonth',
            'kursYuan',
            'rateAir',
            'rateSea',
            'boxes',
            'detailBox',
            'notifications',
            'unmatchedWhCount',
            'openBoxes',
            'redlineBoxes',
        ))->layout('layouts.app');
    }
}
