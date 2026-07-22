<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CargoDestination extends Model
{
    protected $table = 'cargo_destinations';

    protected $fillable = [
        'code',
        'name',
        'address',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Get active destinations sorted by sort_order.
     */
    public static function getActive(): \Illuminate\Database\Eloquent\Collection
    {
        return static::where('is_active', true)
            ->orderBy('sort_order')
            ->get();
    }
}
