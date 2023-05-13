<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Jersey;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as TwoUser;

class SocialiteTest extends TestCase
{
    public array $socialLinks;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->socialLinks = [
            'google' => 'https://accounts.google.com/o/oauth2/auth/oauthchooseaccount'
        ];
    }

    /** @test */
    public function socialite_google_button_can_redirect_to_google_auth(): void
    {
        $this->createMockupSocialiteRedirect('google');

        $this->get(route('auth.redirect.google'))
            ->assertRedirect($this->socialLinks['google']);
    }

    /** @test */
    public function user_can_register_or_login_with_google_account(): void
    {
        $user = $this->createSocialiteUser();
        $this->createMockupSocialiteUser($user);

        $this->get(route('auth.callback.google'))
            ->assertRedirect(route('home'));
        $this->assertAuthenticated();

        $this->checkExistingUser();
    }

    /** @test */
    public function user_can_register_or_login_with_google_account_and_if_there_is_cart_route_session_then_should_redirect_to_jersey_detail_page(): void
    {
        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);
        $jersey = Jersey::bestSeller()
            ->inRandomOrder()
            ->first();
        $sessionData = route('jersey.detail', $jersey);
        $user = $this->createSocialiteUser();
        $this->createMockupSocialiteUser($user);


        $this->withSession(['cartRoute' => $sessionData])
            ->get(route('auth.callback.google'))
            ->assertRedirect($sessionData);
        $this->assertAuthenticated();

        $this->checkExistingUser();
    }

    private function createMockupSocialiteRedirect(string $driver): void
    {
        Socialite::shouldReceive('driver->redirect')
            ->once()
            ->andReturn(redirect($this->socialLinks[$driver]));
    }

    private function createMockupSocialiteUser(TwoUser $user): void
    {
        Socialite::shouldReceive('driver->user')
            ->once()
            ->andReturnUsing(fn (): TwoUser => $user);
    }

    private function createSocialiteUser(): TwoUser
    {
        $email = fake()->safeEmailDomain();
        return (new TwoUser)->setRaw([])->map([
            'id' => bin2hex(random_bytes(21)),
            'nickname' => fake()->firstName(),
            'name' => fake()->name(),
            'email' => $email,
            'avatar' => $avatarUrl = fake()->randomLetter(),
            'avatar_original' => $avatarUrl,
            'verified_email' => $email
        ]);
    }

    private function checkExistingUser(): void
    {
        $user = User::first();
        $this->assertDatabaseHas(User::class, $user->only(['google_id', 'name', 'email']));
    }
}
