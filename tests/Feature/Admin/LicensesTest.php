<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\License;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LicensesTest extends TestCase
{
    use RefreshDatabase;

    private User    $admin;
    private Product  $product;
    private Customer $customer;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin    = User::factory()->create();
        $this->product  = Product::factory()->create();
        $this->customer = Customer::factory()->create();
    }

    public function test_licenses_page_requires_auth(): void
    {
        $this->get(route('admin.licenses'))
             ->assertRedirect(route('admin.login'));
    }

    public function test_licenses_page_renders(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.licenses'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.licenses');
    }

    public function test_licenses_table_lists_licenses(): void
    {
        $license = License::factory()->create([
            'product_id'  => $this->product->id,
            'customer_id' => $this->customer->id,
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.licenses')
                ->assertSee($license->license_key);
    }

    public function test_search_filters_by_license_key(): void
    {
        $l1 = License::factory()->create([
            'product_id'  => $this->product->id,
            'customer_id' => $this->customer->id,
            'license_key' => 'EXCL-AAAA-BBBB-CCCC',
        ]);
        $l2 = License::factory()->create([
            'product_id'  => $this->product->id,
            'customer_id' => $this->customer->id,
            'license_key' => 'EXCL-XXXX-YYYY-ZZZZ',
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.licenses')
                ->set('search', 'AAAA')
                ->assertSee('EXCL-AAAA-BBBB-CCCC')
                ->assertDontSee('EXCL-XXXX-YYYY-ZZZZ');
    }

    public function test_status_filter_works(): void
    {
        License::factory()->create([
            'product_id'  => $this->product->id,
            'customer_id' => $this->customer->id,
            'license_key' => 'EXCL-ACTV-0001-0001',
            'status'      => 'active',
        ]);
        License::factory()->create([
            'product_id'  => $this->product->id,
            'customer_id' => $this->customer->id,
            'license_key' => 'EXCL-EXPR-0002-0002',
            'status'      => 'expired',
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.licenses')
                ->set('filterStatus', 'active')
                ->assertSee('EXCL-ACTV-0001-0001')
                ->assertDontSee('EXCL-EXPR-0002-0002');
    }

    public function test_can_create_license(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.licenses')
                ->call('openCreate')
                ->assertSet('showModal', true)
                ->set('product_id', $this->product->id)
                ->set('customer_id', $this->customer->id)
                ->set('license_key', 'EXCL-TEST-1234-ABCD')
                ->set('edition', 'standard')
                ->set('type', 'lifetime')
                ->set('max_activations', 1)
                ->set('status', 'active')
                ->call('save')
                ->assertSet('showModal', false)
                ->assertHasNoErrors();

        $this->assertDatabaseHas('licenses', ['license_key' => 'EXCL-TEST-1234-ABCD']);
    }

    public function test_create_license_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.licenses')
                ->call('openCreate')
                ->set('product_id', 0)
                ->set('customer_id', 0)
                ->set('license_key', '')
                ->call('save')
                ->assertHasErrors(['product_id', 'customer_id', 'license_key']);
    }

    public function test_can_revoke_license(): void
    {
        $license = License::factory()->create([
            'product_id'  => $this->product->id,
            'customer_id' => $this->customer->id,
            'status'      => 'active',
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.licenses')
                ->call('revoke', $license->id);

        $this->assertDatabaseHas('licenses', ['id' => $license->id, 'status' => 'revoked']);
    }

    public function test_can_delete_license(): void
    {
        $license = License::factory()->create([
            'product_id'  => $this->product->id,
            'customer_id' => $this->customer->id,
        ]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.licenses')
                ->call('deleteLicense', $license->id);

        $this->assertDatabaseMissing('licenses', ['id' => $license->id]);
    }

    public function test_generate_key_creates_prefixed_key(): void
    {
        $component = Livewire::actingAs($this->admin)
                             ->test('pages::admin.licenses')
                             ->call('openCreate')
                             ->set('product_id', $this->product->id)
                             ->call('generateKey');

        $key = $component->get('license_key');
        $this->assertNotEmpty($key);
        $this->assertStringContainsString('-', $key);
    }
}
