<?php

namespace Tests\Feature\Auth;

use App\Models\Jersey;
use App\Models\User;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use Tests\TestCase;
use Tests\Validation\AuthenticationValidationTest;

class AuthenticationTest extends TestCase
{
    use AuthenticationValidationTest;

    public User $user;
    public string $fakePassword = 'abcabcabcabc';
    public string $realPassword = 'abc@123123';

    public array $data = [
        'username' => 'johnlennon',
        'email' => 'johnlennon@beatles.com',
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();

        $this->data['role'] = User::$roles[2];
        $this->user = $this->createUser($this->data);
    }

    /** @test */
    public function login_page_can_be_rendered(): void
    {
        $res = $this->get(route('login'));

        $res->assertOk()
            ->assertSee(trans('Login'));
    }

    /** @test */
    public function user_can_login_with_email(): void
    {
        $credentials = ['username' => $this->data['email'], 'password' => $this->realPassword];

        $res = $this->post(route('login'), $credentials);

        $res->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    /** @test */
    public function user_can_login_with_username(): void
    {
        $credentials = ['username' => $this->data['username'], 'password' => $this->realPassword];

        $res = $this->post(route('login'), $credentials);

        $res->assertRedirect(route('home'));
        $this->assertAuthenticated();
    }

    /** @test */
    public function authenticated_user_can_access_the_authenticated_routes(): void
    {
        $res = $this->actingAs($this->user)
            ->get(route('dashboard'));

        $res->assertOk()
            ->assertSee(trans('Dashboard'));
        $this->assertAuthenticated();
    }

    /** @test */
    public function if_there_is_cart_route_session_then_should_redirect_to_jersey_detail_page(): void
    {
        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);
        $jersey = Jersey::bestSeller()
            ->inRandomOrder()
            ->first();
        $sessionData = route('jersey.detail', $jersey);
        $credentials = ['username' => $this->data['username'], 'password' => $this->realPassword];

        $res = $this->withSession(['cartRoute' => $sessionData])
            ->post(route('login'), $credentials);

        $res->assertRedirect($sessionData);
        $this->assertAuthenticated();
    }
}
