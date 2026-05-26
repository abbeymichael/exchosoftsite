<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\License;
use App\Models\Product;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class SubscriptionsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    private function makeLicense(array $attrs = []): License
    {
        $product  = Product::factory()->create();
        $customer = Customer::factory()->create();
        return License::factory()->create(array_merge([
            'product_id'  => $product->id,
            'customer_id' => $customer->id,
        ], $attrs));
    }

    public function test_subscriptions_page_requires_auth(): void
    {
        $this->get(route('admin.subscriptions'))
             ->assertRedirect(route('admin.login'));
    }

    public function test_subscriptions_page_renders(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.subscriptions'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.subscriptions');
    }

    public function test_subscriptions_table_lists_subscriptions(): void
    {
        $license = $this->makeLicense();
        Subscription::factory()->create(['license_id' => $license->id, 'status' => 'active', 'amount' => 29.99]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.subscriptions')
                ->assertSee('29.99');
    }

    public function test_can_cancel_active_subscription(): void
    {
        $license      = $this->makeLicense();
        $subscription = Subscription::factory()->create([
            'license_id' => $license->id,
            'status'     => 'active',
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.subscriptions')
                ->call('cancel', $subscription->id);

        $this->assertDatabaseHas('subscriptions', [
            'id'     => $subscription->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_stats_show_correct_counts(): void
    {
        $l1 = $this->makeLicense();
        $l2 = $this->makeLicense();
        $l3 = $this->makeLicense();

        Subscription::factory()->create(['license_id' => $l1->id, 'status' => 'active',    'billing_cycle' => 'monthly', 'amount' => 29.99]);
        Subscription::factory()->create(['license_id' => $l2->id, 'status' => 'cancelled', 'billing_cycle' => 'monthly', 'amount' => 9.99]);
        Subscription::factory()->create(['license_id' => $l3->id, 'status' => 'past_due',  'billing_cycle' => 'annual',  'amount' => 299.00]);

        $component = Livewire::actingAs($this->admin)
                             ->test('pages::admin.subscriptions');

        $stats = $component->get('stats');

        $this->assertEquals(1, $stats['active']);
        $this->assertEquals(1, $stats['cancelled']);
        $this->assertEquals(1, $stats['past_due']);
        $this->assertEquals(29.99, $stats['total_mrr']);
    }

    public function test_billing_cycle_filter_works(): void
    {
        $l1 = $this->makeLicense();
        $l2 = $this->makeLicense();

        Subscription::factory()->create(['license_id' => $l1->id, 'billing_cycle' => 'monthly', 'amount' => 9.99,   'status' => 'active']);
        Subscription::factory()->create(['license_id' => $l2->id, 'billing_cycle' => 'annual',  'amount' => 99.00,  'status' => 'active']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.subscriptions')
                ->set('filterCycle', 'monthly')
                ->assertSee('9.99')
                ->assertDontSee('99.00');
    }
}
