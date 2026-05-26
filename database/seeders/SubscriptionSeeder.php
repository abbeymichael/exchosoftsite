<?php

namespace Database\Seeders;

use App\Models\License;
use App\Models\Subscription;
use Illuminate\Database\Seeder;

class SubscriptionSeeder extends Seeder
{
    public function run(): void
    {
        // Only create subscriptions for monthly / annual type licenses without one already
        $licenses = License::whereIn('type', ['monthly', 'annual'])
            ->whereDoesntHave('subscription')
            ->get();

        if ($licenses->isEmpty()) {
            $this->command->info('  No subscription-type licenses without subscriptions found.');
            return;
        }

        $plans = [
            ['billing_cycle' => 'monthly', 'amount' => 9.99,  'status' => 'active'],
            ['billing_cycle' => 'monthly', 'amount' => 29.99, 'status' => 'active'],
            ['billing_cycle' => 'monthly', 'amount' => 79.99, 'status' => 'active'],
            ['billing_cycle' => 'annual',  'amount' => 99.00, 'status' => 'active'],
            ['billing_cycle' => 'annual',  'amount' => 299.00,'status' => 'active'],
            ['billing_cycle' => 'monthly', 'amount' => 29.99, 'status' => 'cancelled'],
            ['billing_cycle' => 'annual',  'amount' => 799.00,'status' => 'past_due'],
        ];

        $created = 0;

        foreach ($licenses as $i => $license) {
            $plan = $plans[$i % count($plans)];

            Subscription::create([
                'license_id'        => $license->id,
                'billing_cycle'     => $license->type, // monthly or annual
                'amount'            => $plan['amount'],
                'currency'          => 'USD',
                'status'            => $license->status === 'expired' ? 'cancelled' : $plan['status'],
                'next_billing_date' => $license->type === 'monthly' ? now()->addMonth() : now()->addYear(),
                'provider'          => 'stripe',
                'provider_reference'=> 'sub_' . strtolower(substr(md5($license->id . 'sub'), 0, 12)),
                'cancelled_at'      => $plan['status'] === 'cancelled' ? now()->subDays(rand(1, 30)) : null,
            ]);

            $created++;
        }

        $this->command->info("  {$created} subscriptions seeded.");
    }
}
