<?php

namespace Tests\Feature;

use App\Filament\Resources\UserResource;
use App\Filament\Resources\UserResource\Pages\CreateUser;
use App\Filament\Resources\UserResource\Pages\EditUser;
use App\Filament\Resources\UserResource\Pages\ListUsers;
use App\Filament\Resources\UserResource\Pages\ViewUser;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Livewire;

class AdminPanelUserManagementFeatureTest extends TestCase
{
    private Collection $users;
    private User $user;
    private User $authenticatedUser;
    private array $roles;
    private int $paginationCount = 10;

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->roles = Arr::except(User::$roles, [2]);

        for ($i = 0; $i < 18; $i++)
            User::factory(['role' => Arr::random($this->roles), 'status' => Arr::random(User::$status)])
                ->create();

        $this->authenticatedUser = $this->authenticatedUser(['role' => $this->roles[0]]);

        $this->users = User::where('id', '!=', $this->authenticatedUser->id)
            ->get();
        $this->user = $this->users
            ->random(1)
            ->first();
    }

    /** @test */
    public function user_menu_list_can_be_rendered(): void
    {
        $this->get(UserResource::getUrl())
            ->assertSuccessful()
            ->assertSee(trans('List of users'));
    }

    /** @test */
    public function user_menu_list_can_show_table_records(): void
    {
        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($this->getPaginationData());
    }

    /** @test */
    public function user_menu_list_pagination_page_2_can_be_rendered(): void
    {
        Livewire::withQueryParams(['page' => 2])
            ->test(ListUsers::class)
            ->assertCanSeeTableRecords($this->getPaginationData(true));
    }

    /** @test */
    public function user_menu_list_can_search_users_by_name_or_username(): void
    {
        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->searchTable($this->user->name)
            ->assertCanSeeTableRecords($this->getPaginationData(search: $this->user->name));
    }

    /** @test */
    public function user_menu_list_can_filter_users_by_role(): void
    {
        $role = Arr::random($this->roles);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->filterTable('role', $role)
            ->assertCanSeeTableRecords($this->getPaginationData(filters: [['role', $role]]));
    }

    /** @test */
    public function user_menu_list_can_filter_users_by_status(): void
    {
        $status = Arr::random(User::$status);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->filterTable('status', $status)
            ->assertCanSeeTableRecords($this->getPaginationData(filters: [['status', $status]]));
    }

    /** @test */
    public function user_menu_list_can_search_users_by_name_or_username_and_filter_by_role_and_filter_by_status(): void
    {
        $username = $this->user->username;
        $role = Arr::random($this->roles);
        $status = Arr::random(User::$status);

        Livewire::test(ListUsers::class)
            ->assertCanSeeTableRecords($this->getPaginationData())
            ->searchTable($username)
            ->assertCanSeeTableRecords($this->getPaginationData(search: $username))
            ->filterTable('role', $role)
            ->assertCanSeeTableRecords($this->getPaginationData(search: $username, filters: [['role', $role]]))
            ->filterTable('status', $status)
            ->assertCanSeeTableRecords($this->getPaginationData(search: $username, filters: [['role', $role], ['status', $status]]));
    }

    /** @test */
    public function user_menu_create_can_be_rendered(): void
    {
        $this->get(UserResource::getUrl('create'))
            ->assertSuccessful()
            ->assertSee(trans('Create user'));
    }

    /** @test */
    public function user_menu_create_can_create_new_user(): void
    {
        $data = User::factory(['role' => $this->roles[1], 'created_by' => $this->authenticatedUser->id])
            ->make()
            ->toArray();

        Livewire::test(CreateUser::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.resources.users.index'));

        $this->assertDatabaseHas(User::class, Arr::except($data, ['email_verified_at']));
    }

    /** @test */
    public function user_menu_create_the_validation_rules_should_be_dispatched(): void
    {
        $data = User::factory(['role' => $this->roles[1]])
            ->create()
            ->toArray();

        Livewire::test(CreateUser::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique', 'phone' => 'unique']);
    }

    /** @test */
    public function user_menu_edit_can_be_rendered(): void
    {
        $user = $this->user
            ->where('role', $this->roles[1])
            ->first();

        $this->get(UserResource::getUrl('edit', $user))
            ->assertSuccessful()
            ->assertSee(trans('Edit user'));
    }

    /** @test */
    public function user_menu_edit_can_retrieve_selected_user_menu(): void
    {
        $user = $this->user
            ->where('role', $this->roles[1])
            ->first();

        Livewire::test(EditUser::class, ['record' => $user->username])
            ->assertFormSet($user->toArray());
    }

    /** @test */
    public function user_menu_edit_can_edit_selected_user_who_has_staff_role(): void
    {
        $user = $this->user
            ->where('role', $this->roles[1])
            ->first();
        $data = ['role' => Arr::random($this->roles), 'status' => Arr::random(User::$status)];

        Livewire::test(EditUser::class, ['record' => $user->username])
            ->fillForm($data)
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.resources.users.index'));

        $data = array_merge($data, ['username' => $user->username]);
        $this->assertDatabaseHas(User::class, $data);
    }

    /** @test */
    public function user_menu_edit_can_edit_the_validation_rules_should_be_dispatcehd(): void
    {
        $user = $this->user
            ->where('role', $this->roles[1])
            ->first();
        $data = ['role' => 'MEMBER', 'status' => 'ACTIVATED'];

        Livewire::test(EditUser::class, ['record' => $user->username])
            ->fillForm($data)
            ->call('save')
            ->assertHasFormErrors(['role' => 'in', 'status' => 'in']);
    }

    /** @test */
    public function admin_cannot_edit_selected_user_who_has_admin_or_customer_role(): void
    {
        $this->withExceptionHandling();
        $user = $this->user
            ->where('role', $this->roles[0])
            ->first();

        Livewire::test(EditUser::class, ['record' => $user->username])
            ->assertForbidden();
    }

    /** @test */
    public function user_menu_delete_can_delete_user_who_has_staff_role_and_status_is_inactive(): void
    {
        $user = User::factory(['role' => $this->roles[1], 'status' => User::$status[1]])
            ->create();

        Livewire::test(ListUsers::class)
            ->callTableAction('delete', $user);

        $this->assertDatabaseMissing(User::class, $user->toArray());
    }

    /** @test */
    public function admin_cannot_delete_user_who_has_admin_or_customer_role(): void
    {
        $user = $this->users
            ->where('role', $this->roles[0])
            ->first();

        Livewire::test(ListUsers::class)
            ->assertTableActionHidden('delete', $user);
    }

    /** @test */
    public function admin_cannot_delete_user_who_has_staff_role_and_which_status_is_active(): void
    {
        $user = User::factory(['role' => $this->roles[1], 'status' => User::$status[0]])
            ->create();

        Livewire::test(ListUsers::class)
            ->assertTableActionHidden('delete', $user);
    }

    /** @test */
    public function user_menu_view_can_be_rendered(): void
    {
        $this->get(UserResource::getUrl('view', $this->user))
            ->assertSuccessful()
            ->assertSee(trans('Detail of user'));
    }

    /** @test */
    public function user_menu_view_can_retrieve_selected_user_data(): void
    {
        Livewire::test(ViewUser::class, ['record' => $this->user->username])
            ->assertFormSet($this->user->toArray());
    }

    private function getPaginationData(bool $secondPage = false, ?string $search = null, ?array $filters = null): ?Collection
    {
        $users = User::where(function (Builder $query) use ($search): Builder {
            return $query->where('name', 'LIKE', "%$search%")
                ->orWhere('username', 'LIKE', "%$search%");
        })
            ->where('id', '!=', $this->authenticatedUser->id)
            ->take($this->paginationCount);

        if ($filters) {
            foreach ($filters as $filter)
                $users->where($filter[0], $filter[1]);
        }

        if ($secondPage)
            $users->skip($this->paginationCount);

        return $users->get();
    }
}
