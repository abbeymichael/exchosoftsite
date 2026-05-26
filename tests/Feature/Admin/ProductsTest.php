<?php

namespace Tests\Feature\Admin;

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class ProductsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_products_page_requires_auth(): void
    {
        $this->get(route('admin.products'))
             ->assertRedirect(route('admin.login'));
    }

    public function test_products_page_renders(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.products'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.products');
    }

    public function test_products_table_lists_products(): void
    {
        Product::factory()->create(['name' => 'My Test App', 'product_code' => 'MYTST']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->assertSee('My Test App')
                ->assertSee('MYTST');
    }

    public function test_search_filters_products(): void
    {
        Product::factory()->create(['name' => 'Alpha App',  'product_code' => 'ALPHA']);
        Product::factory()->create(['name' => 'Beta Suite', 'product_code' => 'BETA0']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->set('search', 'Alpha')
                ->assertSee('Alpha App')
                ->assertDontSee('Beta Suite');
    }

    public function test_platform_filter_works(): void
    {
        Product::factory()->create(['name' => 'Desktop App', 'platform' => 'desktop', 'product_code' => 'DSKTP']);
        Product::factory()->create(['name' => 'SaaS App',    'platform' => 'saas',    'product_code' => 'SAAS0']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->set('platform', 'desktop')
                ->assertSee('Desktop App')
                ->assertDontSee('SaaS App');
    }

    public function test_can_create_product(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->call('openCreate')
                ->assertSet('showModal', true)
                ->set('name', 'New Product')
                ->set('slug', 'new-product')
                ->set('product_code', 'NEWPR')
                ->set('selectedPlatform', 'desktop')
                ->set('current_version', '1.0.0')
                ->set('pricing_type', 'lifetime')
                ->call('save')
                ->assertSet('showModal', false)
                ->assertHasNoErrors();

        $this->assertDatabaseHas('products', ['name' => 'New Product', 'product_code' => 'NEWPR']);
    }

    public function test_create_product_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->call('openCreate')
                ->set('name', '')
                ->call('save')
                ->assertHasErrors(['name' => 'required']);
    }

    public function test_can_edit_product(): void
    {
        $product = Product::factory()->create(['name' => 'Original Name']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->call('openEdit', $product->id)
                ->assertSet('editing', true)
                ->assertSet('name', 'Original Name')
                ->set('name', 'Updated Name')
                ->call('save')
                ->assertHasNoErrors();

        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'Updated Name']);
    }

    public function test_can_toggle_product_active_status(): void
    {
        $product = Product::factory()->create(['is_active' => true]);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->call('toggleActive', $product->id);

        $this->assertDatabaseHas('products', ['id' => $product->id, 'is_active' => false]);
    }

    public function test_can_delete_product(): void
    {
        $product = Product::factory()->create();

        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->call('deleteProduct', $product->id);

        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    public function test_slug_auto_generated_from_name(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.products')
                ->call('openCreate')
                ->set('name', 'My Awesome App')
                ->assertSet('slug', 'my-awesome-app');
    }
}
