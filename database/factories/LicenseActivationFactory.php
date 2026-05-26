<?php

namespace Database\Factories;

use App\Models\License;
use App\Models\LicenseActivation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LicenseActivation>
 */
class LicenseActivationFactory extends Factory
{
    protected $model = LicenseActivation::class;

    public function definition(): array
    {
        return [
            'license_id'     => License::factory(),
            'device_name'    => $this->faker->randomElement(['MacBook Pro', 'Windows PC', 'Linux Box', 'iMac', 'ThinkPad']),
            'device_id'      => 'DEV-' . strtoupper($this->faker->lexify('????????')),
            'platform'       => $this->faker->randomElement(['windows', 'macos', 'linux']),
            'ip_address'     => $this->faker->ipv4(),
            'status'         => 'active',
            'activated_at'   => now()->subDays(rand(1, 30)),
            'last_seen_at'   => now()->subHours(rand(1, 48)),
            'deactivated_at' => null,
            'metadata'       => null,
        ];
    }

    public function active(): static
    {
        return $this->state([
            'status'         => 'active',
            'deactivated_at' => null,
        ]);
    }

    public function deactivated(): static
    {
        return $this->state([
            'status'         => 'deactivated',
            'deactivated_at' => now()->subDays(rand(1, 30)),
        ]);
    }

    public function revoked(): static
    {
        return $this->state([
            'status'         => 'revoked',
            'deactivated_at' => null,
        ]);
    }
}
