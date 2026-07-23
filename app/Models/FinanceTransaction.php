<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Sprint 5B: Finance Transaction — Biaya Operasional, Refund, Pemasukan lain.
 *
 * Kategori:
 * - operasional: sewa, listrik, gaji, dll
 * - refund: pengembalian uang ke customer
 * - pemasukan_lain: pemasukan non-invoice
 */
class FinanceTransaction extends Model
{
    use HasFactory;

    public const CATEGORY_OPERASIONAL = 'operasional';
    public const CATEGORY_REFUND = 'refund';
    public const CATEGORY_PEMASUKAN_LAIN = 'pemasukan_lain';

    protected $fillable = [
        'category',
        'description',
        'amount',
        'transaction_date',
        'input_by',
        'notes',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'date',
    ];

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }
}
