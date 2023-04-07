<?php

namespace Tests\Feature\Livewire;

use Tests\TestCase;
use App\Models\League;
use App\Models\Jersey as JerseyModel;
use Livewire\Livewire;
use App\Http\Livewire\Jersey;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;

class JerseyTest extends TestCase
{
    private ?string $keyword = 'ar', $queryString = 'premier-league';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->seed(LeagueSeeder::class);
        $this->seed(JerseySeeder::class);
    }

    /** @test */
    public function jersey_page_can_be_rendered_without_query_string(): void
    {
        $res = $this->get(route('jersey'));

        $res->assertSee(trans('List of :league jerseys', ['league' => trans('all leagues')]))
            ->assertSeeInOrder($this->getPaginationData());
    }

    /** @test */
    public function jersey_page_can_be_rendered_with_query_string(): void
    {
        $league = League::where('slug', $this->queryString)
            ->first();

        $res = $this->get(route('jersey', ['name' => $league->slug]));

        $res->assertSee(trans('List of :league jerseys', ['league' => $league->name]))
            ->assertSeeInOrder($this->getPaginationData(queryString: $this->queryString));
    }

    /** @test */
    public function jersey_page_contains_livewire_component(): void
    {
        $res = $this->get(route('jersey'));

        $res->assertSeeLivewire(Jersey::class);
    }

    /** @test */
    public function jersey_page_pagination_page_2_should_be_working(): void
    {
        $res = $this->get(route('jersey', ['page' => 2]));

        $res->assertSeeInOrder($this->getPaginationData(true));
    }

    /** @test */
    public function jersey_page_can_search_jersey_by_name(): void
    {
        $res = Livewire::test(Jersey::class)
            ->set('name', $this->keyword);

        $res->assertSeeInOrder($this->getPaginationData(keyword: $this->keyword));
    }

    /** @test */
    public function jersey_page_can_search_jersey_by_league_select_option(): void
    {
        $res = Livewire::test(Jersey::class)
            ->set('leagueSelected', $this->queryString);

        $res->assertSeeInOrder($this->getPaginationData(queryString: $this->queryString));
    }

    /** @test */
    public function jersey_page_pagination_with_input_search_and_select_box_should_be_working(): void
    {
        $res = Livewire::test(Jersey::class)
            ->set('name', $this->keyword)
            ->set('leagueSelected', $this->queryString);

        $res->assertSeeInOrder($this->getPaginationData(keyword: $this->keyword, queryString: $this->queryString));
    }

    /** @test */
    public function when_click_one_of_the_jersey_cards_must_be_redirect_to_the_jersey_detail_page(): void
    {
        $jersey = JerseyModel::inRandomOrder()
            ->first();

        $res = $this->get(route('jersey.detail', $jersey));

        $res->assertSee(trans(':club jersey details', ['club' => $jersey->name]));
    }

    private function getPaginationData(bool $secondPaginationData = false, string $keyword = '',  ?string $queryString = null): array
    {
        $jerseys = JerseyModel::where('name', 'LIKE', "%$keyword%")
            ->take(Jersey::$paginationCount);

        if ($secondPaginationData)
            $jerseys->skip(Jersey::$paginationCount);

        if ($queryString)
            $jerseys->hasLeague($queryString);

        return $jerseys->get()
            ->pluck('name', 'id')
            ->toArray();
    }
}
