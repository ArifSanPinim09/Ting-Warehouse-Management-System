<?php

namespace Database\Factories;

use App\Models\Setting;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Setting>
 */
class SettingFactory extends Factory
{
    protected $model = Setting::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => 'rate_' . fake()->unique()->word(),
            'value' => (string) fake()->numberBetween(1, 10000),
            'group' => fake()->randomElement(['rate_sharing', 'rate_direct', 'fee_packing', 'currency', 'general']),
        ];
    }
}
