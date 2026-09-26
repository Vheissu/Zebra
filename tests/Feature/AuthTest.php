<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    public function test_people_can_register_and_are_signed_in(): void
    {
        $this->post('/register', [
            'username' => 'new_zebra',
            'email' => 'new@example.com',
            'password' => 'correct horse',
            'password_confirmation' => 'correct horse',
        ])->assertRedirect('/');

        $this->assertAuthenticatedAs(User::whereUsername('new_zebra')->sole());
    }

    public function test_usernames_are_unique_regardless_of_case(): void
    {
        User::factory()->create(['username' => 'Zebra']);

        $this->post('/register', [
            'username' => 'zebra',
            'email' => 'other@example.com',
            'password' => 'correct horse',
            'password_confirmation' => 'correct horse',
        ])->assertSessionHasErrors(['username' => 'That username is taken.']);
    }

    public function test_usernames_are_limited_to_safe_characters(): void
    {
        $this->post('/register', [
            'username' => 'bad name!',
            'email' => 'x@example.com',
            'password' => 'correct horse',
            'password_confirmation' => 'correct horse',
        ])->assertSessionHasErrors('username');
    }

    public function test_sign_in_with_username_in_any_case(): void
    {
        $user = User::factory()->create(['username' => 'Quagga']);

        $this->post('/login', ['username' => 'quagga', 'password' => 'password'])->assertRedirect('/');

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();

        $this->post('/login', ['username' => $user->username, 'password' => 'nope'])->assertSessionHasErrors('username');

        $this->assertGuest();
    }

    public function test_banned_members_cannot_sign_in(): void
    {
        $user = User::factory()->banned()->create();

        $this->post('/login', ['username' => $user->username, 'password' => 'password'])
            ->assertSessionHasErrors(['username' => 'This account has been suspended.']);

        $this->assertGuest();
    }

    public function test_members_banned_mid_session_are_signed_out(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->get('/settings')->assertOk();

        $user->forceFill(['banned_at' => now()])->save();

        $this->get('/settings')->assertRedirect('/login');
        $this->assertGuest();
    }

    public function test_password_reset_round_trip(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'forgetful@example.com']);

        $this->post('/forgot-password', ['email' => 'forgetful@example.com'])->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token) {
            $token = $notification->token;

            return true;
        });

        $this->get("/reset-password/{$token}?email=forgetful@example.com")->assertOk();

        $this->post('/reset-password', [
            'token' => $token,
            'email' => 'forgetful@example.com',
            'password' => 'brand new password',
            'password_confirmation' => 'brand new password',
        ])->assertRedirect('/login');

        $this->assertTrue(Hash::check('brand new password', $user->fresh()->password));
    }

    public function test_reset_request_does_not_reveal_whether_an_account_exists(): void
    {
        Notification::fake();

        $this->post('/forgot-password', ['email' => 'nobody@example.com'])
            ->assertSessionHas('status', 'If that address has an account, a reset link is on its way.');

        Notification::assertNothingSent();
    }

    public function test_reset_with_a_bad_token_fails(): void
    {
        User::factory()->create(['email' => 'a@example.com']);

        $this->post('/reset-password', [
            'token' => 'forged',
            'email' => 'a@example.com',
            'password' => 'brand new password',
            'password_confirmation' => 'brand new password',
        ])->assertSessionHasErrors('email');
    }

    public function test_settings_update_profile_and_password(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->put('/settings', ['email' => 'changed@example.com', 'about' => 'Hello'])->assertSessionHas('status');
        $this->actingAs($user)->put('/settings/password', [
            'current_password' => 'password',
            'password' => 'another password',
            'password_confirmation' => 'another password',
        ])->assertSessionHas('status');

        $user->refresh();
        $this->assertSame('changed@example.com', $user->email);
        $this->assertSame('Hello', $user->about);
        $this->assertTrue(Hash::check('another password', $user->password));
    }

    public function test_profiles_do_not_show_email(): void
    {
        $user = User::factory()->create(['email' => 'secret@example.com', 'about' => 'Stripes']);

        $this->get("/u/{$user->username}")->assertOk()->assertSee('Stripes')->assertDontSee('secret@example.com');
    }
}
