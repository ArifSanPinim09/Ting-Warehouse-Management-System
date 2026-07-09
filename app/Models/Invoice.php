<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_number',
        'box_id',
        'customer_id',
        'weight',
        'volume',
        'fee_tax',
        'fee_wh',
        'fee_packing',
        'add_on',
        'denda_total',
        'grand_total',
        'payment_method',
        'payment_proof',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'weight' => 'decimal:2',
            'volume' => 'decimal:2',
            'fee_tax' => 'decimal:2',
            'fee_wh' => 'decimal:2',
            'fee_packing' => 'decimal:2',
            'add_on' => 'decimal:2',
            'denda_total' => 'decimal:2',
            'grand_total' => 'decimal:2',
        ];
    }

    // ─── Status Constants ──────────────────────────────────────────

    const STATUS_WAITING_PAYMENT = 'waiting_payment';
    const STATUS_WAITING_VERIFICATION = 'waiting_verification';
    const STATUS_VERIFIED = 'verified';

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_WAITING_PAYMENT,
            self::STATUS_WAITING_VERIFICATION,
            self::STATUS_VERIFIED,
        ];
    }

    // ─── Relationships (ERD §18.1) ─────────────────────────────────

    /**
     * Box this invoice is for.
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    /**
     * Customer who receives this invoice.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Checkouts related to this invoice.
     */
    public function checkouts(): HasMany
    {
        return $this->hasMany(Checkout::class);
    }

    /**
     * Denda claims tagged to this invoice.
     */
    public function dendaClaims(): HasMany
    {
        return $this->hasMany(DendaClaim::class);
    }

    /**
     * Items in this flexible invoice (Revisi §2.8).
     */
    public function items(): BelongsToMany
    {
        return $this->belongsToMany(Item::class, 'invoice_items')->withTimestamps();
    }

    /**
     * Display-friendly box info. Works for both old (box_id) and flexible (junction) invoices.
     */
    public function getBoxInfoAttribute(): string
    {
        if ($this->box_id && $this->relationLoaded('box') ? $this->box : $this->box_id) {
            $box = $this->box;
            if ($box) {
                return $box->tracking_number ?? $box->batch_name ?? 'Box #' . $this->box_id;
            }
        }

        // Flexible invoice: gather boxes from items
        $boxes = $this->items->pluck('box')->filter()->unique('id');
        if ($boxes->isEmpty()) {
            return '-';
        }

        return $boxes->map(fn ($b) => $b->tracking_number ?? $b->batch_name ?? 'Box #' . $b->id)->join(', ');
    }

    /**
     * Check if this is a flexible invoice (no box_id, uses junction items).
     */
    public function isFlexible(): bool
    {
        return $this->box_id === null;
    }
}
