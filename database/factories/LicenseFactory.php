<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\License;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<License>
 */
class LicenseFactory extends Factory
{
    protected $model = License::class;

    public function definition(): array
    {
        return [
            'product_id'          => Product::factory(),
            'customer_id'         => Customer::factory(),
            'license_key'         => License::generateKey(),
            'edition'             => $this->faker->randomElement(['standard', 'professional', 'enterprise', 'trial']),
            'type'                => $this->faker->randomElement(['lifetime', 'monthly', 'annual', 'trial']),
            'max_activations'     => $this->faker->randomElement([1, 2, 3, 5]),
            'current_activations' => 0,
            'status'              => 'active',
            'expires_at'          => null,
            'notes'               => null,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active']);
    }

    public function expired(): static
    {
        return $this->state([
            'status'     => 'expired',
            'expires_at' => now()->subMonth(),
        ]);
    }

    public function trial(): static
    {
        return $this->state([
            'status'     => 'trial',
            'type'       => 'trial',
            'edition'    => 'trial',
            'expires_at' => now()->addDays(14),
        ]);
    }

    public function revoked(): static
    {
        return $this->state(['status' => 'revoked']);
    }

    public function expiringSoon(): static
    {
        return $this->state([
            'status'     => 'active',
            'expires_at' => now()->addDays(rand(1, 29)),
        ]);
    }
}
