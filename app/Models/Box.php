<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Box extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'type',
        'tracking_number',
        'batch_name',
        'status',
        'method',
        'customer_id',
        'notes',
        'etd',
        'eta',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'etd' => 'date',
            'eta' => 'date',
        ];
    }

    // ─── Status Constants ──────────────────────────────────────────

    const STATUS_OPEN = 'OPEN';
    const STATUS_SENT_TO_CARGO = 'SENT_TO_CARGO';
    const STATUS_OTW_INA = 'OTW_INA';
    const STATUS_UP_INVOICE = 'UP_INVOICE';
    const STATUS_DONE = 'DONE';

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_SENT_TO_CARGO,
            self::STATUS_OTW_INA,
            self::STATUS_UP_INVOICE,
            self::STATUS_DONE,
        ];
    }

    // ─── Relationships (ERD §18.1) ─────────────────────────────────

    /**
     * Customer who owns this box.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Items in this box.
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class);
    }

    /**
     * Invoices for this box.
     */
    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    /**
     * Complains related to this box.
     */
    public function complains(): HasMany
    {
        return $this->hasMany(Complain::class);
    }
}
