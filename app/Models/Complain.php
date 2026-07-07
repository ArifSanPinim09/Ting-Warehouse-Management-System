<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Complain extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'customer_id',
        'box_id',
        'type',
        'resolution',
        'invoice_number',
        'resi_number',
        'description',
        'video_url',
        'photo_url',
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

    const STATUS_OPEN = 'open';
    const STATUS_IN_REVIEW = 'in_review';
    const STATUS_PROCESSING = 'processing';
    const STATUS_RESOLVED = 'resolved';

    /**
     * Get all valid statuses.
     *
     * @return array<string>
     */
    public static function getValidStatuses(): array
    {
        return [
            self::STATUS_OPEN,
            self::STATUS_IN_REVIEW,
            self::STATUS_PROCESSING,
            self::STATUS_RESOLVED,
        ];
    }

    // ─── Relationships (ERD §18.1) ─────────────────────────────────

    /**
     * Customer who filed this complain.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id');
    }

    /**
     * Box this complain is related to.
     */
    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }
}
