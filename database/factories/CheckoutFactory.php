<?php

namespace Database\Factories;

use App\Models\Checkout;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Checkout>
 */
class CheckoutFactory extends Factory
{
    protected $model = Checkout::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'invoice_id' => Invoice::factory(),
            'customer_id' => User::factory(),
            'address_type' => fake()->randomElement(['personal', 'dropship']),
            'recipient_name' => fake()->name(),
            'recipient_phone' => fake()->numerify('08##########'),
            'address' => fake()->address(),
            'packing_photo' => null,
            'tracking_number' => null,
            'status' => Checkout::STATUS_REQUEST,
        ];
    }
}
