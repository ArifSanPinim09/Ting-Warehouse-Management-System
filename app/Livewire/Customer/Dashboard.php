<?php

namespace App\Livewire\Customer;

use App\Models\Box;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\Notification;
use App\Models\Setting;
use Livewire\Component;

/**
 * Customer Dashboard — §4.2, §8.4
 *
 * Komponen: Rate Card, Invoice Unpaid Card, Goods Card, Receipt Card,
 * Status Box List, Notifikasi, Shortcuts
 */
class Dashboard extends Component
{
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

        // Single query for box stats
        $activeBoxes = Box::where('customer_id', $user->id)
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
        $settings = Setting::whereIn('key', ['kurs_yuan_idr', 'rate_sharing_air_berat', 'rate_sharing_sea_berat'])
            ->pluck('value', 'key');

        $kursYuan = (float) ($settings['kurs_yuan_idr'] ?? 2460);
        $rateAir = (float) ($settings['rate_sharing_air_berat'] ?? 255);
        $rateSea = (float) ($settings['rate_sharing_sea_berat'] ?? 70);

        // Box list with status
        $boxes = Box::where('customer_id', $user->id)
            ->withCount('items')
            ->latest()
            ->limit(10)
            ->get();

        // Recent notifications
        $notifications = Notification::where('notifiable_type', 'App\Models\User')
            ->where('notifiable_id', $user->id)
            ->latest()
            ->limit(5)
            ->get();

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
            'notifications',
        ))->layout('layouts.app');
    }
}
