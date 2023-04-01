<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;
use Tests\Validation\PasswordResetValidationTest;

class PasswordResetTest extends TestCase
{
    use PasswordResetValidationTest;

    public User $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->user = $this->createUser(['email' => 'johnlennon@beatles.com', 'role' => User::$roles[2]]);
    }

    /** @test */
    public function forgot_password_page_can_be_rendered(): void
    {
        $res = $this->get(route('password.request'));

        $res->assertOk()
            ->assertSeeText(trans('Email Password Reset Link'));
    }


    /** @test */
    public function user_can_send_the_reset_password_link(): void
    {
        Notification::fake();

        $res = $this->post(route('password.email'), ['email' => $this->user->email]);

        $res->assertSessionHas('status');
        Notification::assertSentTo($this->user, ResetPasswordNotification::class);
    }

    /** @test */
    public function reset_password_page_can_be_rendered(): void
    {
        $res = $this->get(route('password.reset', 'token123'));

        $res->assertOk()
            ->assertSeeText(trans('Reset Password'));
    }

    /** @test */
    public function user_can_reset_password(): void
    {
        Event::fake();
        $newPassword = 'secret@12345';
        $token = Password::createToken($this->user);

        $res = $this->post(route('password.store'), $this->resetPasswordData($this->user->email, $newPassword, $newPassword, $token));

        $res->assertValid()
            ->assertSessionHas('status');

        Event::assertDispatched(PasswordReset::class);

        $this->assertTrue(Hash::check($newPassword, $this->user->fresh()->getAuthPassword()));
        $this->assertDatabaseMissing('password_resets', ['email' => $this->user->email, 'token' => $token]);
    }

    private function resetPasswordData(string $email, string $password, string $passwordConfirmation, string $token): array
    {
        return [
            'email' => $email,
            'password' => $password,
            'password_confirmation' => $passwordConfirmation,
            'token' => $token,
        ];
    }

    private function getToken(): string
    {
        return Password::createToken($this->user);
    }
}
