<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Tests\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withHeader('Origin', 'http://localhost:9200');
        Notification::fake();
    }

    public function test_guests_cannot_read_status_send_mail_or_verify(): void
    {
        $user = User::factory()->unverified()->create();
        $this->getJson('/api/email/verify')->assertUnauthorized()->assertJsonPath('code', '00401');
        $this->postJson('/api/email/verification-notification')->assertUnauthorized();
        $this->getJson($this->link($user))->assertUnauthorized();
        Notification::assertNothingSent();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_unverified_users_can_check_status_and_request_a_signed_mail_link(): void
    {
        $this->freezeTime();
        $user = User::factory()->unverified()->create();
        $this->actingAs($user, 'web')->getJson('/api/email/verify')->assertOk()
            ->assertJsonPath('code', '05005')->assertJsonPath('data.verified', false)
            ->assertJsonPath('data.email_verified_at', null);
        $this->postJson('/api/email/verification-notification')->assertOk()
            ->assertJsonPath('code', '05006')->assertJsonPath('status', true)
            ->assertJsonPath('responseStatus', 200)->assertJsonPath('data.verified', false);
        Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user): bool {
            $url = $notification->toMail($user)->actionUrl;
            parse_str(parse_url($url, PHP_URL_QUERY), $query);
            $this->assertSame(now()->addMinutes(60)->timestamp, (int) $query['expires']);
            $this->assertStringContainsString('/api/email/verify/'.$user->id.'/', $url);
            $this->assertArrayHasKey('signature', $query);

            return true;
        });
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_configured_link_lifetime_is_used(): void
    {
        $this->freezeTime();
        config(['auth.verification.expire' => 15]);
        $user = User::factory()->unverified()->create();
        $this->actingAs($user, 'web')->postJson('/api/email/verification-notification')->assertOk();
        Notification::assertSentTo($user, VerifyEmail::class, function ($notification) use ($user): bool {
            parse_str(parse_url($notification->toMail($user)->actionUrl, PHP_URL_QUERY), $query);
            $this->assertSame(now()->addMinutes(15)->timestamp, (int) $query['expires']);

            return true;
        });
    }

    public function test_valid_link_verifies_only_once_and_returns_json(): void
    {
        Event::fake([Verified::class]);
        $user = User::factory()->unverified()->create();
        $url = $this->link($user);
        $this->actingAs($user, 'web')->getJson($url)->assertOk()
            ->assertJsonPath('code', '05007')->assertJsonPath('status', true)
            ->assertJsonPath('data.verified', true);
        $timestamp = $user->fresh()->email_verified_at;
        $this->assertNotNull($timestamp);
        $this->getJson($url)->assertOk()->assertJsonPath('code', '05008');
        $this->assertTrue($timestamp->eq($user->fresh()->email_verified_at));
        Event::assertDispatchedTimes(Verified::class, 1);
    }

    public function test_forged_expired_and_other_account_links_are_rejected(): void
    {
        $user = User::factory()->unverified()->create();
        $other = User::factory()->unverified()->create();
        $this->actingAs($user, 'web');
        $this->getJson($this->link($user).'&unexpected=1')->assertForbidden()->assertJsonPath('code', '00403');
        $expired = URL::temporarySignedRoute('verification.verify', now()->subMinute(), ['id' => $user->id, 'hash' => sha1($user->email)]);
        $this->getJson($expired)->assertForbidden();
        $this->getJson($this->link($other))->assertForbidden();
        $wrongHash = URL::temporarySignedRoute('verification.verify', now()->addHour(), ['id' => $user->id, 'hash' => sha1('other@example.com')]);
        $this->getJson($wrongHash)->assertForbidden();
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
        $this->assertFalse($other->fresh()->hasVerifiedEmail());
    }

    public function test_already_verified_account_does_not_receive_another_mail(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user, 'web')->postJson('/api/email/verification-notification')
            ->assertOk()->assertJsonPath('code', '05008')->assertJsonPath('data.verified', true);
        Notification::assertNothingSent();
    }

    public function test_resend_is_rate_limited(): void
    {
        $user = User::factory()->unverified()->create();
        $this->actingAs($user, 'web');
        for ($attempt = 0; $attempt < 6; $attempt++) {
            $this->postJson('/api/email/verification-notification')->assertOk();
        }
        $this->postJson('/api/email/verification-notification')->assertTooManyRequests()
            ->assertJsonPath('code', '00429')->assertHeader('Retry-After');
        Notification::assertSentToTimes($user, VerifyEmail::class, 6);
    }

    public function test_registered_event_uses_native_notification_and_unverified_login_remains_available(): void
    {
        $user = User::factory()->unverified()->create();
        event(new Registered($user));
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])
            ->assertOk()->assertJsonPath('code', '05001');
        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }

    public function test_resend_requires_csrf_in_a_stateful_session(): void
    {
        $user = User::factory()->unverified()->create();
        $this->app->instance('env', 'local');
        $this->actingAs($user, 'web')->postJson('/api/email/verification-notification')
            ->assertStatus(419)->assertJsonPath('code', '00419');
        Notification::assertNothingSent();
    }

    public function test_email_link_uses_the_session_when_opened_without_a_frontend_origin(): void
    {
        $user = User::factory()->unverified()->create();
        $url = $this->link($user);
        $this->postJson('/api/login', ['email' => $user->email, 'password' => 'password'])->assertOk();
        Auth::forgetGuards();
        $this->flushHeaders()->get($url)->assertOk()->assertJsonPath('code', '05007');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
    }

    private function link(User $user): string
    {
        return URL::temporarySignedRoute('verification.verify', now()->addMinutes(60), [
            'id' => $user->id, 'hash' => sha1($user->email),
        ]);
    }
}
