<?php

namespace Database\Factories;

use App\Models\License;
use App\Models\Subscription;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Subscription>
 */
class SubscriptionFactory extends Factory
{
    protected $model = Subscription::class;

    public function definition(): array
    {
        $cycle = $this->faker->randomElement(['monthly', 'annual']);

        return [
            'license_id'         => License::factory(),
            'billing_cycle'      => $cycle,
            'amount'             => $this->faker->randomElement([9.99, 29.99, 79.99, 99.00, 299.00, 799.00]),
            'currency'           => 'USD',
            'status'             => 'active',
            'next_billing_date'  => $cycle === 'monthly' ? now()->addMonth() : now()->addYear(),
            'provider'           => 'stripe',
            'provider_reference' => 'sub_' . Str::lower(Str::random(12)),
            'cancelled_at'       => null,
        ];
    }

    public function active(): static
    {
        return $this->state(['status' => 'active', 'cancelled_at' => null]);
    }

    public function cancelled(): static
    {
        return $this->state([
            'status'       => 'cancelled',
            'cancelled_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function pastDue(): static
    {
        return $this->state(['status' => 'past_due']);
    }

    public function monthly(): static
    {
        return $this->state([
            'billing_cycle'     => 'monthly',
            'next_billing_date' => now()->addMonth(),
        ]);
    }

    public function annual(): static
    {
        return $this->state([
            'billing_cycle'     => 'annual',
            'next_billing_date' => now()->addYear(),
        ]);
    }
}
