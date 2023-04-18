<?php

namespace Tests\Feature;

use App\Filament\Resources\LeagueResource;
use App\Filament\Resources\LeagueResource\Pages\CreateLeague;
use App\Filament\Resources\LeagueResource\Pages\EditLeague;
use App\Filament\Resources\LeagueResource\Pages\ListLeagues;
use App\Filament\Resources\LeagueResource\Pages\ViewLeague;
use App\Models\League;
use App\Models\User;
use Database\Seeders\LeagueSeeder;
use Filament\Tables\Actions\DeleteAction;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;

class AdminPanelLeagueFeatureTest extends TestCase
{
    private Collection $leagues;
    private string $directory = 'leagues';

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        Storage::fake($this->directory);
        $this->authenticatedUser(['role' => User::$roles[0]]);
        $this->seed(LeagueSeeder::class);
        $this->leagues = League::all();
    }

    /** @test */
    public function league_menu_list_can_be_rendered(): void
    {
        $this->get(LeagueResource::getUrl())
            ->assertSuccessful()
            ->assertSee(trans('List of leagues'));
    }

    /** @test */
    public function league_menu_list_can_show_table_records(): void
    {
        Livewire::test(ListLeagues::class)
            ->assertCanSeeTableRecords($this->leagues);
    }

    /** @test */
    public function league_menu_list_can_search_league_by_name_or_country(): void
    {
        $name = $this->leagues->last()->name;
        // $country = $this->leagues->last()->country;

        Livewire::test(ListLeagues::class)
            ->searchTable($name)
            ->assertCanSeeTableRecords($this->leagues->where('name', $name))
            ->assertCanNotSeeTableRecords($this->leagues->where('name', '!=', $name));
    }

    /** @test */
    public function league_menu_create_can_be_rendered(): void
    {
        $this->get(LeagueResource::getUrl('create'))
            ->assertSuccessful()
            ->assertSee(trans('Create league'));
    }

    /** @test */
    public function league_menu_create_can_create_new_league(): void
    {
        $image = UploadedFile::fake()
            ->image('beatles.png');
        $image = ['image' => $image];
        $league = League::factory(['name' => 'championship', 'slug' => 'championship', 'country' => 'england'])
            ->make();
        $data = $this->newData($league->toArray(), $image);

        $res = Livewire::test(CreateLeague::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors();

        $image = $res->json()->payload['serverMemo']['data']['data']['image'][0];
        $this->deleteFile($image);

        $this->assertDatabaseHas(League::class, $this->newData($league->only(['name', 'slug']), ['image' => $image]));
        $this->assertDatabaseCount(League::class, 6);
    }

    /** @test */
    public function league_menu_create_the_validation_rules_should_be_dispatched(): void
    {
        $this->withExceptionHandling();
        $data = League::factory()
            ->make()
            ->toArray();
        $data = Arr::except($data, ['image']);
        $data = array_merge($data, ['name' => $this->leagues->random(1)->first()->name]);

        Livewire::test(CreateLeague::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasFormErrors(['name' => 'unique', 'image' => 'required']);
    }

    /** @test */
    public function league_menu_edit_can_be_rendered(): void
    {
        $league = $this->leagues
            ->random(1)
            ->first();

        $this->get(LeagueResource::getUrl('edit', $league->slug))
            ->assertSuccessful()
            ->assertSee(trans('Edit league'));
    }

    /** @test */
    public function league_menu_edit_can_retrieve_selected_league_data(): void
    {
        $league = $this->leagues
            ->random(1)
            ->first();
        $expectedData = Arr::except($league->toArray(), ['image']);

        Livewire::test(EditLeague::class, ['record' => $league->slug])
            ->assertFormSet($expectedData);
    }

    /** @test */
    public function league_menu_edit_can_edit_selected_league(): void
    {
        $league = $this->leagues
            ->random(1)
            ->first();
        $image = UploadedFile::fake()
            ->image('beatles.png');
        $image = ['image' => $image];
        $data = League::factory()
            ->make();
        $data = $this->newData($data->toArray(), $image);

        $res = Livewire::test(EditLeague::class, ['record' => $league->slug])
            ->fillForm($data)
            ->call('save')
            ->assertHasNoFormErrors();

        $image = $res->json()->payload['serverMemo']['data']['data']['image'][0];
        $this->deleteFile($image);
        $data = $this->newData($data, ['image' => $image]);

        $this->assertDatabaseHas(League::class, $data)
            ->assertDatabaseMissing(League::class, $league->only(['name', 'slug', 'country', 'image']));
    }

    /** @test */
    public function admin_cannot_edit_selected_league_created_by_staff(): void
    {
        $this->withExceptionHandling();
        $userStaff = $this->createUser(['role' => User::$roles[1]]);
        $league = League::factory(['created_by' => $userStaff->id])
            ->create();

        $this->get(LeagueResource::getUrl('edit', $league))
            ->assertForbidden();
    }

    /** @test */
    public function league_menu_delete_can_delete_selected_league(): void
    {
        $league = $this->leagues
            ->random(1)
            ->first();

        Livewire::test(ListLeagues::class)
            ->callTableAction(DeleteAction::class, $league);

        $this->assertDatabaseMissing(League::class, $league->toArray());
    }

    /** @test */
    public function admin_cannot_delete_selected_league_created_by_staff(): void
    {
        $userStaff = $this->createUser(['role' => User::$roles[1]]);
        $league = League::factory(['created_by' => $userStaff->id])
            ->create();

        Livewire::test(ListLeagues::class)
            ->assertTableActionHidden('delete', $league);
    }

    /** @test */
    public function league_menu_view_can_be_rendered(): void
    {
        $league = $this->leagues
            ->random(1)
            ->first();

        $this->get(LeagueResource::getUrl('view', $league))
            ->assertSuccessful()
            ->assertSee(trans('Detail of league'));
    }

    /** @test */
    public function league_menu_view_can_retrieve_selected_league_data(): void
    {
        $league = $this->leagues
            ->random(1)
            ->first();
        $expectedData = Arr::except($league->toArray(), ['image']);
        $expectedData = $this->newData($expectedData, ['created_by' => (User::find($league->created_by))->name]);

        Livewire::test(ViewLeague::class, ['record' => $league->slug])
            ->assertFormSet($expectedData);
    }

    private function newData(array $data, ?array $additionalData = null): array
    {
        if ($additionalData) $data = array_merge($data, $additionalData);

        return $data;
    }
}
