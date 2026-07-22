<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GoodsWeightFee extends Model
{
    protected $table = 'goods_weight_fees';

    protected $fillable = [
        'batch_id',
        'box_id',
        'berat_kg',
        'biaya_yuan',
        'status',
        'notes',
        'input_by',
    ];

    protected $casts = [
        'berat_kg' => 'decimal:2',
        'biaya_yuan' => 'decimal:2',
    ];

    public const STATUS_PAID = 'PAID';
    public const STATUS_UNPAID = 'UNPAID';

    public function box(): BelongsTo
    {
        return $this->belongsTo(Box::class);
    }

    public function inputBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    /**
     * Convert yuan to rupiah using current kurs.
     * Owner sees this value.
     */
    public function getBiayaRupiahAttribute(): float
    {
        $kurs = (float) Setting::getValue('kurs_yuan_idr', 2460);
        return (float) $this->biaya_yuan * $kurs;
    }
}
