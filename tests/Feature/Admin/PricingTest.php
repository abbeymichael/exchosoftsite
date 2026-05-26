<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PricingTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_pricing_page_requires_auth(): void
    {
        $this->get(route('admin.pricing'))
             ->assertRedirect(route('admin.login'));
    }

    public function test_pricing_page_renders(): void
    {
        $this->actingAs($this->admin)
             ->get(route('admin.pricing'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.pricing');
    }

    public function test_pricing_page_shows_all_plans(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.pricing')
                ->assertSee('Starter')
                ->assertSee('Professional')
                ->assertSee('Enterprise');
    }

    public function test_pricing_page_shows_stats(): void
    {
        Livewire::actingAs($this->admin)
                ->test('pages::admin.pricing')
                ->assertSee('Active Subscriptions')
                ->assertSee('Monthly Revenue')
                ->assertSee('Annual Revenue')
                ->assertSee('Active Products');
    }

    public function test_plans_property_has_three_tiers(): void
    {
        $component = Livewire::actingAs($this->admin)
                             ->test('pages::admin.pricing');

        $plans = $component->get('plans');
        $this->assertCount(3, $plans);
    }

    public function test_professional_plan_is_marked_popular(): void
    {
        $component = Livewire::actingAs($this->admin)
                             ->test('pages::admin.pricing');

        $plans = $component->get('plans');

        $professional = collect($plans)->firstWhere('name', 'Professional');
        $this->assertTrue($professional['popular']);
    }
}
