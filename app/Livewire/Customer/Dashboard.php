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

        // Stat cards data
        $activeBoxes = Box::where('customer_id', $user->id)
            ->whereIn('status', [Box::STATUS_OPEN, Box::STATUS_SENT_TO_CARGO, Box::STATUS_OTW_INA])
            ->count();

        $unpaidInvoices = Invoice::where('customer_id', $user->id)
            ->where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->sum('grand_total');

        $unpaidCount = Invoice::where('customer_id', $user->id)
            ->where('status', Invoice::STATUS_WAITING_PAYMENT)
            ->count();

        $goodsThisMonth = Item::where('customer_id', $user->id)
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $receiptsThisMonth = Item::where('customer_id', $user->id)
            ->whereNotNull('resi_number')
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        // Rates from settings
        $kursYuan = (float) Setting::getValue('kurs_yuan_idr', '2460');
        $rateAir = (float) Setting::getValue('rate_sharing_air_berat', '255');
        $rateSea = (float) Setting::getValue('rate_sharing_sea_berat', '70');

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
