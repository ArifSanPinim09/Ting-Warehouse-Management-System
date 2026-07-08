<?php

namespace Tests\Unit\Models;

use App\Models\Setting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SettingModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_value_returns_setting(): void
    {
        Setting::create(['key' => 'kurs_yuan_idr', 'value' => '2460', 'group' => 'currency']);

        $this->assertEquals('2460', Setting::getValue('kurs_yuan_idr'));
    }

    public function test_get_value_returns_default_when_missing(): void
    {
        $this->assertNull(Setting::getValue('nonexistent'));
        $this->assertEquals('fallback', Setting::getValue('nonexistent', 'fallback'));
    }

    public function test_set_value_creates_new(): void
    {
        Setting::setValue('new_key', 'new_value', 'general');

        $this->assertDatabaseHas('settings', [
            'key' => 'new_key',
            'value' => 'new_value',
            'group' => 'general',
        ]);
    }

    public function test_set_value_updates_existing(): void
    {
        Setting::create(['key' => 'rate_sharing_air_berat', 'value' => '255', 'group' => 'rate_sharing']);

        Setting::setValue('rate_sharing_air_berat', '300', 'rate_sharing');

        $this->assertDatabaseHas('settings', [
            'key' => 'rate_sharing_air_berat',
            'value' => '300',
        ]);
        $this->assertEquals(1, Setting::where('key', 'rate_sharing_air_berat')->count());
    }

    public function test_set_value_with_group(): void
    {
        Setting::setValue('test_key', '100', 'fee_packing');

        $this->assertDatabaseHas('settings', [
            'key' => 'test_key',
            'value' => '100',
            'group' => 'fee_packing',
        ]);
    }
}
