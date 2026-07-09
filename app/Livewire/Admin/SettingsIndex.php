<?php

namespace App\Livewire\Admin;

use App\Models\Setting;
use App\Services\AuditLogService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('layouts.admin')]
#[Title('Pengaturan Rate — Ting Warehouse')]
class SettingsIndex extends Component
{
    public string $activeTab = 'sharing';

    // ─── Rate Values ────────────────────────────────────────────
    public string $rate_sharing_air_berat = '';
    public string $rate_sharing_air_volume = '';
    public string $rate_sharing_sea_berat = '';
    public string $rate_sharing_sea_volume = '';
    public string $rate_sharing_sensitive_air_berat = '';
    public string $rate_sharing_sensitive_air_volume = '';
    public string $rate_sharing_sensitive_sea_berat = '';
    public string $rate_sharing_sensitive_sea_volume = '';
    public string $rate_direct_air_berat = '';
    public string $rate_direct_air_volume = '';
    public string $rate_direct_sea_berat = '';
    public string $rate_direct_sea_volume = '';
    public string $fee_packing_150 = '';
    public string $fee_packing_1000 = '';
    public string $fee_packing_2000 = '';
    public string $fee_packing_extra_per_kg = '';

    // ─── UI State ───────────────────────────────────────────────
    public bool $showConfirmModal = false;
    public string $confirmSection = '';
    public ?string $lastUpdatedAt = null;

    // ─── Dirty tracking ─────────────────────────────────────────
    public array $originalValues = [];

    public function mount(): void
    {
        $this->loadSettings();
    }

    public function loadSettings(): void
    {
        $keys = [
            'rate_sharing_air_berat', 'rate_sharing_air_volume',
            'rate_sharing_sea_berat', 'rate_sharing_sea_volume',
            'rate_sharing_sensitive_air_berat', 'rate_sharing_sensitive_air_volume',
            'rate_sharing_sensitive_sea_berat', 'rate_sharing_sensitive_sea_volume',
            'rate_direct_air_berat', 'rate_direct_air_volume',
            'rate_direct_sea_berat', 'rate_direct_sea_volume',
            'fee_packing_150', 'fee_packing_1000', 'fee_packing_2000', 'fee_packing_extra_per_kg',
        ];

        foreach ($keys as $key) {
            $val = Setting::getValue($key, '');
            $this->$key = $val;
            $this->originalValues[$key] = $val;
        }

        $lastUpdate = Setting::whereIn('key', $keys)->latest('updated_at')->first();
        $this->lastUpdatedAt = $lastUpdate ? $lastUpdate->updated_at->format('d M Y H:i') : null;
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function confirmSave(string $section): void
    {
        $this->confirmSection = $section;
        $this->showConfirmModal = true;
    }

    public function saveSharingRates(AuditLogService $auditService): void
    {
        $this->validate([
            'rate_sharing_air_berat' => 'required|numeric|min:1|max:99999',
            'rate_sharing_air_volume' => 'required|numeric|min:1|max:99999',
            'rate_sharing_sea_berat' => 'required|numeric|min:1|max:99999',
            'rate_sharing_sea_volume' => 'required|numeric|min:1|max:99999',
            'rate_sharing_sensitive_air_berat' => 'required|numeric|min:1|max:99999',
            'rate_sharing_sensitive_air_volume' => 'required|numeric|min:1|max:99999',
            'rate_sharing_sensitive_sea_berat' => 'required|numeric|min:1|max:99999',
            'rate_sharing_sensitive_sea_volume' => 'required|numeric|min:1|max:99999',
        ]);

        $sharingKeys = [
            'rate_sharing_air_berat', 'rate_sharing_air_volume',
            'rate_sharing_sea_berat', 'rate_sharing_sea_volume',
            'rate_sharing_sensitive_air_berat', 'rate_sharing_sensitive_air_volume',
            'rate_sharing_sensitive_sea_berat', 'rate_sharing_sensitive_sea_volume',
        ];

        $oldValues = [];
        $newValues = [];
        foreach ($sharingKeys as $key) {
            $oldValues[$key] = $this->originalValues[$key];
            $newValues[$key] = $this->$key;
            Setting::setValue($key, $this->$key, 'rate_sharing');
            $this->originalValues[$key] = $this->$key;
        }

        $auditService->logCustom(
            Setting::where('key', 'rate_sharing_air_berat')->first(),
            'rate_updated',
            'Rate sharing diupdate',
            $oldValues,
            $newValues,
        );

        $this->lastUpdatedAt = now()->format('d M Y H:i');
        $this->showConfirmModal = false;
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Rate sharing berhasil diupdate.');
    }

    public function saveDirectRates(AuditLogService $auditService): void
    {
        $this->validate([
            'rate_direct_air_berat' => 'required|numeric|min:1|max:99999',
            'rate_direct_air_volume' => 'required|numeric|min:1|max:99999',
            'rate_direct_sea_berat' => 'required|numeric|min:1|max:99999',
            'rate_direct_sea_volume' => 'required|numeric|min:1|max:99999',
        ]);

        $directKeys = ['rate_direct_air_berat', 'rate_direct_air_volume', 'rate_direct_sea_berat', 'rate_direct_sea_volume'];

        $oldValues = [];
        $newValues = [];
        foreach ($directKeys as $key) {
            $oldValues[$key] = $this->originalValues[$key];
            $newValues[$key] = $this->$key;
            Setting::setValue($key, $this->$key, 'rate_direct');
            $this->originalValues[$key] = $this->$key;
        }

        $auditService->logCustom(
            Setting::where('key', 'rate_direct_air_berat')->first(),
            'rate_updated',
            'Rate direct diupdate',
            $oldValues,
            $newValues,
        );

        $this->lastUpdatedAt = now()->format('d M Y H:i');
        $this->showConfirmModal = false;
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Rate direct berhasil diupdate.');
    }

    public function saveFeePacking(AuditLogService $auditService): void
    {
        $this->validate([
            'fee_packing_150' => 'required|numeric|min:0|max:999999',
            'fee_packing_1000' => 'required|numeric|min:0|max:999999',
            'fee_packing_2000' => 'required|numeric|min:0|max:999999',
            'fee_packing_extra_per_kg' => 'required|numeric|min:0|max:999999',
        ]);

        $packingKeys = ['fee_packing_150', 'fee_packing_1000', 'fee_packing_2000', 'fee_packing_extra_per_kg'];

        $oldValues = [];
        $newValues = [];
        foreach ($packingKeys as $key) {
            $oldValues[$key] = $this->originalValues[$key];
            $newValues[$key] = $this->$key;
            Setting::setValue($key, $this->$key, 'fee_packing');
            $this->originalValues[$key] = $this->$key;
        }

        $auditService->logCustom(
            Setting::where('key', 'fee_packing_150')->first(),
            'rate_updated',
            'Fee packing diupdate',
            $oldValues,
            $newValues,
        );

        $this->lastUpdatedAt = now()->format('d M Y H:i');
        $this->showConfirmModal = false;
        $this->dispatch('toast', type: 'success', title: 'Berhasil', message: 'Fee packing berhasil diupdate.');
    }

    public function render()
    {
        return view('livewire.admin.settings.index');
    }
}
