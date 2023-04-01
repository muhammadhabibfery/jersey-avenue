<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Notifications\VerifyEmail;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\Validation\RegistrationValidationTest;

class RegistrationTest extends TestCase
{
    use RegistrationValidationTest;

    protected array $data = [
        'name' => 'John Lennon',
        'username' => 'johnlennon',
        'email' => 'johnlennon@beatles.com',
        'password' => 'abc@1234567',
        'password_confirmation' => 'abc@1234567',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function registration_page_can_be_rendered(): void
    {
        $res = $this->get(route('register'));

        $res->assertViewIs('auth.register')
            ->assertSee(trans('Register'));
    }

    /** @test */
    public function user_can_register(): void
    {
        Event::fake();
        Notification::fake();

        $res = $this->post(route('register'), $this->data);

        $res->assertValid()
            ->assertSessionHas('status')
            ->assertRedirect(route('login'));
        $this->assertDatabaseHas(User::class, Arr::except($this->data, ['password', 'password_confirmation']))
            ->assertDatabaseCount(User::class, 1);

        $user = User::first();
        $user->notify(new VerifyEmail);
        Event::assertDispatched(Registered::class);
        Event::assertListening(Registered::class, SendEmailVerificationNotification::class);
        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
