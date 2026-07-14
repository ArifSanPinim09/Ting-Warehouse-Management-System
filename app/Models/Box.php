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
        'huruf_box',
        'status',
        'method',
        'customer_id',
        'notes',
        'etd',
        'eta',
        'stevedoring_date',
        'tagihan_update_date',
        'open_date',
        'close_date',
        'last_setor_date',
    ];

    /**
     * Get box display name: batch_name + huruf_box.
     * Contoh: "126-H"
     */
    public function getBoxCodeAttribute(): string
    {
        $parts = array_filter([
            $this->batch_name,
            $this->huruf_box,
        ]);
        return $parts ? implode('-', $parts) : ('Box #' . $this->id);
    }

    /**
     * Get display name (tracking_number > batch_name + huruf_box > Box #id).
     */
    public function getDisplayNameAttribute(): string
    {
        if ($this->tracking_number) {
            return $this->tracking_number;
        }
        return $this->box_code;
    }

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
            'stevedoring_date' => 'date',
            'tagihan_update_date' => 'date',
            'open_date' => 'datetime',
            'close_date' => 'datetime',
            'last_setor_date' => 'datetime',
        ];
    }

    // ─── Status Constants ──────────────────────────────────────────

    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';           // Revisi §2.3
    const STATUS_LAST_SETOR = 'LAST_SETOR';   // Revisi §2.3
    const STATUS_SENT_TO_CARGO = 'SENT_TO_CARGO';
    const STATUS_OTW_INA = 'OTW_INA';
    const STATUS_UP_INVOICE = 'UP_INVOICE';
    const STATUS_DONE = 'DONE';
    const STATUS_REQUEST_TO_CLOSE = 'REQUEST_TO_CLOSE'; // REV-04.6

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_CLOSED,
            self::STATUS_LAST_SETOR,
            self::STATUS_SENT_TO_CARGO,
            self::STATUS_OTW_INA,
            self::STATUS_UP_INVOICE,
            self::STATUS_DONE,
            self::STATUS_REQUEST_TO_CLOSE,
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
