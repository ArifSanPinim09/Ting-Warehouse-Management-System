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
        'biaya_jasa' => 'decimal:2',
        'matched_at' => 'datetime',
        'tanggal_setor' => 'datetime',
    ];

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
