<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\License;
use App\Models\LicenseActivation;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_dashboard_requires_auth(): void
    {
        $this->get(route('admin.dashboard'))
             ->assertRedirect(route('admin.login'));
    }

    public function test_dashboard_page_loads_for_authenticated_user(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.dashboard'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.dashboard');
    }

    public function test_dashboard_shows_stats(): void
    {
        Product::factory()->count(2)->create();
        Customer::factory()->count(3)->create();

        Livewire::actingAs($this->admin)
                ->test('pages::admin.dashboard')
                ->assertSee('Total Licenses')
                ->assertSee('Customers')
                ->assertSee('Active Devices')
                ->assertSee('Expiring');
    }

    public function test_dashboard_shows_recent_licenses(): void
    {
        $product  = Product::factory()->create();
        $customer = Customer::factory()->create(['name' => 'Jane Doe']);
        License::factory()->create(['product_id' => $product->id, 'customer_id' => $customer->id]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.dashboard')
                ->assertSee('Recent Licenses');
    }

    public function test_dashboard_shows_empty_state_when_no_licenses(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.dashboard')
                ->assertSee('No licenses yet.');
    }
}
