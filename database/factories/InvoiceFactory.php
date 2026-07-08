<?php

namespace Database\Factories;

use App\Models\Box;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Invoice>
 */
class InvoiceFactory extends Factory
{
    protected $model = Invoice::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_number' => 'INV-' . fake()->unique()->numerify('Y-m-d-####'),
            'box_id' => Box::factory(),
            'customer_id' => User::factory(),
            'weight' => fake()->randomFloat(2, 10, 500),
            'volume' => fake()->randomFloat(2, 1, 100),
            'fee_tax' => fake()->randomFloat(2, 100000, 5000000),
            'fee_wh' => fake()->randomElement([5000, 6500, 8000]),
            'fee_packing' => fake()->randomElement([5000, 6500, 8000]),
            'add_on' => 0,
            'grand_total' => fake()->randomFloat(2, 200000, 10000000),
            'status' => Invoice::STATUS_WAITING_PAYMENT,
        ];
    }
}
