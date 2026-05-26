<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    public function run(): void
    {
        $customers = [
            [
                'name'      => 'Alice Johnson',
                'email'     => 'alice@techcorp.io',
                'type'      => 'individual',
                'company'   => null,
                'phone'     => '+1 555 100 0001',
                'notes'     => 'Long-time power user, prefers lifetime licenses.',
                'is_active' => true,
            ],
            [
                'name'      => 'Bob Williams',
                'email'     => 'bob@startuplab.co',
                'type'      => 'individual',
                'company'   => null,
                'phone'     => '+1 555 100 0002',
                'notes'     => null,
                'is_active' => true,
            ],
            [
                'name'      => 'Carol Davis',
                'email'     => 'carol@devcorp.net',
                'type'      => 'company',
                'company'   => 'DevCorp Inc',
                'phone'     => '+1 555 100 0003',
                'notes'     => 'Team of 12 developers. Enterprise tier.',
                'is_active' => true,
            ],
            [
                'name'      => 'David Martinez',
                'email'     => 'david@bigfirm.com',
                'type'      => 'company',
                'company'   => 'Big Firm LLC',
                'phone'     => '+1 555 100 0004',
                'notes'     => 'Requires invoice-based billing.',
                'is_active' => true,
            ],
            [
                'name'      => 'Eve Thompson',
                'email'     => 'eve@solodev.me',
                'type'      => 'individual',
                'company'   => null,
                'phone'     => null,
                'notes'     => null,
                'is_active' => true,
            ],
            [
                'name'      => 'Frank Garcia',
                'email'     => 'frank@techventure.io',
                'type'      => 'company',
                'company'   => 'TechVenture',
                'phone'     => '+1 555 100 0006',
                'notes'     => 'Reseller partner.',
                'is_active' => true,
            ],
            [
                'name'      => 'Grace Lee',
                'email'     => 'grace@designstudio.co',
                'type'      => 'individual',
                'company'   => null,
                'phone'     => '+1 555 200 0007',
                'notes'     => null,
                'is_active' => true,
            ],
            [
                'name'      => 'Henry Chen',
                'email'     => 'henry@globalops.com',
                'type'      => 'company',
                'company'   => 'GlobalOps Ltd',
                'phone'     => '+1 555 200 0008',
                'notes'     => 'Multi-seat enterprise contract.',
                'is_active' => true,
            ],
            [
                'name'      => 'Irene Patel',
                'email'     => 'irene@cloudnine.io',
                'type'      => 'individual',
                'company'   => null,
                'phone'     => null,
                'notes'     => null,
                'is_active' => false,
            ],
            [
                'name'      => 'Jake Robinson',
                'email'     => 'jake@devtools.net',
                'type'      => 'company',
                'company'   => 'DevTools Co.',
                'phone'     => '+1 555 300 0010',
                'notes'     => 'Trial customer, evaluating enterprise plan.',
                'is_active' => true,
            ],
        ];

        foreach ($customers as $customer) {
            Customer::updateOrCreate(['email' => $customer['email']], $customer);
        }

        $this->command->info('  ' . count($customers) . ' customers seeded.');
    }
}
