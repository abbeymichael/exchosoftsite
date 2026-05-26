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

class ActivationsTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private License $license;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin   = User::factory()->create();
        $product       = Product::factory()->create();
        $customer      = Customer::factory()->create();
        $this->license = License::factory()->create([
            'product_id'  => $product->id,
            'customer_id' => $customer->id,
        ]);
    }

    public function test_activations_page_requires_auth(): void
    {
        $this->get(route('admin.activations'))
             ->assertRedirect(route('admin.login'));
    }

    public function test_activations_page_renders(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.activations'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.activations');
    }

    public function test_activations_table_lists_activations(): void
    {
        LicenseActivation::factory()->create([
            'license_id'  => $this->license->id,
            'device_name' => 'My Test Machine',
            'status'      => 'active',
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.activations')
                ->assertSee('My Test Machine');
    }

    public function test_stats_are_computed_correctly(): void
    {
        LicenseActivation::factory()->create(['license_id' => $this->license->id, 'status' => 'active']);
        LicenseActivation::factory()->create(['license_id' => $this->license->id, 'status' => 'deactivated']);
        LicenseActivation::factory()->create(['license_id' => $this->license->id, 'status' => 'revoked']);

        $component = Livewire::actingAs($this->admin)
                             ->test('pages::admin.activations');

        $stats = $component->get('stats');

        $this->assertEquals(3, $stats['total']);
        $this->assertEquals(1, $stats['active']);
        $this->assertEquals(1, $stats['deactivated']);
        $this->assertEquals(1, $stats['revoked']);
    }

    public function test_can_deactivate_active_activation(): void
    {
        $activation = LicenseActivation::factory()->create([
            'license_id' => $this->license->id,
            'status'     => 'active',
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.activations')
                ->call('deactivate', $activation->id);

        $this->assertDatabaseHas('license_activations', [
            'id'     => $activation->id,
            'status' => 'deactivated',
        ]);
    }

    public function test_can_revoke_activation(): void
    {
        $activation = LicenseActivation::factory()->create([
            'license_id' => $this->license->id,
            'status'     => 'active',
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.activations')
                ->call('revoke', $activation->id);

        $this->assertDatabaseHas('license_activations', [
            'id'     => $activation->id,
            'status' => 'revoked',
        ]);
    }

    public function test_status_filter_works(): void
    {
        LicenseActivation::factory()->create(['license_id' => $this->license->id, 'device_name' => 'ActiveBox',  'status' => 'active']);
        LicenseActivation::factory()->create(['license_id' => $this->license->id, 'device_name' => 'InactiveBox','status' => 'deactivated']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.activations')
                ->set('filterStatus', 'active')
                ->assertSee('ActiveBox')
                ->assertDontSee('InactiveBox');
    }
}
