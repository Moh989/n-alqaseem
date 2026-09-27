<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_admin_route_requires_authentication(): void
    {
        $this->seedSite();

        $routes = collect(Route::getRoutes()->getRoutes())
            ->filter(fn ($route): bool => str_starts_with((string) $route->getName(), 'admin.') && ! in_array($route->getName(), ['admin.login', 'admin.login.store'], true));

        $this->assertGreaterThan(30, $routes->count());

        foreach ($routes as $route) {
            $uri = preg_replace('/\{[^}]+\}/', '1', $route->uri());
            $uri = str_replace('/1/move/1', '/1/move/up', $uri);
            $method = collect($route->methods())->reject(fn (string $m): bool => $m === 'HEAD')->first();

            $response = $this->call($method, '/'.$uri);

            $this->assertTrue(
                $response->isRedirect(route('admin.login')) || $response->getStatusCode() === 404 || $response->getStatusCode() === 419,
                "{$method} /{$uri} is reachable without logging in (status {$response->getStatusCode()})."
            );
        }
    }

    public function test_admin_pages_are_not_cached_or_indexed(): void
    {
        $this->get('/admin/login')
            ->assertOk()
            ->assertHeader('X-Robots-Tag', 'noindex, nofollow');

        $this->assertStringContainsString('no-store', (string) $this->get('/admin/login')->headers->get('Cache-Control'));
    }

    public function test_administrator_can_log_in_and_the_session_is_regenerated(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.com', 'password' => 'a-strong-password-123']);

        $this->get('/admin/login');
        $oldSession = session()->getId();

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'a-strong-password-123'])
            ->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($user);
        $this->assertNotSame($oldSession, session()->getId());
        $this->assertNotNull($user->fresh()->last_login_at);

        $this->get('/admin')->assertOk()->assertSee('لوحة المتابعة');
    }

    public function test_wrong_password_and_inactive_accounts_are_rejected(): void
    {
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'a-strong-password-123']);
        User::factory()->inactive()->create(['email' => 'old@example.com', 'password' => 'a-strong-password-123']);

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong'])->assertSessionHasErrors('email');
        $this->post('/admin/login', ['email' => 'old@example.com', 'password' => 'a-strong-password-123'])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_deactivated_users_are_logged_out(): void
    {
        $user = User::factory()->inactive()->create();

        $this->actingAs($user)->get('/admin')->assertRedirect(route('admin.login'));
        $this->assertGuest();
    }

    public function test_login_is_throttled_after_five_failures(): void
    {
        RateLimiter::clear('admin-login:admin@example.com|127.0.0.1');
        User::factory()->create(['email' => 'admin@example.com', 'password' => 'a-strong-password-123']);

        for ($i = 0; $i < 5; $i++) {
            $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'wrong']);
        }

        $this->post('/admin/login', ['email' => 'admin@example.com', 'password' => 'a-strong-password-123'])
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_logout_invalidates_the_session(): void
    {
        $this->actingAs(User::factory()->create())
            ->post('/admin/logout')
            ->assertRedirect(route('admin.login'));

        $this->assertGuest();
    }

    public function test_admin_create_command_creates_an_account_with_a_hidden_password(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator email', 'Owner@Example.com')
            ->expectsQuestion('Display name', 'المدير')
            ->expectsQuestion('Password (min. 12 characters, letters and numbers)', 'correct-horse-42')
            ->expectsQuestion('Confirm password', 'correct-horse-42')
            ->assertSuccessful();

        $user = User::where('email', 'owner@example.com')->sole();
        $this->assertTrue(Hash::check('correct-horse-42', $user->password));
        $this->assertTrue($user->is_active);
    }

    public function test_admin_create_rejects_weak_passwords_and_duplicates(): void
    {
        $this->artisan('admin:create')
            ->expectsQuestion('Administrator email', 'owner@example.com')
            ->expectsQuestion('Display name', 'Owner')
            ->expectsQuestion('Password (min. 12 characters, letters and numbers)', 'short1')
            ->expectsQuestion('Confirm password', 'short1')
            ->assertFailed();

        $this->assertDatabaseCount('users', 0);

        User::factory()->create(['email' => 'owner@example.com']);

        $this->artisan('admin:create')
            ->expectsQuestion('Administrator email', 'owner@example.com')
            ->assertFailed();
    }

    public function test_admin_create_update_resets_the_password(): void
    {
        $user = User::factory()->create(['email' => 'owner@example.com']);

        $this->artisan('admin:create --update')
            ->expectsQuestion('Administrator email', 'owner@example.com')
            ->expectsQuestion('Password (min. 12 characters, letters and numbers)', 'new-password-2026')
            ->expectsQuestion('Confirm password', 'new-password-2026')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('new-password-2026', $user->fresh()->password));
    }

    public function test_seeding_creates_no_user_accounts(): void
    {
        $this->seedSite();

        $this->assertDatabaseCount('users', 0);
    }
}
