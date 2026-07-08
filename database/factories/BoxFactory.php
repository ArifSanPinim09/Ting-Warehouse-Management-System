<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Box>
 */
class BoxFactory extends Factory
{
    protected $model = Box::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => fake()->randomElement(['sharing', 'direct']),
            'tracking_number' => 'TRK-' . fake()->unique()->numerify('######'),
            'batch_name' => 'Batch-' . fake()->numerify('###'),
            'status' => Box::STATUS_OPEN,
            'method' => fake()->randomElement(['air', 'sea']),
            'customer_id' => User::factory(),
            'notes' => null,
        ];
    }
}
