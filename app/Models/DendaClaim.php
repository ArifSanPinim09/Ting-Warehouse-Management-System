<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Denda Claim — Revisi §2.4, §3.2
 *
 * Tracks penalties for No Tuan item claims.
 * Rp 5.000 per item, tagged to invoice for billing.
 */
class DendaClaim extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'denda_claims';

    /**
     * Indicates if the model should be timestamped.
     *
     * Only created_at is used — denda claims are immutable records.
     *
     * @var string|null
     */
    const UPDATED_AT = null;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'item_id',
        'jumlah_denda',
        'invoice_id',
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
            'jumlah_denda' => 'decimal:2',
            'created_at' => 'datetime',
        ];
    }

    // ─── Status Constants (Revisi §3.2) ─────────────────────────

    const STATUS_PENDING = 'pending';
    const STATUS_TAGGED = 'tagged';
    const STATUS_PAID = 'paid';

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_TAGGED,
            self::STATUS_PAID,
        ];
    }

    // ─── Relationships (Revisi §3.2, §9) ────────────────────────

    /**
     * Customer who made the claim.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Item that was claimed.
     */
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    /**
     * Invoice this denda is tagged to (null if not yet billed).
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}
