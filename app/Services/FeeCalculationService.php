<?php

namespace App\Services;

use App\Models\KursHistory;
use App\Models\Setting;

/**
 * Fee Calculation Service — SATU-SATUNYA tempat rumus fee dihitung.
 *
 * Dilarang keras menulis ulang rumus ini di tempat lain manapun,
 * termasuk di Blade view atau Livewire component untuk "quick display".
 * (CLAUDE.md §3.2)
 *
 * PRD §4.8: Kalkulator Biaya
 * - Volume = (P × L × T) / 6000
 * - Dasar  = max(berat_aktual, volume)
 * - Fee TAX = Dasar × Rate
 * - Fee WH  = Tiered berdasarkan berat
 * - Fee Packing = Tiered (150/1000/2000 breakpoint + extra per kg)
 * - Grand Total = Fee TAX + Fee WH + Fee Packing + Add On
 *
 * PRD §4.12: 17 parameter rate dari tabel settings
 */
class FeeCalculationService
{
    /**
     * All rate keys from settings table (PRD §4.12).
     */
    private const RATE_KEYS = [
        'rate_sharing_air_berat',
        'rate_sharing_air_volume',
        'rate_sharing_sea_berat',
        'rate_sharing_sea_volume',
        'rate_sharing_sensitive_air_berat',
        'rate_sharing_sensitive_air_volume',
        'rate_sharing_sensitive_sea_berat',
        'rate_sharing_sensitive_sea_volume',
        'rate_direct_air_berat',
        'rate_direct_air_volume',
        'rate_direct_sea_berat',
        'rate_direct_sea_volume',
    ];

    private const PACKING_KEYS = [
        'fee_packing_150',
        'fee_packing_1000',
        'fee_packing_2000',
        'fee_packing_extra_per_kg',
    ];

    /**
     * Cached rates from settings table.
     *
     * @var array<string, float>
     */
    private array $rates = [];

    /**
     * Calculate all fees for a shipment.
     *
     * PRD §4.8: Main calculation entry point.
     * Used by: Kalkulator customer (§4.8), Generate Invoice admin (§4.10)
     *
     * @param  string  $type      'sharing' or 'direct'
     * @param  string  $method    'air' or 'sea'
     * @param  float   $weight    Berat aktual (kg)
     * @param  float   $length    Panjang (cm)
     * @param  float   $width     Lebar (cm)
     * @param  float   $height    Tinggi (cm)
     * @param  bool    $is_sensitive
     * @param  float   $addOn     Add on (opsional, admin input)
     * @param  float   $dendaTotal Denda klaim total (auto dari pending denda_claims)
     * @return array{
     *     volume: float,
     *     basis: float,
     *     fee_tax: float,
     *     fee_wh: float,
     *     fee_packing: float,
     *     add_on: float,
     *     denda_total: float,
     *     grand_total: float,
     *     rate_used: float,
     *     rate_key: string,
     * }
     */
    public function calculate(
        string $type,
        string $method,
        float $weight,
        float $length,
        float $width,
        float $height,
        bool $isSensitive = false,
        float $addOn = 0.0,
        float $dendaTotal = 0.0,
        ?float $customRate = null,
    ): array {
        $this->loadRates();

        $volume = $this->calculateVolume($length, $width, $height);
        $basis = $this->calculateBasis($weight, $volume);
        $rateKey = $this->getRateKey($type, $method, $isSensitive, $weight, $volume);

        // Per-customer rate override: if customRate is set, use it instead of global rate
        $rate = $customRate ?? ($this->rates[$rateKey] ?? 0);

        // REV-05.4: Direct Sea >25kg → rate -5 per unit (confirmed by client)
        if ($type === 'direct' && $method === 'sea' && $weight > 25 && $customRate === null) {
            $rate = max(0, $rate - 5);
        }

        $feeTax = $this->calculateFeeTax($basis, $rate);
        $feeWh = $this->calculateFeeWh($weight);
        $feePacking = $this->calculateFeePacking($weight);
        $grandTotal = $feeTax + $feeWh + $feePacking + $addOn + $dendaTotal;

        return [
            'volume' => round($volume, 2),
            'basis' => round($basis, 2),
            'fee_tax' => round($feeTax, 0),
            'fee_wh' => round($feeWh, 0),
            'fee_packing' => round($feePacking, 0),
            'add_on' => round($addOn, 0),
            'denda_total' => round($dendaTotal, 0),
            'grand_total' => round($grandTotal, 0),
            'rate_used' => $rate,
            'rate_key' => $rateKey,
        ];
    }

    /**
     * Calculate volume in m³ equivalent.
     *
     * PRD §4.8: Volume = (P × L × T) / 6000
     * Input in cm, output in kg equivalent (for weight comparison).
     *
     * @param  float  $length  Panjang (cm)
     * @param  float  $width   Lebar (cm)
     * @param  float  $height  Tinggi (cm)
     * @return float  Volume (m³ equivalent)
     */
    public function calculateVolume(float $length, float $width, float $height): float
    {
        return ($length * $width * $height) / 6000;
    }

    /**
     * Determine the basis for fee calculation.
     *
     * PRD §4.8: Dasar = max(berat_aktual, volume)
     *
     * @param  float  $weight   Berat aktual (kg)
     * @param  float  $volume   Volume (m³ equivalent)
     * @return float  Basis (the larger of weight or volume)
     */
    public function calculateBasis(float $weight, float $volume): float
    {
        return max($weight, $volume);
    }

    /**
     * Calculate Fee TAX.
     *
     * PRD §4.8: Fee TAX = Dasar × Rate
     *
     * @param  float  $basis  Dasar perhitungan
     * @param  float  $rate   Rate dari settings
     * @return float  Fee TAX
     */
    public function calculateFeeTax(float $basis, float $rate): float
    {
        return $basis * $rate;
    }

