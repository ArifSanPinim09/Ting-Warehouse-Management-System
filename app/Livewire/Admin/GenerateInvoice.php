<?php

namespace App\Livewire\Admin;

use App\Models\Box;
use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Services\AuditLogService;
use App\Services\FeeCalculationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Generate Invoice — Ting Warehouse')]
class GenerateInvoice extends Component
{
    // ─── Search & Filter ────────────────────────────────────────
    #[Url]
    public string $search = '';
    #[Url]
    public string $filterStatus = '';

    // ─── Invoice Generation Form ────────────────────────────────
    public bool $showGenerateModal = false;
    public ?int $selectedBoxId = null;
    public string $weight = '';
    public string $length = '';
    public string $width = '';
    public string $height = '';
    public string $addOn = '0';

    // ─── Preview Calculated Values ──────────────────────────────
    public ?array $preview = null;

    // ─── Detail ─────────────────────────────────────────────────
    public ?int $selectedInvoiceId = null;
    public bool $showDetail = false;

    // ─── Watchers for Live Preview ──────────────────────────────
    public function updatedWeight(): void { $this->calculatePreview(); }
    public function updatedLength(): void { $this->calculatePreview(); }
    public function updatedWidth(): void { $this->calculatePreview(); }
    public function updatedHeight(): void { $this->calculatePreview(); }
    public function updatedAddOn(): void { $this->calculatePreview(); }
    public function updatedSelectedBoxId(): void { $this->calculatePreview(); }

    public function calculatePreview(): void
    {
        if (!$this->selectedBoxId || !$this->weight || !$this->length || !$this->width || !$this->height) {
            $this->preview = null;
            return;
        }

        $box = Box::find($this->selectedBoxId);
        if (!$box) {
            $this->preview = null;
            return;
        }

        $dendaTotal = $this->getPendingDendaTotal($box->customer_id);

        $feeService = app(FeeCalculationService::class);
        $this->preview = $feeService->calculate(
            type: $box->type,
            method: $box->method,
            weight: (float) $this->weight,
            length: (float) $this->length,
            width: (float) $this->width,
            height: (float) $this->height,
            isSensitive: false,
            addOn: (float) ($this->addOn ?: 0),
            dendaTotal: $dendaTotal,
        );
    }

    /**
     * Get sum of pending denda_claims for a customer (not yet tagged to any invoice).
     */
    private function getPendingDendaTotal(?int $customerId): float
    {
        if (!$customerId) return 0;

        return (float) DendaClaim::where('customer_id', $customerId)
            ->where('status', DendaClaim::STATUS_PENDING)
            ->whereNull('invoice_id')
            ->sum('jumlah_denda');
    }

    // ─── Actions ────────────────────────────────────────────────
    public function openGenerateModal(): void
    {
        $this->resetForm();
        $this->showGenerateModal = true;
    }

    public function closeGenerateModal(): void
    {
        $this->showGenerateModal = false;
        $this->resetForm();
    }

    public function generateInvoice(NotificationService $notifService, AuditLogService $auditService): void
    {
        $this->validate([
            'selectedBoxId' => 'required|exists:boxes,id',
            'weight' => 'required|numeric|min:0.1|max:99999',
            'length' => 'required|numeric|min:1|max:999',
            'width' => 'required|numeric|min:1|max:999',
            'height' => 'required|numeric|min:1|max:999',
            'addOn' => 'nullable|numeric|min:0|max:999999',
        ]);

        $box = Box::with('customer')->findOrFail($this->selectedBoxId);

        if (!$box->customer_id) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Box belum memiliki customer.');
            return;
        }

