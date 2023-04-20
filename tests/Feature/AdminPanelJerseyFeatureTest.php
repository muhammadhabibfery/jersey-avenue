<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Jersey;
use App\Models\League;
use Livewire\Livewire;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Database\Seeders\JerseySeeder;
use Database\Seeders\LeagueSeeder;
use Illuminate\Support\Facades\Storage;
use Filament\Tables\Actions\DeleteAction;
use App\Filament\Resources\JerseyResource;
use Filament\Tables\Actions\RestoreAction;
use Illuminate\Database\Eloquent\Collection;
use Filament\Tables\Actions\ForceDeleteAction;
use App\Filament\Resources\JerseyResource\Pages\EditJersey;
use App\Filament\Resources\JerseyResource\Pages\ViewJersey;
use App\Filament\Resources\JerseyResource\Pages\ListJerseys;
use App\Filament\Resources\JerseyResource\Pages\CreateJersey;

class AdminPanelJerseyFeatureTest extends TestCase
{
    private Collection $jerseys;
    private Jersey $jersey;
    private string $directory = 'jerseys';
    private int $paginationCount = 10;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        Storage::fake($this->directory);
        $this->seed([
            LeagueSeeder::class,
            JerseySeeder::class
        ]);
        $userAdmin = $this->authenticatedUser(['role' => User::$roles[0]]);
        Jersey::factory(['deleted_by' => $userAdmin->id, 'deleted_at' => now()->subMinute()])->create();
        $this->jerseys = Jersey::all();
        $this->jersey = $this->jerseys
            ->random(1)
            ->first();
    }

    /** @test */
    public function jersey_menu_list_can_be_rendered(): void
    {
        $this->get(JerseyResource::getUrl())
            ->assertSuccessful()
            ->assertSee(trans('List of jerseys'));
    }

    /** @test */
    public function jersey_menu_list_can_show_table_records(): void
    {
        Livewire::test(ListJerseys::class)
            ->assertCanSeeTableRecords($this->getPaginationData());
    }

    /** @test */
    public function jersey_menu_list_pagination_page_2_can_be_rendered(): void
    {
        Livewire::withQueryParams(['page' => 2])
            ->test(ListJerseys::class)
            ->assertCanSeeTableRecords($this->getPaginationData(true));
    }

    /** @test */
    public function jersey_menu_list_can_search_jersey_by_name(): void
    {
        Livewire::test(ListJerseys::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->searchTable($this->jersey->name)
            ->assertCanSeeTableRecords($this->getPaginationData(name: $this->jersey->name));
    }

    /** @test */
    public function jersey_menu_list_can_filter_jersey_by_league(): void
    {
        $league =  'ligue-1';

        Livewire::test(ListJerseys::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->filterTable('league', ['league' => $league])
            ->assertCanSeeTableRecords($this->getPaginationData(league: $league));
    }

    /** @test */
    public function jersey_menu_list_can_search_jersey_by_name_and_filter_by_league(): void
    {
        $name = 'liverpool';
        $league = 'serie-a';

        Livewire::test(ListJerseys::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->searchTable($name)
            ->assertCanSeeTableRecords($this->getPaginationData(name: $name))
            ->filterTable('league', ['league' => $league])
            ->assertCanSeeTableRecords($this->getPaginationData(name: $name, league: $league));
    }

    /** @test */
    public function only_admin_can_filter_trashed_jersey(): void
    {
        Livewire::test(ListJerseys::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->filterTable('trashed', false)
            ->assertCanSeeTableRecords($this->getPaginationData(withTrashedOption: 'onlyTrashed'));
    }

    /** @test */
    public function staff_can_not_filter_trashed_jersey(): void
    {
        $this->authenticatedUser(['role' => User::$roles[1]]);

        Livewire::test(ListJerseys::class)
            ->assertDontSee(__('tables::table.filters.trashed.label'))
            ->assertCanSeeTableRecords($this->getPaginationData());
    }

    /** @test */
    public function jersey_menu_create_can_be_rendered(): void
    {
        $this->get(JerseyResource::getUrl('create'))
            ->assertSuccessful()
            ->assertSee(trans('Create jersey'));
    }

    /** @test */
    public function jersey_menu_create_can_create_new_jersey(): void
    {
        $image = UploadedFile::fake()
            ->image('beatles.png');
        $image = ['image' => $image];
        $jersey = Jersey::factory()
            ->make();
        $data = array_merge($jersey->toArray(), $image);

        $res = Livewire::test(CreateJersey::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.resources.jerseys.index'));

        $image = $res->json()->payload['serverMemo']['data']['data']['image'][0];
        $this->deleteFile($image);

        $this->assertDatabaseHas(Jersey::class, array_merge($jersey->only(['name', 'slug']), ['image' => $image]))
            ->assertDatabaseCount(Jersey::class, 22);
    }

    /** @test */
    public function jersey_menu_create_the_validation_rules_should_be_dispatched(): void
    {
        $this->withExceptionHandling();
        $data = ['league_id' => 99, 'weight' => -1, 'price' => -1, 'S' => -1];
        $jersey = Jersey::factory()
            ->make();
        $data = array_merge(Arr::except($jersey->toArray(), ['image', 'stock']), $data);
        $data = array_merge($data, ['name' => $this->jersey->name]);

        Livewire::test(CreateJersey::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasFormErrors(['league_id' => 'exists', 'name' => 'unique', 'weight' => 'min', 'price' => 'min', 'S' => 'min']);
    }

    /** @test */
    public function jersey_menu_edit_can_be_rendered(): void
    {
        $this->get(JerseyResource::getUrl('edit', $this->jersey))
            ->assertSuccessful()
            ->assertSee(trans('Edit jersey'));
    }

    /** @test */
    public function jersey_menu_edit_can_retrieve_selected_jersey_data(): void
    {
        $jersey = $this->splitStock($this->jersey->toArray());
        $expectedData = Arr::except($jersey, ['image']);

        Livewire::test(EditJersey::class, ['record' => $this->jersey->slug])
            ->assertFormSet($expectedData);
    }

    /** @test */
    public function jersey_menu_edit_can_edit_selected_jersey(): void
    {
        $jersey = Arr::except(Jersey::factory()->make()->toArray(), ['sold']);
        $jersey = $this->splitStock($jersey);
        $image = UploadedFile::fake()
            ->image('beatles.png');
        $image = ['image' => $image];
        $data = array_merge($jersey, $image);

        $res = Livewire::test(EditJersey::class, ['record' => $this->jersey->slug])
            ->fillForm($data)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.resources.jerseys.index'));

        $image = $res->json()->payload['serverMemo']['data']['data']['image'][0];
        $this->deleteFile($image);
        $data = array_merge($data, ['image' => $image]);
        $data = $this->mergeStock($data);

        $this->assertDatabaseHas(Jersey::class, $data)
            ->assertDatabaseMissing(Jersey::class, $this->jersey->toArray());
    }

    /** @test */
    public function staff_cannot_edit_selected_jersey_created_by_admin(): void
    {
        $this->withExceptionHandling();
        $this->authenticatedUser(['role' => User::$roles[1]]);

        $this->get(JerseyResource::getUrl('edit', $this->jersey))
            ->assertForbidden();
    }

    /** @test */
    public function jersey_menu_delete_can_delete_selected_jersey(): void
    {
        Livewire::test(ListJerseys::class)
            ->callTableAction(DeleteAction::class, $this->jersey);

        $this->assertDatabaseMissing(Jersey::class, $this->jersey->toArray());
    }

    /** @test */
    public function staff_cannot_delete_selected_jersey_created_by_admin(): void
    {
        $this->withExceptionHandling();
        $this->authenticatedUser(['role' => User::$roles[1]]);

        Livewire::test(ListJerseys::class)
            ->assertTableActionHidden('delete', $this->jersey);
    }

    /** @test */
    public function jersey_menu_view_can_be_rendered(): void
    {
        $this->get(JerseyResource::getUrl('view', $this->jersey))
            ->assertSuccessful()
            ->assertSee(trans('Detail of jersey'));
    }

    /** @test */
    public function jersey_menu_view_can_retrieve_selected_jersey_data(): void
    {
        $jersey = Arr::except($this->jersey->toArray(), ['image']);
        $jersey = $this->convertStockToString($jersey);
        $expectedData = array_merge(
            $jersey,
            [
                'created_by' => (User::find($jersey['created_by']))->name,
                'league_id' => (League::find($jersey['league_id']))->name,
                'weight' => $jersey['weight'] . " Gram",
                'price' => currencyFormat($jersey['price']),
                'price_nameset' => currencyFormat($jersey['price_nameset']),
                'sold' => $jersey['sold'] . " pcs"
            ]
        );

        Livewire::test(ViewJersey::class, ['record' => $expectedData['slug']])
            ->assertFormSet($expectedData);
    }

    /** @test */
    public function only_admin_can_restore_deleted_jersey(): void
    {
        $jersey = $this->getPaginationData(withTrashedOption: 'onlyTrashed')->first();

        Livewire::test(ListJerseys::class)
            ->callTableAction(RestoreAction::class, $jersey);

        $this->assertTrue($this->getPaginationData(withTrashedOption: 'onlyTrashed')->count() === 0);
    }

    /** @test */
    public function staff_cannot_restore_deleted_travel_package(): void
    {
        $jersey = $this->getPaginationData(withTrashedOption: 'onlyTrashed')->first();
        $this->authenticatedUser(['role' => User::$roles[1]]);

        Livewire::test(ListJerseys::class)
            ->assertTableActionHidden('restore', $jersey);
    }

    /** @test */
    public function only_admin_can_force_delete_deleted_jersey(): void
    {
        $jersey = $this->getPaginationData(withTrashedOption: 'onlyTrashed')->first();

        Livewire::test(ListJerseys::class)
            ->callTableAction(ForceDeleteAction::class, $jersey);

        $this->assertDatabaseMissing(Jersey::class, $jersey->toArray());
        $this->assertDatabaseCount(Jersey::class, 20);
    }

    /** @test */
    public function staff_cannot_force_delete_deleted_jersey(): void
    {
        $jersey = $this->getPaginationData(withTrashedOption: 'onlyTrashed')->first();
        $this->authenticatedUser(['role' => User::$roles[1]]);

        Livewire::test(ListJerseys::class)
            ->assertTableActionHidden('forceDelete', $jersey);
    }

    private function getPaginationData(bool $secondPage = false, ?string $name = null, ?string $league = null, ?string $withTrashedOption = null): Collection
    {
        $jerseys = Jersey::where('name', 'LIKE', "%$name%")
            ->take($this->paginationCount);

        if ($league)
            $jerseys->hasLeague($league);

        if ($secondPage)
            $jerseys->skip($this->paginationCount);

        if ($withTrashedOption)
            $jerseys->{$withTrashedOption}();

        return $jerseys->orderBy('sold', 'desc')
            ->get();
    }

    private function splitStock(array $data): array
    {
        foreach ($data['stock'] as $key => $stock)
            $data[$key] = $stock;

        unset($data['stock']);
        return $data;
    }

    private function mergeStock(array $data): array
    {
        $data['stock'] = array_combine(
            Jersey::$sizes,
            [$data['S'], $data['M'], $data['L'], $data['XL']]
        );

        $data['stock'] = json_encode($data['stock']);

        foreach (Jersey::$sizes as $size)
            unset($data[$size]);

        return $data;
    }

    private function convertStockToString(array $data): array
    {
        $result = '';

        foreach ($data['stock'] as $key => $stock)
            $result .= "$key : $stock,  ";
        $result = rtrim($result, ',  ');

        $data['stock'] = $result;
        return $data;
    }
}
