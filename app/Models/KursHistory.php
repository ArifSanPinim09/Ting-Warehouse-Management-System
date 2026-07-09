<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class KursHistory extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'kurs_history';

    /**
     * Indicates if the model should be timestamped.
     *
     * Only created_at is used (no updated_at) — history records are immutable.
     * UPDATED_AT is set to null so Eloquent only manages created_at.
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
        'kurs_value',
        'effective_date',
        'input_by',
        'created_at',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'kurs_value' => 'decimal:2',
            'effective_date' => 'date',
            'created_at' => 'datetime',
        ];
    }

    // ─── Relationships (Revisi §3.1, §9) ───────────────────────────

    /**
     * The admin/owner who input this kurs record.
     */
    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    // ─── Query Helpers ──────────────────────────────────────────────

    /**
     * Get the latest kurs value (most recent effective_date, then most recent created_at).
     *
     * @return static|null
     */
    public static function getLatest(): ?self
    {
        return static::orderByDesc('effective_date')
            ->orderByDesc('created_at')
            ->first();
    }

    /**
     * Get the kurs value effective on a specific date.
     *
     * Returns the kurs with the greatest effective_date that is <= $date.
     * Falls back to the earliest kurs if none exists before $date.
     *
     * Uses whereDate() to ensure correct comparison regardless of DB driver
     * (SQLite stores date columns as 'YYYY-MM-DD HH:MM:SS' strings).
     *
     * @param  \Illuminate\Support\Carbon|string  $date
     * @return static|null
     */
    public static function getKursOnDate($date): ?self
    {
        return static::whereDate('effective_date', '<=', $date)
            ->orderByDesc('effective_date')
            ->orderByDesc('created_at')
            ->first();
    }
}
