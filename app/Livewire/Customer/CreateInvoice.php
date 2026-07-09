<?php

namespace App\Livewire\Customer;

use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Models\Item;
use App\Services\FeeCalculationService;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

/**
 * Buat Invoice Fleksibel — Revisi §2.8.3
 *
 * Customer pilih barang (Shopee-cart style) → sistem hitung fee dari agregat.
 * Rate ditentukan dari box item pertama yang dipilih.
 */
class CreateInvoice extends Component
{
    // ─── Selection State ───────────────────────────────────────
    public array $selectedItems = [];

    // ─── Dimensions (customer input for the shipment box) ──────
    public string $length = '';
    public string $width = '';
    public string $height = '';

    // ─── Preview ───────────────────────────────────────────────
    public ?array $preview = null;

    // ─── UI State ──────────────────────────────────────────────
    public bool $creating = false;

    public function toggleItem(int $itemId): void
    {
        $key = array_search($itemId, $this->selectedItems);
        if ($key !== false) {
            unset($this->selectedItems[$key]);
            $this->selectedItems = array_values($this->selectedItems);
        } else {
            $this->selectedItems[] = $itemId;
        }
        $this->calculatePreview();
    }

    public function toggleAll(): void
    {
        $items = $this->getAvailableItemsProperty();
        if (count($this->selectedItems) === $items->count()) {
            $this->selectedItems = [];
        } else {
            $this->selectedItems = $items->pluck('id')->toArray();
        }
        $this->calculatePreview();
    }

    public function updatedLength(): void { $this->calculatePreview(); }
    public function updatedWidth(): void { $this->calculatePreview(); }
    public function updatedHeight(): void { $this->calculatePreview(); }

    public function calculatePreview(): void
    {
        if (empty($this->selectedItems) || !$this->length || !$this->width || !$this->height) {
            $this->preview = null;
            return;
        }

        $items = Item::whereIn('id', $this->selectedItems)
            ->with(['box', 'whChinaData'])
            ->get();

        if ($items->isEmpty()) {
            $this->preview = null;
            return;
        }

        // Aggregate weight from WH China data
        $totalWeight = 0;
        foreach ($items as $item) {
            $totalWeight += $item->whChinaData?->berat ?? 0;
        }

        // If no WH China data, use 0 (customer should ensure items have WH data)
        if ($totalWeight <= 0) {
            $this->preview = null;
            return;
        }

        // Rate from first item's box
        $firstBox = $items->first()->box;
        $type = $firstBox->type;
        $method = $firstBox->method;
        $isSensitive = $items->contains('is_sensitive', true);

        // Pending denda for this customer
        $dendaTotal = (float) DendaClaim::where('customer_id', auth()->id())
            ->where('status', DendaClaim::STATUS_PENDING)
            ->whereNull('invoice_id')
            ->sum('jumlah_denda');

        $feeService = app(FeeCalculationService::class);
        $this->preview = $feeService->calculate(
            type: $type,
            method: $method,
            weight: $totalWeight,
            length: (float) $this->length,
            width: (float) $this->width,
            height: (float) $this->height,
            isSensitive: $isSensitive,
            addOn: 0,
            dendaTotal: $dendaTotal,
        );
        $this->preview['item_count'] = $items->count();
        $this->preview['total_weight'] = $totalWeight;
    }

    public function createInvoice(NotificationService $notifService): void
    {
        if (empty($this->selectedItems)) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Pilih minimal 1 barang.');
            return;
        }

        $this->validate([
            'length' => 'required|numeric|min:1|max:999',
            'width' => 'required|numeric|min:1|max:999',
            'height' => 'required|numeric|min:1|max:999',
        ], [
            'length.required' => 'Panjang box wajib diisi',
            'width.required' => 'Lebar box wajib diisi',
            'height.required' => 'Tinggi box wajib diisi',
        ]);

        $this->creating = true;

        $items = Item::whereIn('id', $this->selectedItems)
            ->where('customer_id', auth()->id())
            ->with(['box', 'whChinaData'])
            ->get();

        if ($items->isEmpty() || $items->count() !== count($this->selectedItems)) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Barang tidak valid.');
            $this->creating = false;
            return;
        }

        // Check none are already invoiced
        $alreadyInvoiced = $items->filter(fn ($item) => $item->isInvoiced());
        if ($alreadyInvoiced->isNotEmpty()) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Beberapa barang sudah masuk invoice lain.');
            $this->creating = false;
            return;
        }

        // Aggregate weight
        $totalWeight = $items->sum(fn ($item) => $item->whChinaData?->berat ?? 0);
        if ($totalWeight <= 0) {
            $this->dispatch('toast', type: 'error', title: 'Error', message: 'Barang belum memiliki data berat dari WH China.');
            $this->creating = false;
            return;
        }

        // Rate from first item's box
        $firstBox = $items->first()->box;
        $type = $firstBox->type;
        $method = $firstBox->method;
        $isSensitive = $items->contains('is_sensitive', true);

        DB::transaction(function () use ($items, $totalWeight, $type, $method, $isSensitive, $notifService) {
            // Pending denda
            $pendingDenda = DendaClaim::where('customer_id', auth()->id())
                ->where('status', DendaClaim::STATUS_PENDING)
                ->whereNull('invoice_id')
                ->lockForUpdate()
                ->get();
            $dendaTotal = (float) $pendingDenda->sum('jumlah_denda');

            $feeService = app(FeeCalculationService::class);
            $fees = $feeService->calculate(
                type: $type,
                method: $method,
                weight: $totalWeight,
                length: (float) $this->length,
                width: (float) $this->width,
                height: (float) $this->height,
                isSensitive: $isSensitive,
                addOn: 0,
                dendaTotal: $dendaTotal,
            );

            // Generate invoice number
            $invoiceNumber = 'INV-' . date('Ymd') . '-' . str_pad(Invoice::count() + 1, 4, '0', STR_PAD_LEFT);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'box_id' => null, // flexible invoice — no single box
                'customer_id' => auth()->id(),
                'weight' => $totalWeight,
                'volume' => $fees['volume'],
                'fee_tax' => $fees['fee_tax'],
                'fee_wh' => $fees['fee_wh'],
                'fee_packing' => $fees['fee_packing'],
                'add_on' => $fees['add_on'],
                'denda_total' => $fees['denda_total'],
                'grand_total' => $fees['grand_total'],
                'status' => Invoice::STATUS_WAITING_PAYMENT,
            ]);

            // Attach items via junction table
            $invoice->items()->attach($items->pluck('id'));

            // Tag pending denda claims
            if ($pendingDenda->isNotEmpty()) {
                DendaClaim::whereIn('id', $pendingDenda->pluck('id'))->update([
                    'invoice_id' => $invoice->id,
                    'status' => DendaClaim::STATUS_TAGGED,
                ]);
            }

            // Notify admin
            $notifService->invoiceGenerated($invoice);
        });

        $this->selectedItems = [];
        $this->preview = null;
        $this->creating = false;

        $this->dispatch('toast',
            type: 'success',
            title: 'Berhasil',
            message: 'Invoice berhasil dibuat.',
        );
    }

    public function getAvailableItemsProperty()
    {
        return Item::where('customer_id', auth()->id())
            ->where('status', Item::STATUS_ACTIVE)
            ->where('arrived_indonesia', true)
            ->whereDoesntHave('invoices')
            ->with(['box', 'whChinaData'])
            ->latest()
            ->get();
    }

    public function render()
    {
        $availableItems = $this->available_items;

        return view('livewire.customer.create-invoice.index', [
            'availableItems' => $availableItems,
        ])->layout('layouts.app');
    }
}
