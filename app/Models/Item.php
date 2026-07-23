<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'box_id',
        'customer_id',
        'name',
        'quantity',
        'price_yuan',
        'resi_number',
        'proof_co',
        'is_sensitive',
        'is_garment',
        'sensitive_type',
        'arrived_china',
        'arrived_china_photo',
        'arrived_indonesia',
        'arrived_indonesia_photo',
        'status',
        'add_on',
        'notes',
        'request_type',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_sensitive' => 'boolean',
            'is_garment' => 'boolean',
            'arrived_china' => 'boolean',
            'arrived_indonesia' => 'boolean',
            'price_yuan' => 'decimal:2',
        ];
    }

    // ─── Status Constants (Revisi §2.5.2, §2.10.2, §2.9) ─────────

    const STATUS_ACTIVE = 'active';
    const STATUS_NO_TUAN = 'no_tuan';
    const STATUS_CLAIMED = 'claimed';
    const STATUS_KLAIM_WH = 'klaim_wh';
    const STATUS_SHIPPED = 'shipped';
    const STATUS_HOLD = 'hold'; // Revisi §2.10.2: Auto hold for overdue items
    const STATUS_DIJUAL = 'dijual'; // Revisi §2.9: Barang ditandai untuk dijual
    const STATUS_LELANG = 'lelang'; // Revisi §2.9: Barang ditandai untuk dilelang

    // Sprint 5C: Status tambahan item
    const STATUS_OTW = 'otw';                       // On The Way — dalam perjalanan
    const STATUS_SEND_BACK = 'send_back_to_seller'; // Kirim balik ke seller China
    const STATUS_SEND_DIFF = 'send_to_diff_addr';   // Kirim ke alamat berbeda
    const STATUS_NEVER_ARRIVED = 'never_arrived';   // Barang tidak pernah sampai
    const STATUS_WRONG_ADDRESS = 'wrong_address';   // Salah alamat

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_ACTIVE,
            self::STATUS_NO_TUAN,
            self::STATUS_CLAIMED,
            self::STATUS_KLAIM_WH,
            self::STATUS_SHIPPED,
            self::STATUS_HOLD,
            self::STATUS_DIJUAL,
            self::STATUS_LELANG,
            self::STATUS_OTW,
            self::STATUS_SEND_BACK,
            self::STATUS_SEND_DIFF,
            self::STATUS_NEVER_ARRIVED,
            self::STATUS_WRONG_ADDRESS,
        ];
    }

    /**
     * Sprint 5C: Get human-readable label for status.
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            self::STATUS_ACTIVE => 'Active',
            self::STATUS_NO_TUAN => 'No Tuan',
            self::STATUS_CLAIMED => 'Claimed',
            self::STATUS_KLAIM_WH => 'Unknown',
            self::STATUS_SHIPPED => 'Shipped',
            self::STATUS_HOLD => 'Hold',
            self::STATUS_DIJUAL => 'Dijual',
            self::STATUS_LELANG => 'Lelang',
            self::STATUS_OTW => 'OTW',
            self::STATUS_SEND_BACK => 'Send Back to Seller',
            self::STATUS_SEND_DIFF => 'Send to Different Address',
            self::STATUS_NEVER_ARRIVED => 'Never Arrived',
            self::STATUS_WRONG_ADDRESS => 'Wrong Address',
            default => ucfirst($this->status ?? 'unknown'),
        };
    }

    /**
     * Check if item is eligible for lelang page (klaim_wh or hold status).
     *
     * @return bool
     */
    public function isLelangEligible(): bool
    {
        return in_array($this->status, [
            self::STATUS_KLAIM_WH,
            self::STATUS_HOLD,
            self::STATUS_DIJUAL,
            self::STATUS_LELANG,
        ]);
    }

    // ─── Relationships (ERD §18.1) ─────────────────────────────────

    /**
     * Box that contains this item.
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    /**
     * Customer who input this item.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * WH China data matched to this item.
     */
    public function whChinaData(): HasOne
    {
        return $this->hasOne(WhChinaData::class);
    }

    /**
     * Flexible invoices this item belongs to (Revisi §2.8).
     */
    public function invoices(): BelongsToMany
    {
        return $this->belongsToMany(Invoice::class, 'invoice_items')->withTimestamps();
    }

    /**
     * Check if this item is already in any invoice.
     */
    public function isInvoiced(): bool
    {
        return $this->invoices()->exists();
    }
}
