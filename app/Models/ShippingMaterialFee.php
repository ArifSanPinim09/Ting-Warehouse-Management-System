<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShippingMaterialFee extends Model
{
    protected $table = 'shipping_material_fees';

    protected $fillable = [
        'category',
        'name',
        'biaya_yuan',
        'status',
        'batch_id',
        'notes',
        'input_by',
    ];

    protected $casts = [
        'biaya_yuan' => 'decimal:2',
    ];

    public const STATUS_PAID = 'PAID';
    public const STATUS_UNPAID = 'UNPAID';

    public const CATEGORIES = [
        'shipping' => 'Shipping',
        'material' => 'Material',
        'operational' => 'Operational',
        'other' => 'Other',
    ];

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
     */
    public function getBiayaRupiahAttribute(): float
    {
        $kurs = (float) Setting::getValue('kurs_yuan_idr', 2460);
        return (float) $this->biaya_yuan * $kurs;
    }
}
