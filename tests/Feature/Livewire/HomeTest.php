<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Home;
use App\Models\Jersey;
use App\Models\League;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use Tests\TestCase;

class HomeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
    }

    /** @test */
    public function home_page_can_be_rendered(): void
    {
        $res = $this->get(route('home'));

        $res->assertOk()
            ->assertSee('Jersey Avenue')
            ->assertSee('Wear Your Passion on Your Sleeve');
    }

    /** @test */
    public function navbar_guest_can_be_rendered(): void
    {
        $res = $this->get(route('home'));

        $res->assertSee(trans('Login'))
            ->assertSee(trans('Register'));
    }

    /** @test */
    public function home_page_contains_livewire_component(): void
    {
        $res = $this->get(route('home'));

        $res->assertSeeLivewire(Home::class);
    }

    /** @test */
    public function home_page_contains_top_4_leagues(): void
    {
        $this->seed(LeagueSeeder::class);
        $top4Leagues = League::limit(4)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $res = $this->get(route('home'));

        $res->assertSeeInOrder($top4Leagues);
    }

    /** @test */
    public function home_page_contains_top_4_jerseys(): void
    {
        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);
        $top4Jerseys = Jersey::where('sold', '>=', 4)
            ->orderBy('sold', 'desc')
            ->limit(4)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $res = $this->get(route('home'));

        $res->assertSeeInOrder($top4Jerseys);
    }
}
