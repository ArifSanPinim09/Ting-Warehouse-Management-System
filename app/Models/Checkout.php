<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Checkout extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'invoice_id',
        'customer_id',
        'address_type',
        'recipient_name',
        'recipient_phone',
        'address',
        'sender_name',
        'sender_phone',
        'packing_photo',
        'tracking_number',
        'status',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [];
    }

    // ─── Status Constants ──────────────────────────────────────────

    const STATUS_REQUEST = 'request';
    const STATUS_ON_PROCESS = 'on_process';
    const STATUS_SENT = 'sent';

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_REQUEST,
            self::STATUS_ON_PROCESS,
            self::STATUS_SENT,
        ];
    }

    // ─── Relationships (ERD §18.1) ─────────────────────────────────

    /**
     * Invoice this checkout is for.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Customer who requested this checkout.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }
}
