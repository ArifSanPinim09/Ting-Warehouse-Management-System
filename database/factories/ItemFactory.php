<?php

namespace Database\Factories;

use App\Models\Item;
use App\Models\Box;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Item>
 */
class ItemFactory extends Factory
{
    protected $model = Item::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'box_id' => Box::factory(),
            'customer_id' => User::factory(),
            'name' => fake()->words(3, true),
            'quantity' => fake()->numberBetween(1, 50),
            'price_yuan' => fake()->randomFloat(2, 10, 5000),
            'resi_number' => 'RESI-' . fake()->unique()->numerify('######'),
            'proof_co' => 'proof-co/' . fake()->uuid() . '.jpg',
            'is_sensitive' => false,
            'sensitive_type' => null,
            'arrived_china' => false,
            'arrived_china_photo' => null,
            'arrived_indonesia' => false,
            'arrived_indonesia_photo' => null,
        ];
    }
}
