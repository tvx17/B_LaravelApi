<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class ApiAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withHeader('Origin', 'http://localhost:9200');
    }

    public function test_csrf_initialization_sets_cookies_with_cors_credentials(): void
    {
        $this->get('/sanctum/csrf-cookie')->assertNoContent()
            ->assertCookie('XSRF-TOKEN')
            ->assertCookie(config('session.cookie'))
            ->assertHeader('Access-Control-Allow-Origin', 'http://localhost:9200')
            ->assertHeader('Access-Control-Allow-Credentials', 'true');
    }

    public function test_session_login_current_user_and_logout_flow(): void
    {
        $user = User::factory()->create();
        $this->withSession(['marker' => 'preserved']);
        $previousId = session()->getId();

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()->assertJsonPath('code', '05001')
            ->assertJsonPath('status', true)->assertJsonPath('responseStatus', 200)
            ->assertJsonPath('data.id', $user->id)
            ->assertJsonMissingPath('data.password')->assertJsonMissingPath('data.remember_token');

        $this->assertNotSame($previousId, session()->getId());
        Auth::forgetGuards();
        $this->getJson('/api/user')->assertOk()
            ->assertJsonPath('code', '05004')->assertJsonPath('data.id', $user->id);

        $previousId = session()->getId();
        $previousToken = session()->token();
        $this->postJson('/api/logout')->assertOk()->assertExactJson([
            'code' => '05003', 'status' => true, 'data' => null, 'responseStatus' => 200,
        ])->assertSessionMissing('marker');

        $this->assertNotSame($previousId, session()->getId());
        $this->assertNotSame($previousToken, session()->token());
        Auth::forgetGuards();
        $this->getJson('/api/user')->assertUnauthorized()->assertJsonPath('code', '00401');
        $this->assertFalse($user->checkPermissionTo('access-admin-panel', 'web'));
        $this->assertFalse($user->checkPermissionTo('run-artisan-runner-ui', 'web'));
    }

    public function test_wrong_credentials_return_the_user_error_code(): void
    {
        $user = User::factory()->create();

        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'wrong'])
            ->assertUnauthorized()->assertExactJson([
                'code' => '05002', 'status' => false, 'data' => null, 'responseStatus' => 401,
            ]);
        $this->assertGuest('web');
    }

    public function test_invalid_input_uses_the_shared_validation_response(): void
    {
        $this->postJson('/api/login', ['email' => ['invalid']])->assertUnprocessable()
            ->assertJsonPath('code', '00422')
            ->assertJsonStructure(['data' => ['errors' => ['email', 'password']]]);
    }

    public function test_login_from_an_untrusted_origin_cannot_create_a_session(): void
    {
        $user = User::factory()->create();

        $this->withHeader('Origin', 'http://localhost:9300')
            ->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertForbidden()->assertJsonPath('code', '00403');
        $this->assertGuest('web');
    }

    public function test_login_requires_csrf_protection_outside_the_test_environment(): void
    {
        $this->app->instance('env', 'local');

        $this->postJson('/api/login', ['email' => 'user@example.com', 'password' => 'password'])
            ->assertStatus(419)->assertJsonPath('code', '00419');
    }

    public function test_valid_csrf_token_allows_login_and_logout_requires_it(): void
    {
        $user = User::factory()->create();
        $this->app->instance('env', 'local');
        $this->get('/sanctum/csrf-cookie')->assertNoContent();

        $this->withHeader('X-CSRF-TOKEN', session()->token())
            ->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()->assertJsonPath('code', '05001');

        $this->withHeader('X-CSRF-TOKEN', 'incorrect')->postJson('/api/logout')
            ->assertStatus(419)->assertJsonPath('code', '00419');
        $this->assertAuthenticatedAs($user, 'web');
    }

    public function test_login_is_rate_limited(): void
    {
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->postJson('/api/login', ['email' => 'missing@example.com', 'password' => 'wrong'])
                ->assertUnauthorized();
        }

        $this->postJson('/api/login', ['email' => 'missing@example.com', 'password' => 'wrong'])
            ->assertTooManyRequests()->assertJsonPath('code', '00429');
    }
}
