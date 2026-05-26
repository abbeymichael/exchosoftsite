<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    protected $model = Product::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->words(3, true);

        return [
            'name'            => ucwords($name),
            'slug'            => Str::slug($name),
            'product_code'    => strtoupper(Str::random(5)),
            'platform'        => $this->faker->randomElement(['desktop', 'saas', 'hybrid', 'offline-first']),
            'current_version' => $this->faker->semver(),
            'pricing_type'    => $this->faker->randomElement(['lifetime', 'subscription', 'trial', 'free']),
            'description'     => $this->faker->sentence(),
            'is_active'       => true,
        ];
    }

    public function inactive(): static
    {
        return $this->state(['is_active' => false]);
    }

    public function desktop(): static
    {
        return $this->state(['platform' => 'desktop']);
    }

    public function saas(): static
    {
        return $this->state(['platform' => 'saas']);
    }
}
