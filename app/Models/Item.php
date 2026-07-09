<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
        'sensitive_type',
        'arrived_china',
        'arrived_china_photo',
        'arrived_indonesia',
        'arrived_indonesia_photo',
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
            'is_sensitive' => 'boolean',
            'arrived_china' => 'boolean',
            'arrived_indonesia' => 'boolean',
            'price_yuan' => 'decimal:2',
        ];
    }

    // ─── Status Constants (Revisi §2.5.2) ──────────────────────────

    const STATUS_ACTIVE = 'active';
    const STATUS_NO_TUAN = 'no_tuan';
    const STATUS_CLAIMED = 'claimed';
    const STATUS_KLAIM_WH = 'klaim_wh';
    const STATUS_SHIPPED = 'shipped';

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
        ];
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
}
