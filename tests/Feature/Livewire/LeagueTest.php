<?php

namespace Tests\Feature\Livewire;

use App\Http\Livewire\Jersey;
use App\Http\Livewire\League;
use Database\Seeders\LeagueSeeder;
use Livewire\Livewire;
use App\Models\League as LeagueModel;
use Tests\TestCase;

class LeagueTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->seed(LeagueSeeder::class);
    }

    /** @test */
    public function league_page_can_be_rendered(): void
    {
        $res = $this->get(route('league'));

        $res->assertSee(trans('List of leagues'));
    }

    /** @test */
    public function league_page_contains_livewire_component(): void
    {
        $res = $this->get(route('league'));

        $res->assertSeeLivewire(League::class);
    }

    /** @test */
    public function league_page_pagination_should_be_working(): void
    {
        $leagues = LeagueModel::limit(League::$paginationCount)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $res = $this->get(route('league'));

        $res->assertSeeInOrder($leagues);
    }

    /** @test */
    public function league_page_pagination_page_2_should_be_working(): void
    {
        $leagues = LeagueModel::skip(League::$paginationCount)
            ->take(League::$paginationCount)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $res = $this->get(route('league', ['page' => 2]));

        $res->assertSeeInOrder($leagues);
    }

    /** @test */
    public function league_page_pagination_with_search_should_be_working(): void
    {
        $keyword = 'le';
        $leagues = LeagueModel::where('name', 'LIKE', "%$keyword%")
            ->take(League::$paginationCount)
            ->get()
            ->pluck('name', 'id')
            ->toArray();

        $res = Livewire::test(League::class)
            ->set('name', $keyword);

        $res->assertSeeInOrder($leagues);
    }


    /** @test */
    public function when_click_one_of_the_league_cards_must_be_redirect_to_the_jersey_page(): void
    {
        $queryString = 'premier-league';
        $league = LeagueModel::where('slug', $queryString)
            ->first();

        $res = $this->get(route('jersey', ['slug' => $league->slug]));

        $res->assertSee(trans('List of :league jerseys', ['league' => $league->name]));
    }
}
