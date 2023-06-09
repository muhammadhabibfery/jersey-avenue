<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use App\Models\User;
use App\Models\Jersey;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Event;
use App\Providers\RouteServiceProvider;
use Tests\Validation\EmailVerificationValidationTest;

class EmailVerificationTest extends TestCase
{
    use EmailVerificationValidationTest;

    public User $userVerified, $userNotVerified;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->userVerified = $this->createUser(['username' => 'paul', 'email' => 'paul@beatles.com', 'role' => User::$roles[2]]);
        $this->userNotVerified = $this->createUser(['username' => 'johnlennon', 'email' => 'johnlennon@beatles.com', 'email_verified_at' => null, 'role' => User::$roles[2]]);
    }

    /** @test */
    public function verify_email_page_can_be_rendered(): void
    {
        $res = $this->actingAs($this->userNotVerified)
            ->get(route('verification.notice'));

        $res->assertOk()
            ->assertSee(trans('Verification Email Address'));
    }

    /** @test */
    public function user_can_verify_email(): void
    {
        Event::fake();
        $user = $this->userNotVerified;

        $res = $this->actingAs($user)
            ->get($this->getVerificationUrl($user->id, $user->email));

        $res->assertRedirect(route('home'))
            ->assertSessionHas('status');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    /** @test */
    public function user_can_resend_verify_link(): void
    {
        $res = $this->actingAs($this->userNotVerified)
            ->post(route('verification.send'));

        $res->assertRedirect(route('home'))
            ->assertSessionHas('status');
    }

    /** @test */
    public function verified_user_can_access_the_verified_routes(): void
    {
        $res = $this->actingAs($this->userVerified)
            ->get(route('cart'));

        $res->assertOk()
            ->assertSee(trans('Shopping Cart'));
    }

    /** @test */
    public function if_there_is_cart_route_session_then_should_redirect_to_jersey_detail_page_after_verify_email(): void
    {
        Event::fake();
        $user = $this->userNotVerified;
        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);
        $jersey = Jersey::bestSeller()
            ->inRandomOrder()
            ->first();
        $sessionData = route('jersey.detail', $jersey);

        $res = $this->actingAs($user)
            ->withSession(['cartRoute' => $sessionData])
            ->get($this->getVerificationUrl($user->id, $user->email));

        $res->assertRedirect($sessionData)
            ->assertSessionHas('status');
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        Event::assertDispatched(Verified::class);
    }

    private function verifyData(int $id, string $email): array
    {
        return ['id' => $id, 'hash' => sha1($email)];
    }

    private function getVerificationUrl(int $id, string $email): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            $this->verifyData($id, $email)
        );
    }
}
