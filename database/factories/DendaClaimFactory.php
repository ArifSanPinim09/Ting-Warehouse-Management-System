<?php

namespace Database\Factories;

use App\Models\DendaClaim;
use App\Models\Invoice;
use App\Models\Item;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DendaClaim>
 */
class DendaClaimFactory extends Factory
{
    protected $model = DendaClaim::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => User::factory(),
            'item_id' => Item::factory(),
            'jumlah_denda' => 5000,
            'invoice_id' => null,
            'status' => DendaClaim::STATUS_PENDING,
        ];
    }

    /**
     * Indicate that the denda is tagged to an invoice.
     */
    public function tagged(?Invoice $invoice = null): static
    {
        return $this->state(fn (array $attributes) => [
            'invoice_id' => $invoice?->id ?? Invoice::factory(),
            'status' => DendaClaim::STATUS_TAGGED,
        ]);
    }

    /**
     * Indicate that the denda has been paid.
     */
    public function paid(?Invoice $invoice = null): static
    {
        return $this->state(fn (array $attributes) => [
            'invoice_id' => $invoice?->id ?? Invoice::factory(),
            'status' => DendaClaim::STATUS_PAID,
        ]);
    }
}
