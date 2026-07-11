<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhChinaData extends Model
{
    protected $table = 'wh_china_data';

    protected $fillable = [
        'resi_number',
        'berat',
        'berat_ina',
        'panjang',
        'lebar',
        'tinggi',
        'volume',
        'ukuran_box',
        'huruf_box',
        'biaya_jasa',
        'foto_barang',
        'foto_arrived_china',
        'foto_arrived_ina',
        'tanggal_setor',
        'item_id',
        'matched_at',
        'input_by',
    ];

    protected $casts = [
        'berat' => 'decimal:2',
        'berat_ina' => 'decimal:2',
        'panjang' => 'decimal:2',
        'lebar' => 'decimal:2',
        'tinggi' => 'decimal:2',
        'volume' => 'decimal:4',
        'biaya_jasa' => 'decimal:2',
        'matched_at' => 'datetime',
        'tanggal_setor' => 'datetime',
    ];

    /**
     * Calculate volume from dimensions: (P × L × T) / 6000
     */
    public function calculateVolume(): ?float
    {
        if ($this->panjang && $this->lebar && $this->tinggi) {
            return round(($this->panjang * $this->lebar * $this->tinggi) / 6000, 4);
        }
        return null;
    }

    /**
     * Get effective weight for fee calculation: MAX(berat_ina, volume)
     * Falls back to berat (China) if berat_ina not set.
     */
    public function getEffectiveWeight(): ?float
    {
        $weight = $this->berat_ina ?? $this->berat;
        $vol = $this->volume;

        if ($weight && $vol) {
            return max($weight, $vol);
        }

        return $weight ?? $vol;
    }

    /** @var list<string> Exclude biaya_jasa from serialization (sensitive, admin-only) */
    protected $hidden = ['biaya_jasa'];

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'input_by');
    }

    public function isMatched(): bool
    {
        return $this->item_id !== null;
    }
}