        // Check if invoice already exists for this box
        $existingInvoice = Invoice::where('box_id', $box->id)->where('status', '!=', 'verified')->first();
        if ($existingInvoice) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Box ini sudah memiliki invoice yang belum selesai.');
            return;
        }

        // Fetch pending denda claims BEFORE creating invoice (inside transaction)
        $pendingDenda = DendaClaim::where('customer_id', $box->customer_id)
            ->where('status', DendaClaim::STATUS_PENDING)
            ->whereNull('invoice_id')
            ->lockForUpdate()
            ->get();
        $dendaTotal = (float) $pendingDenda->sum('jumlah_denda');

        $feeService = app(FeeCalculationService::class);
        $fees = $feeService->calculate(
            type: $box->type,
            method: $box->method,
            weight: (float) $this->weight,
            length: (float) $this->length,
            width: (float) $this->width,
            height: (float) $this->height,
            isSensitive: false,
            addOn: (float) ($this->addOn ?: 0),
            dendaTotal: $dendaTotal,
        );

        DB::transaction(function () use ($box, $fees, $pendingDenda, $dendaTotal, $notifService, $auditService) {
            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'box_id' => $box->id,
                'customer_id' => $box->customer_id,
                'weight' => (float) $this->weight,
                'volume' => $fees['volume'],
                'fee_tax' => $fees['fee_tax'],
                'fee_wh' => $fees['fee_wh'],
                'fee_packing' => $fees['fee_packing'],
                'add_on' => $fees['add_on'],
                'denda_total' => $fees['denda_total'],
                'grand_total' => $fees['grand_total'],
                'status' => Invoice::STATUS_WAITING_PAYMENT,
            ]);

            // Tag pending denda claims to this invoice
            if ($pendingDenda->isNotEmpty()) {
                $pendingDendaIds = $pendingDenda->pluck('id');
                DendaClaim::whereIn('id', $pendingDendaIds)->update([
                    'invoice_id' => $invoice->id,
                    'status' => DendaClaim::STATUS_TAGGED,
                ]);
            }

            // Update box status
            $box->status = Box::STATUS_UP_INVOICE;
            $box->save();

            // Audit logs
            $auditService->logCustom($invoice, 'generated', "Invoice {$invoiceNumber} dibuat untuk box #{$box->id}" . ($dendaTotal > 0 ? " (denda: Rp " . number_format($dendaTotal, 0, ',', '.') . ")" : ''));
            $auditService->logCustom($box, 'status_changed', "Status box berubah dari OTW_INA ke UP_INVOICE");

            // Notify customer
            $notifService->invoiceGenerated($invoice);
        });

        $this->showGenerateModal = false;
        $this->resetForm();

        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: "Invoice berhasil dibuat.");
    }

    public function selectInvoice(int $id): void
    {
        $this->selectedInvoiceId = $id;
        $this->showDetail = true;
    }

    public function closeDetail(): void
    {
        $this->showDetail = false;
        $this->selectedInvoiceId = null;
    }

    private function resetForm(): void
    {
        $this->selectedBoxId = null;
        $this->weight = '';
        $this->length = '';
        $this->width = '';
        $this->height = '';
        $this->addOn = '0';
        $this->preview = null;
    }

    // ─── Computed ───────────────────────────────────────────────
    public function getAvailableBoxesProperty()
    {
        return Box::with('customer')
            ->where('status', Box::STATUS_OTW_INA)
            ->whereNotNull('customer_id')
            ->latest()
            ->get();
    }

    /**
     * Get pending denda info for the selected box's customer.
     * Returns ['count' => int, 'total' => float] or null if none.
     */
    public function getPendingDendaInfoProperty(): ?array
    {
        if (!$this->selectedBoxId) return null;

        $box = Box::find($this->selectedBoxId);
        if (!$box || !$box->customer_id) return null;

        $claims = DendaClaim::where('customer_id', $box->customer_id)
            ->where('status', DendaClaim::STATUS_PENDING)
            ->whereNull('invoice_id')
            ->get();

        if ($claims->isEmpty()) return null;

        return [
            'count' => $claims->count(),
            'total' => (float) $claims->sum('jumlah_denda'),
        ];
    }

    public function getSelectedInvoiceProperty(): ?Invoice
    {
        if (!$this->selectedInvoiceId) return null;
        return Invoice::with(['box', 'customer'])->find($this->selectedInvoiceId);
    }

    public function render()
    {
        $query = Invoice::with(['box', 'customer']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('invoice_number', 'like', "%{$this->search}%")
                  ->orWhereHas('customer', function ($cq) {
                      $cq->where('name', 'like', "%{$this->search}%");
                  })
                  ->orWhereHas('box', function ($bq) {
                      $bq->where('tracking_number', 'like', "%{$this->search}%");
                  });
            });
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $invoices = $query->latest()->paginate(15);

        return view('livewire.admin.invoices.index', [
            'invoices' => $invoices,
            'availableBoxes' => $this->available_boxes,
            'selectedInvoice' => $this->selected_invoice,
        ]);
    }
}
