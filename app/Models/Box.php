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
        'cargo_destination',
        'cargo_tracking_number',
        'cargo_photo',
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
        'reminder_sent_at',
        'is_redline',
        'redline_note',
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

    // ─── Status Constants (Sprint 1: 13 status sesuai dokumen client) ──

    const STATUS_OPEN = 'OPEN';
    const STATUS_LAST_CLAIM = 'LAST_CLAIM';       // Was LAST_SETOR
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_REQUEST_TO_SEND = 'REQUEST_TO_SEND'; // Admin INA minta Admin China kirim
    const STATUS_SEND_TO_CARGO = 'SEND_TO_CARGO';     // Was SENT_TO_CARGO — Admin China klik SEND
    const STATUS_ARRIVED_AT_CARGO = 'ARRIVED_AT_CARGO';
    const STATUS_WAITING_FOR_DEPARTURE = 'WAITING_FOR_DEPARTURE';
    const STATUS_DEPARTURE = 'DEPARTURE';             // Isi tanggal ETD
    const STATUS_ARRIVED_INA = 'ARRIVED_INA';         // Was OTW_INA — isi tanggal ETA
    const STATUS_REDLINE = 'REDLINE';                 // Optional, ada redline_note
    const STATUS_STEVEDORING = 'STEVEDORING';         // Isi tanggal stevedoring
    const STATUS_CHECKED_BY_WH = 'CHECKED_BY_WH';     // Sedang hitung berat & ukuran
    const STATUS_INVOICE = 'INVOICE';                 // Was UP_INVOICE
    const STATUS_DONE = 'DONE';
    const STATUS_REQUEST_TO_CLOSE = 'REQUEST_TO_CLOSE'; // REV-04.6 (Direct only)

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_LAST_CLAIM,
            self::STATUS_CLOSED,
            self::STATUS_REQUEST_TO_SEND,
            self::STATUS_SEND_TO_CARGO,
            self::STATUS_ARRIVED_AT_CARGO,
            self::STATUS_WAITING_FOR_DEPARTURE,
            self::STATUS_DEPARTURE,
            self::STATUS_ARRIVED_INA,
            self::STATUS_REDLINE,
            self::STATUS_STEVEDORING,
            self::STATUS_CHECKED_BY_WH,
            self::STATUS_INVOICE,
            self::STATUS_DONE,
            self::STATUS_REQUEST_TO_CLOSE,
        ];
    }

    /**
     * Valid status transitions (Sprint 1: sesuai dokumen client flow).
     * Key = current status, Value = array of valid next statuses.
     */
    public static function getValidTransitions(): array
    {
        return [
            self::STATUS_OPEN => [self::STATUS_LAST_CLAIM, self::STATUS_CLOSED],
            self::STATUS_LAST_CLAIM => [self::STATUS_CLOSED],
            self::STATUS_CLOSED => [self::STATUS_REQUEST_TO_SEND],
            self::STATUS_REQUEST_TO_SEND => [self::STATUS_SEND_TO_CARGO],
            self::STATUS_SEND_TO_CARGO => [self::STATUS_ARRIVED_AT_CARGO],
            self::STATUS_ARRIVED_AT_CARGO => [self::STATUS_WAITING_FOR_DEPARTURE],
            self::STATUS_WAITING_FOR_DEPARTURE => [self::STATUS_DEPARTURE],
            self::STATUS_DEPARTURE => [self::STATUS_ARRIVED_INA, self::STATUS_REDLINE],
            self::STATUS_ARRIVED_INA => [self::STATUS_REDLINE, self::STATUS_STEVEDORING],
            self::STATUS_REDLINE => [self::STATUS_STEVEDORING],
            self::STATUS_STEVEDORING => [self::STATUS_CHECKED_BY_WH],
            self::STATUS_CHECKED_BY_WH => [self::STATUS_INVOICE],
            self::STATUS_INVOICE => [self::STATUS_DONE],
            self::STATUS_DONE => [],
            self::STATUS_REQUEST_TO_CLOSE => [self::STATUS_CLOSED],
        ];
    }

    /**
     * Check if a status transition is valid.
     */
    public function canTransitionTo(string $newStatus): bool
    {
        $transitions = self::getValidTransitions();
        $allowed = $transitions[$this->status] ?? [];
        return in_array($newStatus, $allowed);
    }

    /**
     * Get status label for display.
     */
    public function getStatusLabelAttribute(): string
    {
        $labels = [
            self::STATUS_OPEN => 'Open',
            self::STATUS_LAST_CLAIM => 'Last Claim',
            self::STATUS_CLOSED => 'Closed',
            self::STATUS_REQUEST_TO_SEND => 'Request to Send',
            self::STATUS_SEND_TO_CARGO => 'Send to Cargo',
            self::STATUS_ARRIVED_AT_CARGO => 'Arrived at Cargo',
            self::STATUS_WAITING_FOR_DEPARTURE => 'Waiting for Departure',
            self::STATUS_DEPARTURE => 'Departure',
            self::STATUS_ARRIVED_INA => 'Arrived INA',
            self::STATUS_REDLINE => 'Redline',
            self::STATUS_STEVEDORING => 'Stevedoring',
            self::STATUS_CHECKED_BY_WH => 'Checked by WH',
            self::STATUS_INVOICE => 'Invoice',
            self::STATUS_DONE => 'Done',
            self::STATUS_REQUEST_TO_CLOSE => 'Request to Close',
        ];

        return $labels[$this->status] ?? ucfirst(str_replace('_', ' ', $this->status));
    }

    // ─── Legacy aliases (backward compatibility) ──────────────────

    /** @deprecated Use STATUS_LAST_CLAIM */
    const STATUS_LAST_SETOR = self::STATUS_LAST_CLAIM;
    /** @deprecated Use STATUS_SEND_TO_CARGO */
    const STATUS_SENT_TO_CARGO = self::STATUS_SEND_TO_CARGO;
    /** @deprecated Use STATUS_ARRIVED_INA */
    const STATUS_OTW_INA = self::STATUS_ARRIVED_INA;
    /** @deprecated Use STATUS_INVOICE */
    const STATUS_UP_INVOICE = self::STATUS_INVOICE;

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
