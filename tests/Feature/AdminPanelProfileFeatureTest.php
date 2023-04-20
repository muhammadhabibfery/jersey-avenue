<?php

namespace Tests\Feature;

use App\Filament\Resources\ProfileResource;
use App\Filament\Resources\ProfileResource\Pages\CreateProfile;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

class AdminPanelProfileFeatureTest extends TestCase
{
    public User $userAdmin;
    private string $directory = 'avatars';
    public array $data = [
        'username' => 'johnlennon',
        'email' => 'johnlennon@beatles.com',
        'phone' => '12344567890'
    ];

    public function setUp(): void
    {
        parent::setUp();
        $this->withoutExceptionHandling();
        $this->userAdmin = $this->authenticatedUser(['role' => User::$roles[0]]);
    }

    /** @test */
    public function profile_menu_can_be_rendered(): void
    {
        $this->get(ProfileResource::getUrl())
            ->assertSuccessful()
            ->assertSee($this->userAdmin->name);
    }

    /** @test */
    public function profile_menu_can_retrieve_data(): void
    {
        Livewire::test(CreateProfile::class)
            ->assertFormSet($this->userAdmin->only(['name', 'username', 'email', 'address', 'phone']));
    }

    /** @test */
    public function profile_menu_the_validation_should_be_dispatched(): void
    {
        $this->withExceptionHandling();
        $user = $this->createUser($this->data);
        $data = $user->only(['username', 'email', 'phone']);

        Livewire::test(CreateProfile::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasFormErrors(['email' => 'unique', 'phone' => 'unique']);
    }

    /** @test */
    public function profile_menu_can_update_profile(): void
    {
        Livewire::test(CreateProfile::class)
            ->fillForm($this->data)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.pages.dashboard'));

        $this->assertDatabaseHas(User::class, $this->data);
    }

    /** @test */
    public function profile_menu_can_upload_avatar(): void
    {
        Storage::fake($this->directory);
        $image = UploadedFile::fake()
            ->image('beatles.png');
        $image = ['avatar' => $image];
        $data = array_merge($this->data, $image);

        $res = Livewire::test(CreateProfile::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.pages.dashboard'));

        $image = $res->json();
        $image = $res->json()->payload['serverMemo']['data']['data']['avatar'][0];
        $this->deleteFile($image);

        $data = array_merge($data, ['avatar' => $image]);
        $this->assertDatabaseHas(User::class, $data);
    }

    /** @test */
    public function profile_menu_can_change_password(): void
    {
        $newPassword = 'abc@121212';
        $passwordData = [
            'current_password' => 'abc@123123',
            'new_password' => $newPassword,
            'new_password_confirmation' => $newPassword
        ];
        $data = array_merge($this->data, $passwordData);

        Livewire::test(CreateProfile::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasNoFormErrors()
            ->assertRedirect(route('filament.pages.dashboard'));

        $this->assertTrue(Hash::check($newPassword, $this->userAdmin->fresh()->getAuthPassword()));
    }

    /** @test */
    public function profile_menu_the_password_validation_rules_should_be_dispatched(): void
    {
        $passwordData = [
            'current_password' => 'abc@123123',
            'new_password' => 'abc123457',
            'new_password_confirmation' => 'abc@123'
        ];
        $data = array_merge($this->data, $passwordData);

        Livewire::test(CreateProfile::class)
            ->fillForm($data)
            ->call('create')
            ->assertHasFormErrors(['new_password']);
    }
}
