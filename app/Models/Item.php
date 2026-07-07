<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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
}
