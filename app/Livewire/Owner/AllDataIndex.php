<?php

namespace App\Livewire\Owner;

use App\Models\Box;
use App\Models\Checkout;
use App\Models\Complain;
use App\Models\Invoice;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

/**
 * Owner All Data — §7.4, §6.3
 *
 * Semua data operasional: customer, box, invoice, complain dalam satu halaman.
 */
#[Layout('layouts.admin')]
#[Title('All Data — Ting Warehouse')]
class AllDataIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $activeTab = 'customers';
    #[Url]
    public string $search = '';

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingActiveTab(): void { $this->resetPage(); }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->search = '';
        $this->resetPage();
    }

    public function render()
    {
        $data = [];

        switch ($this->activeTab) {
            case 'customers':
                $query = User::where('role', 'customer');
                if ($this->search) {
                    $query->where(function ($q) {
                        $q->where('name', 'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%");
                    });
                }
                $data['items'] = $query->latest()->paginate(15);
                $data['total'] = User::where('role', 'customer')->count();
                $data['active'] = User::where('role', 'customer')->where('status', 'active')->count();
                break;

            case 'boxes':
                $query = Box::with('customer');
                if ($this->search) {
                    $query->where(function ($q) {
                        $q->where('tracking_number', 'like', "%{$this->search}%")
                          ->orWhere('batch_name', 'like', "%{$this->search}%")
                          ->orWhereHas('customer', function ($cq) {
                              $cq->where('name', 'like', "%{$this->search}%");
                          });
                    });
                }
                $data['items'] = $query->latest()->paginate(15);
                $data['total'] = Box::count();
                $data['active'] = Box::whereNotIn('status', ['DONE'])->count();
                break;

            case 'invoices':
                $query = Invoice::with(['customer', 'box']);
                if ($this->search) {
                    $query->where(function ($q) {
                        $q->where('invoice_number', 'like', "%{$this->search}%")
                          ->orWhereHas('customer', function ($cq) {
                              $cq->where('name', 'like', "%{$this->search}%");
                          });
                    });
                }
                $data['items'] = $query->latest()->paginate(15);
                $data['total'] = Invoice::count();
                $data['totalRevenue'] = Invoice::where('status', Invoice::STATUS_VERIFIED)->sum('grand_total');
                break;

            case 'complains':
                $query = Complain::with('customer');
                if ($this->search) {
                    $query->where(function ($q) {
                        $q->where('type', 'like', "%{$this->search}%")
                          ->orWhere('description', 'like', "%{$this->search}%")
                          ->orWhereHas('customer', function ($cq) {
                              $cq->where('name', 'like', "%{$this->search}%");
                          });
                    });
                }
                $data['items'] = $query->latest()->paginate(15);
                $data['total'] = Complain::count();
                $data['open'] = Complain::whereIn('status', [Complain::STATUS_OPEN, Complain::STATUS_IN_REVIEW])->count();
                break;
        }

        return view('livewire.owner.all-data.index', array_merge([
            'activeTab' => $this->activeTab,
        ], $data));
    }
}
