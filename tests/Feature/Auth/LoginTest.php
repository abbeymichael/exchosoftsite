<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    // ── Unauthenticated redirect ──────────────────────────────────────────────

    public function test_admin_routes_redirect_guests_to_login(): void
    {
        $response = $this->get(route('admin.dashboard'));

        $response->assertRedirect(route('admin.login'));
    }

    public function test_login_page_renders_for_guests(): void
    {
        $this->get(route('admin.login'))
             ->assertOk()
             ->assertSeeLivewire('pages::admin.auth.login');
    }

    public function test_authenticated_users_are_redirected_away_from_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->get(route('admin.login'))
             ->assertRedirect(route('admin.dashboard'));
    }

    // ── Livewire login component ──────────────────────────────────────────────

    public function test_login_component_renders(): void
    {
        Livewire::test('pages::admin.auth.login')
                ->assertSee('Sign in');
    }

    public function test_valid_credentials_log_in_and_redirect(): void
    {
        $user = User::factory()->create([
            'email'    => 'admin@exchosoft.com',
            'password' => bcrypt('secret1234'),
        ]);

        Livewire::test('pages::admin.auth.login')
                ->set('email', 'admin@exchosoft.com')
                ->set('password', 'secret1234')
                ->call('admin.login')
                ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_invalid_credentials_return_error(): void
    {
        User::factory()->create([
            'email'    => 'admin@exchosoft.com',
            'password' => bcrypt('correct-password'),
        ]);

        Livewire::test('pages::admin.auth.login')
                ->set('email', 'admin@exchosoft.com')
                ->set('password', 'wrong-password')
                ->call('admin.login')
                ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    public function test_email_is_required(): void
    {
        Livewire::test('pages::admin.auth.login')
                ->set('email', '')
                ->set('password', 'password')
                ->call('admin.login')
                ->assertHasErrors(['email' => 'required']);
    }

    public function test_password_is_required(): void
    {
        Livewire::test('pages::admin.auth.login')
                ->set('email', 'admin@exchosoft.com')
                ->set('password', '')
                ->call('admin.login')
                ->assertHasErrors(['password' => 'required']);
    }

    public function test_email_must_be_valid(): void
    {
        Livewire::test('pages::admin.auth.login')
                ->set('email', 'not-an-email')
                ->set('password', 'password')
                ->call('admin.login')
                ->assertHasErrors(['email' => 'email']);
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    public function test_logout_signs_out_and_redirects_to_login(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
             ->post(route('admin.logout'))
             ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_logout_requires_authentication(): void
    {
        $this->post(route('admin.logout'))
             ->assertRedirect(route('admin.login'));
    }
}
