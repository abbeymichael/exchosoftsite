<?php

namespace Database\Factories;

use App\Models\Customer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Customer>
 */
class CustomerFactory extends Factory
{
    protected $model = Customer::class;

    public function definition(): array
    {
        $type = $this->faker->randomElement(['individual', 'company']);

        return [
            'name'      => $this->faker->name(),
            'email'     => $this->faker->unique()->safeEmail(),
            'company'   => $type === 'company' ? $this->faker->company() : null,
            'phone'     => $this->faker->optional()->phoneNumber(),
            'type'      => $type,
            'notes'     => $this->faker->optional()->sentence(),
            'is_active' => true,
        ];
    }

    public function individual(): static
    {
        return $this->state(['type' => 'individual', 'company' => null]);
    }

    public function company(): static
    {
        return $this->state(fn () => [
            'type'    => 'company',
            'company' => $this->faker->company(),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }
}
