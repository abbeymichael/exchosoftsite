<?php

namespace Tests\Feature\Admin;

use App\Models\Customer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class CustomersTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_customers_page_requires_auth(): void
    {
        $this->get(route('admin.customers'))
             ->assertRedirect(route('admin.login'));
    }

    public function test_customers_page_renders(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.customers'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.customers');
    }

    public function test_customers_table_lists_customers(): void
    {
        Customer::factory()->create(['name' => 'Jane Doe', 'email' => 'jane@example.com']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->assertSee('Jane Doe')
                ->assertSee('jane@example.com');
    }

    public function test_search_filters_by_name(): void
    {
        Customer::factory()->create(['name' => 'Alice Smith', 'email' => 'alice@test.com']);
        Customer::factory()->create(['name' => 'Bob Jones',   'email' => 'bob@test.com']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->set('search', 'Alice')
                ->assertSee('Alice Smith')
                ->assertDontSee('Bob Jones');
    }

    public function test_type_filter_works(): void
    {
        Customer::factory()->create(['name' => 'Solo Dev',   'email' => 'solo@example.com',    'type' => 'individual']);
        Customer::factory()->create(['name' => 'Corp Client', 'email' => 'corp@example.com',   'type' => 'company']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->set('filterType', 'individual')
                ->assertSee('Solo Dev')
                ->assertDontSee('Corp Client');
    }

    public function test_can_create_customer(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->call('openCreate')
                ->assertSet('showModal', true)
                ->set('name', 'New Customer')
                ->set('email', 'newcustomer@example.com')
                ->set('type', 'individual')
                ->call('save')
                ->assertSet('showModal', false)
                ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', ['email' => 'newcustomer@example.com']);
    }

    public function test_create_customer_validates_required_fields(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->call('openCreate')
                ->set('name', '')
                ->set('email', '')
                ->call('save')
                ->assertHasErrors(['name' => 'required', 'email' => 'required']);
    }

    public function test_create_customer_validates_unique_email(): void
    {
        Customer::factory()->create(['email' => 'existing@example.com']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->call('openCreate')
                ->set('name', 'Another User')
                ->set('email', 'existing@example.com')
                ->set('type', 'individual')
                ->call('save')
                ->assertHasErrors(['email' => 'unique']);
    }

    public function test_can_edit_customer(): void
    {
        $customer = Customer::factory()->create(['name' => 'Old Name', 'type' => 'individual']);

        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->call('openEdit', $customer->id)
                ->assertSet('name', 'Old Name')
                ->set('name', 'New Name')
                ->call('save')
                ->assertHasNoErrors();

        $this->assertDatabaseHas('customers', ['id' => $customer->id, 'name' => 'New Name']);
    }

    public function test_can_delete_customer(): void
    {
        $customer = Customer::factory()->create();

        Livewire::actingAs($this->admin)
                ->test('pages::admin.customers')
                ->call('deleteCustomer', $customer->id);

        $this->assertDatabaseMissing('customers', ['id' => $customer->id]);
    }
}
