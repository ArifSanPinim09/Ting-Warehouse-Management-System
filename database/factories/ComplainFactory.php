<?php

namespace Database\Factories;

use App\Models\Complain;
use App\Models\Box;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Complain>
 */
class ComplainFactory extends Factory
{
    protected $model = Complain::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'box_id' => Box::factory(),
            'type' => fake()->randomElement(['Barang Rusak', 'Barang Hilang', 'Barang Salah']),
            'resolution' => fake()->randomElement(['refund', 'replacement']),
            'invoice_number' => 'INV-' . fake()->numerify('####'),
            'resi_number' => 'RESI-' . fake()->numerify('####'),
            'description' => fake()->sentence(),
            'video_url' => null,
            'photo_url' => null,
            'status' => Complain::STATUS_OPEN,
        ];
    }
}
