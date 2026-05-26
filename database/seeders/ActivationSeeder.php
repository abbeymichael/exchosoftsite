<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\LicenseActivation;
use Illuminate\Database\Seeder;

class ActivationSeeder extends Seeder
{
    public function run(): void
    {
        $licenses = License::whereIn('status', ['active', 'revoked'])->get();

        if ($licenses->isEmpty()) {
            $this->command->warn('  Skipping activations — run LicenseSeeder first.');
            return;
        }

        $deviceNames = [
            'MacBook Pro 16"',
            'Windows PC (Dell XPS)',
            'Linux Workstation',
            'Surface Pro 9',
            'iMac 27"',
            'ThinkPad X1 Carbon',
            'MacBook Air M2',
            'HP ZBook Studio',
        ];

        $platforms = ['windows', 'macos', 'linux', 'windows', 'macos', 'linux', 'macos', 'windows'];
        $ips       = ['192.168.1.10', '10.0.0.55', '172.16.0.1', '203.0.113.5', '198.51.100.3', '10.10.1.20', '192.168.2.50', '10.20.0.3'];

        $created = 0;

        foreach ($licenses as $i => $license) {
            $activationCount = match ($license->status) {
                'active'  => min(2, $license->max_activations),
                'revoked' => 1,
                default   => 0,
            };

            for ($j = 0; $j < $activationCount; $j++) {
                $idx    = ($i + $j) % count($deviceNames);
                $status = match (true) {
                    $license->status === 'revoked' => 'revoked',
                    $j === 1                       => 'deactivated',
                    default                        => 'active',
                };

                $activation = LicenseActivation::create([
                    'license_id'     => $license->id,
                    'device_name'    => $deviceNames[$idx],
                    'device_id'      => 'DEV-' . strtoupper(substr(md5($license->id . $j), 0, 8)),
                    'platform'       => $platforms[$idx],
                    'ip_address'     => $ips[$idx],
                    'status'         => $status,
                    'activated_at'   => now()->subDays(rand(1, 90)),
                    'last_seen_at'   => $status === 'active' ? now()->subHours(rand(1, 72)) : null,
                    'deactivated_at' => $status === 'deactivated' ? now()->subDays(rand(1, 30)) : null,
                ]);

                $created++;
            }

            // Sync current_activations count
            $license->update([
                'current_activations' => LicenseActivation::where('license_id', $license->id)
                    ->where('status', 'active')
                    ->count(),
            ]);
        }

        $this->command->info("  {$created} activations seeded.");
    }
}