    /**
     * Calculate Fee WH (Warehouse Fee) — tiered based on weight.
     *
     * PRD §4.12: fee_packing_150(5000), fee_packing_1000(6500), fee_packing_2000(8000), fee_packing_extra_per_kg(1500)
     *
     * Tiered structure (berdasarkan CLAUDE.md):
     * - 0-150 kg:    fee_packing_150 (flat)
     * - 151-1000 kg: fee_packing_1000 (flat)
     * - 1001-2000 kg: fee_packing_2000 (flat)
     * - >2000 kg:    fee_packing_2000 + (berat - 2000) × fee_packing_extra_per_kg
     *
     * @param  float  $weight  Berat aktual (kg)
     * @return float  Fee WH
     */
    public function calculateFeeWh(float $weight): float
    {
        $this->loadRates();

        $fee150 = $this->rates['fee_packing_150'] ?? 5000;
        $fee1000 = $this->rates['fee_packing_1000'] ?? 6500;
        $fee2000 = $this->rates['fee_packing_2000'] ?? 8000;
        $extraPerKg = $this->rates['fee_packing_extra_per_kg'] ?? 1500;

        if ($weight <= 150) {
            return $fee150;
        }

        if ($weight <= 1000) {
            return $fee1000;
        }

        if ($weight <= 2000) {
            return $fee2000;
        }

        return $fee2000 + (($weight - 2000) * $extraPerKg);
    }

    /**
     * Calculate Fee Packing — tiered based on weight.
     *
     * PRD §4.12: fee_packing_150(5000), fee_packing_1000(6500), fee_packing_2000(8000), fee_packing_extra_per_kg(1500)
     *
     * Same tiered structure as Fee WH:
     * - 0-150 kg:    fee_packing_150 (flat)
     * - 151-1000 kg: fee_packing_1000 (flat)
     * - 1001-2000 kg: fee_packing_2000 (flat)
     * - >2000 kg:    fee_packing_2000 + (berat - 2000) × fee_packing_extra_per_kg
     *
     * @param  float  $weight  Berat aktual (kg)
     * @return float  Fee Packing
     */
    public function calculateFeePacking(float $weight): float
    {
        // Same tiered logic as Fee WH
        return $this->calculateFeeWh($weight);
    }

    /**
     * Get a single rate value from settings.
     *
     * @param  string  $key
     * @return float
     */
    public function getRate(string $key): float
    {
        $this->loadRates();

        return $this->rates[$key] ?? 0;
    }

    /**
     * Get all current rates from settings.
     *
     * @return array<string, float>
     */
    public function getAllRates(): array
    {
        $this->loadRates();

        return $this->rates;
    }

    // ─── Kurs Methods (Revisi §2.2) ─────────────────────────────

    /**
     * Get today's kurs value from kurs_history.
     *
     * Returns the kurs with the greatest effective_date <= today.
     * Used by: Customer dashboard display ("Kurs Hari Ini").
     *
     * @return float Kurs value (0 if no kurs record exists)
     */
    public function getKursToday(): float
    {
        $kurs = KursHistory::getKursOnDate(now());

        return $kurs ? (float) $kurs->kurs_value : 0;
    }

    /**
     * Get kurs value effective on a specific transaction date.
     *
     * Returns the kurs with the greatest effective_date <= $transactionDate.
     * Used by: Invoice generation, historical calculator — MUST use kurs at
     * transaction time, not current kurs (CLAUDE.md §3.3 / Revisi §2.2).
     *
     * @param  \Illuminate\Support\Carbon|string|null  $transactionDate  Defaults to today if null
     * @return float Kurs value (0 if no kurs record exists before that date)
     */
    public function getKursOnDate($transactionDate = null): float
    {
        $date = $transactionDate ?? now();
        $kurs = KursHistory::getKursOnDate($date);

        return $kurs ? (float) $kurs->kurs_value : 0;
    }

    // ─── Private Helpers ───────────────────────────────────────────

    /**
     * Load rates from settings table. Cached per request.
     */
    private function loadRates(): void
    {
        if (! empty($this->rates)) {
            return;
        }

        $keys = array_merge(self::RATE_KEYS, self::PACKING_KEYS);

        $settings = Setting::whereIn('key', $keys)->pluck('value', 'key');

        foreach ($keys as $key) {
            $this->rates[$key] = (float) ($settings[$key] ?? 0);
        }
    }

    /**
     * Determine the correct rate key based on type, method, sensitivity, and basis.
     *
     * PRD §4.12: 12 varian rate
     * - sharing: air/sea × berat/volume × normal/sensitive (8 variants)
     * - direct: air/sea × berat/volume (4 variants, NO sensitive)
     * - Pilih berat/volume berdasarkan mana yang lebih besar (basis)
     *
     * @param  string  $type        'sharing' or 'direct'
     * @param  string  $method      'air' or 'sea'
     * @param  bool    $isSensitive
     * @param  float   $weight      Berat aktual
     * @param  float   $volume      Volume
     * @return string  Rate key
     */
    private function getRateKey(
        string $type,
        string $method,
        bool $isSensitive,
        float $weight,
        float $volume,
    ): string {
        $basis = ($weight >= $volume) ? 'berat' : 'volume';

        // PRD §4.12: Hanya sharing yang punya sensitive rate
        if ($isSensitive && $type === 'sharing') {
            return "rate_sharing_sensitive_{$method}_{$basis}";
        }

        return "rate_{$type}_{$method}_{$basis}";
    }
}
